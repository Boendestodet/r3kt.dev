<?php

namespace App\Services;

use Anthropic\Client;
use Anthropic\Factory;
use Illuminate\Support\Facades\Log;

class ClaudeAIService
{
    public function __construct()
    {
        //
    }

    /**
     * Generate a complete project using Claude AI
     */
    public function generateWebsite(string $prompt, string $projectType = 'nextjs'): array
    {
        try {
            $systemPrompt = $this->buildSystemPrompt($projectType);
            $userPrompt = $this->buildUserPrompt($prompt, $projectType);

            $factory = new Factory();
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
                        'content' => $systemPrompt . "\n\n" . $userPrompt
                    ]
                ]
            ]);

            $content = $response->choices[0]->message->content ?? '';
            $tokensUsed = $response->usage->inputTokens + $response->usage->outputTokens;

            // Parse the JSON response
            $projectData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Claude: ' . json_last_error_msg());
            }

            return [
                'project' => $projectData,
                'tokens_used' => $tokensUsed,
                'model' => config('services.claude.model', 'claude-3-5-sonnet-20241022'),
            ];

        } catch (\Exception $e) {
            Log::error('Claude AI API Error: ' . $e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Failed to generate website with Claude: ' . $e->getMessage());
        }
    }

    /**
     * Build the system prompt for website generation
     */
    private function buildSystemPrompt(string $projectType = 'nextjs'): string
    {
        if ($projectType === 'vite') {
            return "You are a web developer. Generate a Vite + React + TypeScript project as JSON with these exact keys where each value is a STRING (not an object): index.html, src/main.tsx, src/App.tsx, src/App.css, src/index.css. Each value must be a complete file content as a string. DO NOT include configuration files like package.json, vite.config.ts, tsconfig.json, etc. - these are handled by the system. Focus only on the application code and UI components. Return only valid JSON, no other text.";
        } else {
            return "You are a web developer. Generate a Next.js project as JSON with these exact keys where each value is a STRING (not an object): app/layout.tsx, app/page.tsx, app/globals.css. Each value must be a complete file content as a string. DO NOT include configuration files like package.json, next.config.js, tsconfig.json, etc. - these are handled by the system. Focus only on the application code and UI components. Return only valid JSON, no other text.";
        }
    }

    /**
     * Build the user prompt with context
     */
    private function buildUserPrompt(string $prompt, string $projectType = 'nextjs'): string
    {
        if ($projectType === 'vite') {
            return "Create a Vite + React + TypeScript website for: {$prompt}";
        } else {
            return "Create a Next.js website for: {$prompt}";
        }
    }

    /**
     * Check if Claude AI is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty(config('services.claude.api_key'));
    }

    /**
     * Get the current model being used
     */
    public function getModel(): string
    {
        return config('services.claude.model', 'claude-3-sonnet-20240229');
    }
}
