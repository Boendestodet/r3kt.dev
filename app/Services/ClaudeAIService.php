<?php

namespace App\Services;

use Anthropic\Factory;
use Illuminate\Support\Facades\Log;

class ClaudeAIService
{
    public function __construct(
        private StackControllerFactory $stackFactory
    ) {
        //
    }

    /**
     * Generate a complete project using Claude AI
     */
    public function generateWebsite(string $prompt, string $projectType = 'nextjs'): array
    {
        try {
            $stackController = $this->stackFactory->getControllerByType($projectType);
            $systemPrompt = $stackController->getSystemPrompt();
            $userPrompt = $stackController->getUserPrompt($prompt);

            $factory = new Factory;
            $factory->withApiKey(config('services.claude.api_key'));
            $factory->withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => config('services.claude.api_key'),
                'anthropic-version' => '2023-06-01',
            ]);
            $client = $factory->make();

            $response = $client->chat()->create([
                'model' => config('services.claude.model', 'claude-3-5-sonnet-20241022'),
                'max_tokens' => (int) config('services.claude.max_tokens', 4000),
                'temperature' => (float) config('services.claude.temperature', 0.7),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $systemPrompt."\n\n".$userPrompt,
                    ],
                ],
            ]);

            $content = $response->choices[0]->message->content ?? '';
            $tokensUsed = $response->usage->inputTokens + $response->usage->outputTokens;

            // Parse the JSON response
            $projectData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Claude: '.json_last_error_msg());
            }

            return [
                'project' => $projectData,
                'tokens_used' => $tokensUsed,
                'model' => config('services.claude.model', 'claude-3-5-sonnet-20241022'),
            ];

        } catch (\Exception $e) {
            Log::error('Claude AI API Error: '.$e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to generate website with Claude: '.$e->getMessage());
        }
    }

    /**
     * Check if Claude AI is properly configured
     */
    public function isConfigured(): bool
    {
        return ! empty(config('services.claude.api_key'));
    }

    /**
     * Get the current model being used
     */
    public function getModel(): string
    {
        return config('services.claude.model', 'claude-3-sonnet-20240229');
    }

    /**
     * Send a conversational message to Claude
     */
    public function sendConversationalMessage(string $message, string $projectName = ''): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'error' => 'Claude API is not configured',
                ];
            }

            $factory = new Factory;
            $factory->withApiKey(config('services.claude.api_key'));
            $factory->withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => config('services.claude.api_key'),
                'anthropic-version' => '2023-06-01',
            ]);
            $client = $factory->make();

            // The message now includes project context from ChatService
            $response = $client->chat()->create([
                'model' => config('services.claude.model', 'claude-3-5-sonnet-20241022'),
                'max_tokens' => 1500,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $message,
                    ],
                ],
            ]);

            $content = $response->choices[0]->message->content ?? 'I received your message and I\'m here to help!';

            // Extract token usage if available
            $inputTokens = $response->usage->inputTokens ?? 0;
            $outputTokens = $response->usage->outputTokens ?? 0;

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
