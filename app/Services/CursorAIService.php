<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ChatConversation;
use App\Services\StackControllerFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CursorAIService
{
    public function __construct(
        private StackControllerFactory $stackFactory
    ) {
        //
    }

    /**
     * Generate a complete project using Cursor CLI
     */
    public function generateWebsite(string $prompt, string $projectType = 'nextjs'): array
    {
        try {
            $stackController = $this->stackFactory->getControllerByType($projectType);
            $systemPrompt = $stackController->getSystemPrompt();
            $userPrompt = $stackController->getUserPrompt($prompt);

            // Check if Cursor CLI is installed and available
            if (! $this->isCursorCliAvailable()) {
                throw new \Exception('Cursor CLI is not installed or not available in PATH');
            }

            // Create a combined prompt for Cursor CLI
            $combinedPrompt = $systemPrompt."\n\n".$userPrompt;

            // Use Cursor CLI to generate the website
            $result = $this->executeCursorCommand($combinedPrompt, $projectType);

            if (! $result['success']) {
                throw new \Exception('Cursor CLI execution failed: '.$result['error']);
            }

            // Parse the chat conversation response
            $projectData = $this->extractProjectDataFromChatResponse($result['output'], $projectType);

            if (! $projectData) {
                throw new \Exception('Could not extract project data from Cursor CLI response');
            }

            return [
                'project' => $projectData,
                'tokens_used' => $this->estimateTokensUsed($combinedPrompt, $result['output']),
                'model' => 'cursor-cli',
                'chat_id' => $result['chat_id'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Cursor CLI Error: '.$e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to generate website with Cursor CLI: '.$e->getMessage());
        }
    }

    /**
     * Execute Cursor CLI command with the given prompt using chat sessions
     */
    private function executeCursorCommand(string $prompt, string $projectType): array
    {
        try {
            // Create a chat session for better context and conversation flow
            $chatId = $this->createChatSession();
            
            if (!$chatId) {
                throw new \Exception('Failed to create Cursor chat session');
            }

            // Enhanced prompt for chat context
            $enhancedPrompt = $prompt . "\n\nPlease create a complete {$projectType} project with all necessary files. Generate the files and provide them in a structured format that I can use to build the project.";
            
            // Execute cursor-agent command with chat session
            $process = Process::timeout(180)->run([
                'cursor-agent',
                '--resume', $chatId,
                '--print',
                '--output-format', 'json',
                $enhancedPrompt,
            ]);

            if ($process->failed()) {
                return [
                    'success' => false,
                    'error' => $process->errorOutput() ?: 'Cursor CLI command failed',
                    'output' => null,
                ];
            }

            return [
                'success' => true,
                'error' => null,
                'output' => $process->output(),
                'chat_id' => $chatId,
            ];

        } catch (ProcessFailedException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'output' => null,
            ];
        }
    }

    /**
     * Create a new Cursor chat session
     */
    public function createChatSession(): ?string
    {
        try {
            $process = Process::timeout(30)->run(['cursor-agent', 'create-chat']);

            if ($process->successful()) {
                $chatId = trim($process->output());
                Log::info('Created Cursor chat session', ['chat_id' => $chatId]);
                return $chatId;
            }

            Log::warning('Failed to create Cursor chat session', [
                'error' => $process->errorOutput(),
                'output' => $process->output(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception creating Cursor chat session', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if Cursor CLI is available
     */
    public function isCursorCliAvailable(): bool
    {
        try {
            $process = Process::run(['which', 'cursor-agent']);

            return $process->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if Cursor CLI is properly configured
     */
    public function isConfigured(): bool
    {
        return $this->isCursorCliAvailable();
    }

    /**
     * Get the current model being used
     */
    public function getModel(): string
    {
        return 'cursor-cli';
    }

    /**
     * Extract project data from Cursor CLI chat response
     * For chat-based approach, we'll return a simple structure and let the sandbox handle the chat
     */
    private function extractProjectDataFromChatResponse(string $output, string $projectType): ?array
    {
        // For chat-based approach, we don't need to parse complex output
        // Just return a basic project structure and let the sandbox show the chat
        Log::info('Using chat-based approach, returning basic project structure');
        
        return $this->createBasicProjectFromText($output, $projectType);
    }

    /**
     * Get chat conversation for a project
     */
    public function getChatConversation(string $chatId): array
    {
        try {
            // Get the chat conversation using Cursor CLI
            $process = Process::timeout(60)->run([
                'cursor-agent',
                '--resume', $chatId,
                '--print',
                '--output-format', 'json',
                'Show me the conversation history',
            ]);

            if ($process->successful()) {
                $output = $process->output();
                return [
                    'success' => true,
                    'conversation' => $output,
                    'chat_id' => $chatId,
                ];
            }

            return [
                'success' => false,
                'error' => $process->errorOutput() ?: 'Failed to get conversation',
                'chat_id' => $chatId,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
            ];
        }
    }

    /**
     * Send a message to the project's chat
     */
    public function sendChatMessage(string $chatId, string $message): array
    {
        try {
            $process = Process::timeout(120)->run([
                'cursor-agent',
                '--resume', $chatId,
                '--print',
                '--output-format', 'json',
                $message,
            ]);

            if ($process->successful()) {
                return [
                    'success' => true,
                    'response' => $process->output(),
                    'chat_id' => $chatId,
                ];
            }

            return [
                'success' => false,
                'error' => $process->errorOutput() ?: 'Failed to send message',
                'chat_id' => $chatId,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
            ];
        }
    }

    /**
     * Store chat ID in project settings
     */
    public function storeChatIdInProject(\App\Models\Project $project, string $chatId): void
    {
        $settings = $project->settings ?? [];
        $settings['cursor_chat_id'] = $chatId;
        $project->update(['settings' => $settings]);
        
        Log::info('Stored Cursor chat ID in project', [
            'project_id' => $project->id,
            'chat_id' => $chatId,
        ]);
    }

    /**
     * Get chat ID from project settings
     */
    public function getChatIdFromProject(\App\Models\Project $project): ?string
    {
        return $project->settings['cursor_chat_id'] ?? null;
    }

    /**
     * Create or get chat conversation in database
     */
    public function getOrCreateChatConversation(Project $project, string $chatId): ChatConversation
    {
        return ChatConversation::firstOrCreate(
            ['chat_id' => $chatId],
            [
                'project_id' => $project->id,
                'chat_id' => $chatId,
                'messages' => [],
                'last_activity' => now(),
            ]
        );
    }

    /**
     * Add message to chat conversation in database
     */
    public function addMessageToConversation(Project $project, string $chatId, string $role, string $content): void
    {
        $conversation = $this->getOrCreateChatConversation($project, $chatId);
        $conversation->addMessage($role, $content);
    }

    /**
     * Update raw conversation in database
     */
    public function updateConversationRawData(Project $project, string $chatId, string $rawConversation): void
    {
        $conversation = $this->getOrCreateChatConversation($project, $chatId);
        $conversation->updateRawConversation($rawConversation);
    }

    /**
     * Get chat conversation from database
     */
    public function getChatConversationFromDatabase(Project $project, string $chatId): ?ChatConversation
    {
        return ChatConversation::where('project_id', $project->id)
            ->where('chat_id', $chatId)
            ->first();
    }

    /**
     * Extract project data from Cursor CLI response (legacy JSON method)
     */
    private function extractProjectDataFromResponse(array $responseData): ?array
    {
        // Check if it's already in the expected format
        if (isset($responseData['result']) && is_array($responseData['result'])) {
            return $responseData['result'];
        }

        // If result is a string, try to extract JSON from it
        if (isset($responseData['result']) && is_string($responseData['result'])) {
            $resultContent = $responseData['result'];
            
            // Try to find JSON in the result content
            $jsonPatterns = [
                '/\{.*\}/s',  // Any JSON object
                '/```json\s*(\{.*?\})\s*```/s',  // JSON in code blocks
            ];
            
            foreach ($jsonPatterns as $pattern) {
                if (preg_match($pattern, $resultContent, $matches)) {
                    $jsonString = $matches[1] ?? $matches[0];
                    $jsonString = trim($jsonString);
                    
                    $decoded = json_decode($jsonString, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return $decoded;
                    }
                }
            }
            
            // If no JSON found, create a basic project structure from the text
            return $this->createBasicProjectFromText($resultContent, $projectType);
        }

        // If it's already the file contents JSON, return it
        if (is_array($responseData) && !isset($responseData['type'])) {
            return $responseData;
        }

        return null;
    }

    /**
     * Create a basic project structure from text content
     * This should only return raw content, not stack-specific files
     */
    private function createBasicProjectFromText(string $content, string $projectType = 'nextjs'): array
    {
        // Just return the raw content - let the stack controllers handle file creation
        return [
            'content' => $content,
            'type' => $projectType,
            'generated_by' => 'cursor_cli_fallback'
        ];
    }

    /**
     * Extract JSON from Cursor CLI output that might contain additional text
     */
    private function extractJsonFromOutput(string $output): ?array
    {
        // First, try to parse the entire output as JSON (in case it's already clean)
        $decoded = json_decode($output, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            // If it's a Cursor CLI response with a result field, extract the result
            if (isset($decoded['result']) && is_string($decoded['result'])) {
                // The result field contains the actual content - try to parse it as JSON
                $resultContent = $decoded['result'];
                
                // Try to find JSON within the result content
                $jsonPatterns = [
                    '/\{.*\}/s',  // Any JSON object
                    '/```json\s*(\{.*?\})\s*```/s',  // JSON in code blocks
                ];
                
                foreach ($jsonPatterns as $pattern) {
                    if (preg_match($pattern, $resultContent, $matches)) {
                        $jsonString = $matches[1] ?? $matches[0];
                        $jsonString = trim($jsonString);
                        $jsonString = stripslashes($jsonString);
                        
                        $jsonDecoded = json_decode($jsonString, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonDecoded)) {
                            return $jsonDecoded;
                        }
                    }
                }
                
                // If no JSON found in result, return null (Cursor CLI didn't follow instructions)
                return null;
            }
            
            // If it's already the file contents JSON, return it
            return $decoded;
        }
        
        // Try to find JSON in the output using multiple patterns
        $patterns = [
            '/\{.*\}/s',  // Any JSON object
            '/"result":\s*"([^"]+)"/',  // Extract from result field
            '/```json\s*(\{.*?\})\s*```/s',  // JSON in code blocks
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $output, $matches)) {
                $jsonString = $matches[1] ?? $matches[0];
                
                // Clean up the JSON string
                $jsonString = trim($jsonString);
                $jsonString = stripslashes($jsonString);
                
                $decoded = json_decode($jsonString, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
            }
        }
        
        // If no JSON found, try to extract from the result field specifically
        if (preg_match('/"result":\s*"([^"]+)"/', $output, $matches)) {
            $result = $matches[1];
            // Unescape the JSON string
            $result = str_replace('\\"', '"', $result);
            $result = str_replace('\\n', "\n", $result);
            $result = str_replace('\\/', '/', $result);
            $decoded = json_decode($result, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        
        // Try to extract JSON from code blocks in the result
        if (preg_match('/```json\s*(\{.*?\})\s*```/s', $output, $matches)) {
            $jsonString = $matches[1];
            $decoded = json_decode($jsonString, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        
        return null;
    }

    /**
     * Estimate tokens used based on input and output length
     */
    private function estimateTokensUsed(string $input, string $output): int
    {
        // Rough estimation: 1 token ≈ 4 characters
        return (int) ceil((strlen($input) + strlen($output)) / 4);
    }

    /**
     * Install Cursor CLI if not available
     */
    public function installCursorCli(): array
    {
        try {
            Log::info('Installing Cursor CLI...');

            $process = Process::run([
                'bash', '-c',
                'curl https://cursor.com/install -fsS | bash',
            ]);

            if ($process->failed()) {
                return [
                    'success' => false,
                    'message' => 'Failed to install Cursor CLI: '.$process->errorOutput(),
                ];
            }

            return [
                'success' => true,
                'message' => 'Cursor CLI installed successfully',
            ];

        } catch (\Exception $e) {
            Log::error('Cursor CLI installation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Installation failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Send a conversational message to Cursor CLI
     */
    public function sendConversationalMessage(string $message, string $projectName = ''): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'error' => 'Cursor CLI is not configured',
                ];
            }

            // Extract the user's actual question from the contextual message
            $userQuestion = $this->extractUserQuestion($message);
            
            // Cursor CLI is designed for code generation, not conversational chat
            // Return a helpful conversational response based on the project context
            $response = "I can see you're working on your project and asking: '{$userQuestion}'. Based on your project context, I can help you with code generation and implementation. Cursor CLI excels at creating code, so if you need specific files, components, or functionality added to your project, I can generate that for you. What specific code or feature would you like me to help you implement?";

            // Estimate tokens for Cursor CLI (since it doesn't provide exact counts)
            $estimatedInputTokens = $this->estimateTokens($message);
            $estimatedOutputTokens = $this->estimateTokens($response);

            return [
                'success' => true,
                'response' => $response,
                'input_tokens' => $estimatedInputTokens,
                'output_tokens' => $estimatedOutputTokens,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extract the user's actual question from the contextual message
     */
    private function extractUserQuestion(string $contextualMessage): string
    {
        // Look for the "## User Question" section
        if (preg_match('/## User Question\s*\n(.+?)(?:\n\n|$)/s', $contextualMessage, $matches)) {
            return trim($matches[1]);
        }
        
        // Fallback: return the last part of the message
        $lines = explode("\n", $contextualMessage);
        $lastLine = end($lines);
        return trim($lastLine) ?: 'your project';
    }

    /**
     * Estimate token count from text (rough approximation)
     */
    private function estimateTokens(string $text): int
    {
        // Rough estimation: 1 token ≈ 4 characters for English text
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Extract conversational response from Cursor CLI raw output
     */
    private function extractConversationalResponseFromOutput(string $output): string
    {
        // Clean up the output and extract meaningful content
        $output = trim($output);
        
        // If output is empty, return a default response
        if (empty($output)) {
            return "I understand your request. I can help you with that! What specific aspects would you like to focus on first?";
        }
        
        // Remove JSON structures and session IDs
        $output = preg_replace('/\{[\s\S]*?\}/', '', $output);
        $output = preg_replace('/"session_id":"[^"]*"/', '', $output);
        $output = preg_replace('/```[\s\S]*?```/', '', $output);
        $output = preg_replace('/\[[\s\S]*?\]/', '', $output);
        
        // Clean up extra whitespace and special characters
        $output = preg_replace('/\s+/', ' ', $output);
        $output = preg_replace('/[^\w\s.,!?\-]/', '', $output);
        $output = trim($output);
        
        // If we still have meaningful content, return it
        if (strlen($output) > 20) {
            return $output;
        }
        
        // Fallback response
        return "I understand your request. I can help you with that! What specific aspects would you like to focus on first?";
    }

    /**
     * Extract conversational response from Cursor CLI project data
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
            return "I'll help you with: {$projectData['name']}. Let me break this down into manageable steps.";
        }
        
        // Check if there are files with descriptions
        if (isset($projectData['files']) && is_array($projectData['files'])) {
            $fileCount = count($projectData['files']);
            return "I'll create {$fileCount} files for your project. Let me explain what each file will do and how they work together.";
        }
        
        // Fallback to a conversational response
        return "I understand your request. I can help you with that! What specific aspects would you like to focus on first?";
    }
}
