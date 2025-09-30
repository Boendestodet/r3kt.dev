<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ChatConversation;
use App\Services\CursorAIService;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    public function __construct(
        private CursorAIService $cursorService,
        private ChatService $chatService
    ) {
        //
    }

    /**
     * Get chat conversation for a project
     */
    public function getConversation(Request $request, Project $project): JsonResponse
    {
        $chatId = $request->query('chat_id');
        $conversation = $this->chatService->getConversation($project, $chatId);
        
        if (!$conversation) {
            return response()->json([
                'success' => false,
                'error' => 'No chat session found for this project',
            ], 404);
        }

        // Get current user balance info
        $balanceService = app(\App\Services\BalanceService::class);
        $balanceInfo = $balanceService->getBalanceInfo($project->user);
        
        return response()->json([
            'success' => true,
            'chat_id' => $conversation->chat_id,
            'messages' => $conversation->messages,
            'last_activity' => $conversation->last_activity?->toISOString(),
            'cost_info' => [
                'total_cost' => $conversation->total_cost,
                'total_tokens' => $conversation->total_tokens,
                'input_tokens' => $conversation->input_tokens,
                'output_tokens' => $conversation->output_tokens,
                'cost_currency' => $conversation->cost_currency,
                'formatted_cost' => $conversation->getFormattedCost(),
            ],
            'balance_info' => $balanceInfo,
        ]);
    }

    /**
     * Get all conversations for a project
     */
    public function getAllConversations(Project $project): JsonResponse
    {
        $conversations = $this->chatService->getAllConversations($project);
        
        return response()->json([
            'success' => true,
            'conversations' => $conversations->map(function ($conversation) {
                return [
                    'chat_id' => $conversation->chat_id,
                    'message_count' => count($conversation->messages ?? []),
                    'last_activity' => $conversation->last_activity?->toISOString(),
                    'created_at' => $conversation->created_at?->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * Parse conversation to extract meaningful messages for display
     */
    private function parseConversationForDisplay(string $conversation): array
    {
        $messages = [];
        
        try {
            // Split by JSON objects
            $jsonObjects = preg_split('/\}(?=\s*\{)/', $conversation);
            
            foreach ($jsonObjects as $jsonStr) {
                $jsonStr = trim($jsonStr);
                if (!str_starts_with($jsonStr, '{')) {
                    $jsonStr = '{' . $jsonStr;
                }
                if (!str_ends_with($jsonStr, '}')) {
                    $jsonStr = $jsonStr . '}';
                }
                
                try {
                    $parsed = json_decode($jsonStr, true);
                    
                    if (isset($parsed['type']) && isset($parsed['message']['content'])) {
                        $role = $parsed['type'] === 'assistant' ? 'ai' : 'user';
                        $content = is_array($parsed['message']['content']) 
                            ? implode('', array_column($parsed['message']['content'], 'text'))
                            : $parsed['message']['content'];
                        
                        if (!empty(trim($content))) {
                            $messages[] = [
                                'role' => $role,
                                'content' => $this->extractMeaningfulContent($content),
                                'timestamp' => now()->toISOString(),
                            ];
                        }
                    }
                } catch (\Exception $e) {
                    // Skip invalid JSON
                    continue;
                }
            }
        } catch (\Exception $e) {
            // Fallback: return empty array
        }
        
        return $messages;
    }

    /**
     * Extract meaningful content from AI responses
     */
    private function extractMeaningfulContent(string $content): string
    {
        // Remove JSON code blocks
        $content = preg_replace('/```json[\s\S]*?```/', '', $content);
        $content = preg_replace('/```[\s\S]*?```/', '', $content);
        
        // Remove file paths
        $content = preg_replace('/[^\s]*\.(tsx?|jsx?|css|json|html|vue|svelte|astro)[^\s]*/', '', $content);
        
        // Clean up whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        $content = trim($content);
        
        // Extract action descriptions
        $actionPatterns = [
            '/I\'ll\s+([^.!?]+[.!?])/i',
            '/Let me\s+([^.!?]+[.!?])/i',
            '/I\'m going to\s+([^.!?]+[.!?])/i',
            '/I\'ll create\s+([^.!?]+[.!?])/i',
            '/I\'ll add\s+([^.!?]+[.!?])/i',
            '/I\'ll update\s+([^.!?]+[.!?])/i',
        ];
        
        $extractedActions = '';
        foreach ($actionPatterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                $extractedActions .= implode(' ', $matches[0]) . ' ';
            }
        }
        
        if (!empty(trim($extractedActions))) {
            return trim($extractedActions);
        }
        
        // If content is too technical, provide a summary
        if (strlen($content) > 200 && str_contains($content, '{') && str_contains($content, '}')) {
            return 'I\'ve processed your request and made the necessary changes to your project.';
        }
        
        return $content ?: 'I\'ve completed the requested changes.';
    }

    /**
     * Send a message to the project's chat
     */
    public function sendMessage(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'provider' => 'sometimes|string|in:cursor-cli,openai,claude,gemini',
            'chat_id' => 'sometimes|string',
        ]);

        $provider = $request->input('provider', 'cursor-cli');
        $chatId = $request->input('chat_id');
        $response = $this->chatService->sendMessage($project, $request->message, $provider, $chatId);
        
        return response()->json($response);
    }

    /**
     * Create a new chat session for a project
     */
    public function createSession(Request $request, Project $project): JsonResponse
    {
        $provider = $request->input('provider', 'cursor-cli');
        $response = $this->chatService->createSession($project, $provider);
        
        return response()->json($response);
    }

    /**
     * Get chat status for a project
     */
    public function getStatus(Project $project): JsonResponse
    {
        $conversation = $this->chatService->getConversation($project);
        
        return response()->json([
            'has_chat' => !is_null($conversation),
            'chat_id' => $conversation?->chat_id,
        ]);
    }
}