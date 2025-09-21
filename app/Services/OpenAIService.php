<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function __construct()
    {
        //
    }

    /**
     * Generate a complete Next.js project using OpenAI
     */
    public function generateWebsite(string $prompt): array
    {
        try {
            $systemPrompt = $this->buildSystemPrompt();
            $userPrompt = $this->buildUserPrompt($prompt);

            $client = \OpenAI::client(config('services.openai.api_key'));
            $response = $client->chat()->create([
                'model' => config('services.openai.model'),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt
                    ]
                ],
                'max_tokens' => (int) config('services.openai.max_tokens'),
                'temperature' => (float) config('services.openai.temperature'),
            ]);

            $content = $response->choices[0]->message->content;
            $tokensUsed = $response->usage->totalTokens;

            // Parse the JSON response
            $projectData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from OpenAI: ' . json_last_error_msg());
            }

            return [
                'project' => $projectData,
                'tokens_used' => $tokensUsed,
                'model' => config('services.openai.model'),
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI API Error: ' . $e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Failed to generate website: ' . $e->getMessage());
        }
    }

    /**
     * Build the system prompt for website generation
     */
    private function buildSystemPrompt(): string
    {
        return "You are a web developer. Generate a Next.js project as JSON with these exact keys where each value is a STRING (not an object): package.json, next.config.js, tsconfig.json, app/layout.tsx, app/page.tsx, app/globals.css. Each value must be a complete file content as a string. Return only valid JSON, no other text.";
    }

    /**
     * Build the user prompt with context
     */
    private function buildUserPrompt(string $prompt): string
    {
        return "Create a Next.js website for: {$prompt}";
    }

    /**
     * Check if OpenAI is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty(config('services.openai.api_key'));
    }

    /**
     * Get the current model being used
     */
    public function getModel(): string
    {
        return config('services.openai.model');
    }
}
