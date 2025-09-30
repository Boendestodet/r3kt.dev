<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ChatConversation;
use App\Services\CursorAIService;
use App\Services\OpenAIService;
use App\Services\ClaudeAIService;
use App\Services\GeminiAIService;
use App\Services\ProjectContextService;
use App\Services\CostCalculationService;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Log;

class ChatService
{
    public function __construct(
        private CursorAIService $cursorAIService,
        private OpenAIService $openAIService,
        private ClaudeAIService $claudeAIService,
        private GeminiAIService $geminiAIService,
        private ProjectContextService $projectContextService,
        private CostCalculationService $costCalculationService,
        private BalanceService $balanceService
    ) {
        //
    }

    /**
     * Send a message to any AI provider and store the conversation
     */
    public function sendMessage(Project $project, string $message, string $provider = 'cursor-cli', string $chatId = null): array
    {
        try {
            // Get user from project
            $user = $project->user;
            
            // Get or create chat conversation with specific chat ID
            $conversation = $this->getOrCreateConversation($project, $chatId);
            
            // Add user message to database
            $conversation->addMessage('user', $message);
            
            // Get project context for better AI understanding
            $projectContext = $this->projectContextService->getProjectContext($project);
            $contextualMessage = $this->buildContextualMessage($message, $projectContext);
            
            // Estimate tokens for balance check (rough estimation)
            $estimatedInputTokens = $this->costCalculationService->estimateTokens($contextualMessage);
            $estimatedOutputTokens = 200; // Conservative estimate for response
            
            // Get the AI model for cost calculation
            $aiModel = $project->settings['ai_model'] ?? $project->model ?? 'gpt-3.5-turbo';
            
            // Check if user has sufficient balance
            if (!$this->balanceService->canAffordChat($user, $provider, $aiModel, $estimatedInputTokens, $estimatedOutputTokens)) {
                return [
                    'success' => false,
                    'error' => 'Insufficient balance for chat interaction. Please add credits to your account.',
                    'provider' => $provider,
                ];
            }
            
            // Send message to appropriate AI provider with context
            $response = $this->sendToAIProvider($provider, $contextualMessage, $project);
            
            if ($response['success']) {
                // Add AI response to database
                $conversation->addMessage('ai', $response['response']);
                
                // Track costs if token information is available
                if (isset($response['input_tokens']) && isset($response['output_tokens'])) {
                    $inputTokens = $response['input_tokens'];
                    $outputTokens = $response['output_tokens'];
                    
                    // Get the AI model for cost calculation
                    $aiModel = $project->settings['ai_model'] ?? $project->model ?? 'gpt-3.5-turbo';
                    
                    // Calculate cost
                    $costInfo = $this->costCalculationService->calculateCost(
                        $provider,
                        $aiModel,
                        $inputTokens,
                        $outputTokens
                    );
                    
                    // Deduct cost from user's balance
                    $balanceDeducted = $this->balanceService->deductChatCost(
                        $user,
                        $provider,
                        $aiModel,
                        $inputTokens,
                        $outputTokens
                    );
                    
                    if (!$balanceDeducted) {
                        Log::error('Failed to deduct balance for chat interaction', [
                            'user_id' => $user->id,
                            'project_id' => $project->id,
                            'cost' => $costInfo['cost'],
                            'provider' => $provider,
                        ]);
                        
                        // Still add the cost to conversation for tracking, but log the issue
                        $conversation->addCost(
                            $costInfo['cost'],
                            $inputTokens,
                            $outputTokens,
                            $costInfo['currency']
                        );
                        
                        return [
                            'success' => false,
                            'error' => 'Failed to process payment. Please try again.',
                            'provider' => $provider,
                        ];
                    }
                    
                    // Add cost to conversation
                    $conversation->addCost(
                        $costInfo['cost'],
                        $inputTokens,
                        $outputTokens,
                        $costInfo['currency']
                    );
                }
                
                return [
                    'success' => true,
                    'response' => $response['response'],
                    'provider' => $provider,
                    'chat_id' => $conversation->chat_id,
                    'cost' => $conversation->getFormattedCost(),
                    'tokens' => $conversation->total_tokens,
                    'balance_info' => $this->balanceService->getBalanceInfo($user),
                ];
            }
            
            return [
                'success' => false,
                'error' => $response['error'] ?? 'Failed to get response from AI',
                'provider' => $provider,
            ];
            
        } catch (\Exception $e) {
            Log::error('Chat service error', [
                'project_id' => $project->id,
                'provider' => $provider,
                'chat_id' => $chatId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Chat service error: ' . $e->getMessage(),
                'provider' => $provider,
            ];
        }
    }

    /**
     * Get conversation for a project by chat ID
     */
    public function getConversation(Project $project, string $chatId = null): ?ChatConversation
    {
        if ($chatId) {
            return ChatConversation::where('project_id', $project->id)
                ->where('chat_id', $chatId)
                ->first();
        }
        
        // If no chat ID provided, return the latest conversation
        return $project->chatConversations()->latest('last_activity')->first();
    }

    /**
     * Get all conversations for a project
     */
    public function getAllConversations(Project $project): \Illuminate\Database\Eloquent\Collection
    {
        return $project->chatConversations()->latest('last_activity')->get();
    }

    /**
     * Get chat status for a project
     */
    public function getStatus(Project $project): array
    {
        $conversation = $this->getConversation($project);
        
        return [
            'has_chat' => !is_null($conversation),
            'chat_id' => $conversation?->chat_id,
        ];
    }

    /**
     * Create a new chat session for a project
     */
    public function createSession(Project $project, string $provider = 'cursor-cli'): array
    {
        try {
            $chatId = $this->generateProviderSpecificChatId($provider);
            
            // Create conversation in database
            $conversation = ChatConversation::create([
                'project_id' => $project->id,
                'chat_id' => $chatId,
                'messages' => [],
                'last_activity' => now(),
            ]);
            
            // Store chat ID in project settings
            $this->storeChatIdInProject($project, $chatId);
            
            // Add welcome message
            $welcomeMessage = $this->getWelcomeMessage($provider, $project);
            $conversation->addMessage('ai', $welcomeMessage);
            
            return [
                'success' => true,
                'chat_id' => $chatId,
                'provider' => $provider,
                'message' => 'Chat session created successfully',
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to create chat session', [
                'project_id' => $project->id,
                'provider' => $provider,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'Failed to create chat session: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Build contextual message with project information
     */
    private function buildContextualMessage(string $userMessage, array $projectContext): string
    {
        $context = $this->projectContextService->formatContextForAI($projectContext);
        
        return "{$context}\n\n## User Question\n{$userMessage}\n\nPlease provide helpful guidance based on the project context above. Focus on the specific stack, current files, and recent development activity.";
    }

    /**
     * Send message to specific AI provider
     */
    private function sendToAIProvider(string $provider, string $message, Project $project): array
    {
        switch ($provider) {
            case 'cursor-cli':
                return $this->sendToCursorCLI($message, $project);
            case 'openai':
                return $this->sendToOpenAI($message, $project);
            case 'claude':
                return $this->sendToClaude($message, $project);
            case 'gemini':
                return $this->sendToGemini($message, $project);
            default:
                return [
                    'success' => false,
                    'error' => "Unsupported AI provider: {$provider}",
                ];
        }
    }

    /**
     * Send message to Cursor CLI
     */
    private function sendToCursorCLI(string $message, Project $project): array
    {
        try {
            // Use Cursor CLI's conversational method (same pattern as others)
            $result = $this->cursorAIService->sendConversationalMessage($message, $project->name);
            
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send message to OpenAI
     */
    private function sendToOpenAI(string $message, Project $project): array
    {
        try {
            // Use OpenAI's conversational method
            $result = $this->openAIService->sendConversationalMessage($message, $project->name);
            
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send message to Claude
     */
    private function sendToClaude(string $message, Project $project): array
    {
        try {
            // Use Claude's conversational method
            $result = $this->claudeAIService->sendConversationalMessage($message, $project->name);
            
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send message to Gemini
     */
    private function sendToGemini(string $message, Project $project): array
    {
        try {
            // Use Gemini's conversational method
            $result = $this->geminiAIService->sendConversationalMessage($message, $project->name);
            
            return $result;
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract conversational response from AI project data
     */
    private function extractConversationalResponse(array $projectData): string
    {
        // Try to extract meaningful conversation from project data
        if (isset($projectData['description']) && !empty($projectData['description'])) {
            return $projectData['description'];
        }
        
        if (isset($projectData['content']) && !empty($projectData['content'])) {
            return $projectData['content'];
        }
        
        if (isset($projectData['name']) && !empty($projectData['name'])) {
            return "I'll help you build: {$projectData['name']}. Let me break this down into manageable steps.";
        }
        
        // Check if there are files with descriptions
        if (isset($projectData['files']) && is_array($projectData['files'])) {
            $fileCount = count($projectData['files']);
            return "I'll create {$fileCount} files for your project. Let me explain what each file will do and how they work together.";
        }
        
        // Fallback to a generic response
        return "I understand your request. I can help you build this feature step by step. What specific aspects would you like to focus on first?";
    }

    /**
     * Get welcome message for different providers
     */
    private function getWelcomeMessage(string $provider, Project $project): string
    {
        $providerNames = [
            'cursor-cli' => 'Cursor CLI',
            'openai' => 'OpenAI GPT',
            'claude' => 'Claude AI',
            'gemini' => 'Gemini AI',
        ];
        
        $providerName = $providerNames[$provider] ?? 'AI Assistant';
        
        return "Hello! I'm your {$providerName} assistant. I'm here to help you build your {$project->name} project. What would you like to create today?";
    }

    /**
     * Generate provider-specific chat ID
     */
    private function generateProviderSpecificChatId(string $provider): string
    {
        $timestamp = time();
        $uniqueId = uniqid();
        
        switch ($provider) {
            case 'cursor-cli':
                return "cursor-chat-{$uniqueId}-{$timestamp}";
            case 'openai':
                return "openai-chat-{$uniqueId}-{$timestamp}";
            case 'claude':
                return "claude-chat-{$uniqueId}-{$timestamp}";
            case 'gemini':
                return "gemini-chat-{$uniqueId}-{$timestamp}";
            default:
                return "chat-{$provider}-{$uniqueId}-{$timestamp}";
        }
    }

    /**
     * Generate a unique chat ID
     */
    private function generateChatId(): string
    {
        return 'chat-' . uniqid() . '-' . time();
    }

    /**
     * Get or create conversation for project with specific chat ID
     */
    private function getOrCreateConversation(Project $project, string $chatId = null): ChatConversation
    {
        if ($chatId) {
            // Try to find existing conversation with this chat ID
            $conversation = ChatConversation::where('project_id', $project->id)
                ->where('chat_id', $chatId)
                ->first();
                
            if ($conversation) {
                return $conversation;
            }
        } else {
            // If no chat ID provided, try to find the latest conversation for this project
            $conversation = $this->getConversation($project);
            if ($conversation) {
                return $conversation;
            }
        }
        
        // Create new conversation only if none exists
        $newChatId = $chatId ?: $this->generateProviderSpecificChatId('generic');
        $conversation = ChatConversation::create([
            'project_id' => $project->id,
            'chat_id' => $newChatId,
            'messages' => [],
            'last_activity' => now(),
        ]);
        
        $this->storeChatIdInProject($project, $newChatId);
        
        return $conversation;
    }

    /**
     * Store chat ID in project settings
     */
    private function storeChatIdInProject(Project $project, string $chatId): void
    {
        $settings = $project->settings ?? [];
        $settings['chat_id'] = $chatId;  // Store as 'chat_id' not 'cursor_chat_id'
        $project->update(['settings' => $settings]);
    }
}