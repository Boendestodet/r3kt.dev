<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use OpenAI;

class OpenAIService
{
    public function __construct(
        private StackControllerFactory $stackFactory
    ) {
        //
    }

    /**
     * Generate a complete project using OpenAI
     */
    public function generateWebsite(string $prompt, string $projectType = 'nextjs'): array
    {
        try {
            $stackController = $this->stackFactory->getControllerByType($projectType);
            $systemPrompt = $stackController->getSystemPrompt();
            $userPrompt = $stackController->getUserPrompt($prompt);

            $client = \OpenAI::client(config('services.openai.api_key'));
            $response = $client->chat()->create([
                'model' => config('services.openai.model'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
                'max_tokens' => (int) config('services.openai.max_tokens'),
                'temperature' => (float) config('services.openai.temperature'),
            ]);

            $content = $response->choices[0]->message->content;
            $tokensUsed = $response->usage->totalTokens;

            // Parse the JSON response
            $projectData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from OpenAI: '.json_last_error_msg());
            }

            return [
                'project' => $projectData,
                'tokens_used' => $tokensUsed,
                'model' => config('services.openai.model'),
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI API Error: '.$e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to generate website: '.$e->getMessage());
        }
    }

    /**
     * Check if OpenAI is properly configured
     */
    public function isConfigured(): bool
    {
        return ! empty(config('services.openai.api_key'));
    }

    /**
     * Get the current model being used
     */
    public function getModel(): string
    {
        return config('services.openai.model');
    }

    /**
     * Send a conversational message to OpenAI
     */
    public function sendConversationalMessage(string $message, string $projectName = ''): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'error' => 'OpenAI API is not configured',
                ];
            }

            $client = \OpenAI::client(config('services.openai.api_key'));

            // The message now includes project context from ChatService
            $response = $client->chat()->create([
                'model' => $this->getModel(),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $message,
                    ],
                ],
                'max_tokens' => 1500,
                'temperature' => 0.7,
            ]);

            $content = $response->choices[0]->message->content ?? 'I received your message and I\'m here to help!';

            // Extract token usage if available
            $inputTokens = $response->usage->promptTokens ?? 0;
            $outputTokens = $response->usage->completionTokens ?? 0;

            return [
                'success' => true,
                'response' => $content,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
