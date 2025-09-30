<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    public function __construct(
        private StackControllerFactory $stackFactory
    ) {
        //
    }

    /**
     * Generate a complete project using Gemini AI
     */
    public function generateWebsite(string $prompt, string $projectType = 'nextjs'): array
    {
        try {
            $stackController = $this->stackFactory->getControllerByType($projectType);
            $systemPrompt = $stackController->getSystemPrompt();
            $userPrompt = $stackController->getUserPrompt($prompt);

            // Combine system and user prompts for Gemini
            $combinedPrompt = $systemPrompt."\n\n".$userPrompt;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/'.config('services.gemini.model').':generateContent?key='.config('services.gemini.api_key'), [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $combinedPrompt,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => (float) config('services.gemini.temperature'),
                    'maxOutputTokens' => (int) config('services.gemini.max_tokens'),
                ],
            ]);

            if (! $response->successful()) {
                throw new \Exception('Gemini API request failed: '.$response->body());
            }

            $data = $response->json();

            if (! isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                throw new \Exception('Invalid response structure from Gemini API');
            }

            $content = $data['candidates'][0]['content']['parts'][0]['text'];

            // Estimate tokens used (Gemini doesn't provide exact count in response)
            $tokensUsed = $this->estimateTokens($combinedPrompt.$content);

            // Parse the JSON response
            $projectData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response from Gemini: '.json_last_error_msg());
            }

            return [
                'project' => $projectData,
                'tokens_used' => $tokensUsed,
                'model' => config('services.gemini.model'),
            ];

        } catch (\Exception $e) {
            Log::error('Gemini AI API Error: '.$e->getMessage(), [
                'prompt' => $prompt,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to generate website with Gemini: '.$e->getMessage());
        }
    }

    /**
     * Check if Gemini AI is properly configured
     */
    public function isConfigured(): bool
    {
        return ! empty(config('services.gemini.api_key'));
    }

    /**
     * Get the current model being used
     */
    public function getModel(): string
    {
        return config('services.gemini.model', 'gemini-1.5-pro');
    }

    /**
     * Estimate token count (rough approximation)
     * Gemini doesn't provide exact token count in response
     */
    private function estimateTokens(string $text): int
    {
        // Rough estimation: 1 token ≈ 4 characters for English text
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Send a conversational message to Gemini
     */
    public function sendConversationalMessage(string $message, string $projectName = ''): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'error' => 'Gemini API is not configured',
                ];
            }

            // The message now includes project context from ChatService
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/'.config('services.gemini.model').':generateContent?key='.config('services.gemini.api_key'), [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $message,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1500,
                ],
            ]);

            if (!$response->successful()) {
                throw new \Exception('Gemini API request failed: '.$response->body());
            }

            $data = $response->json();
            $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'I received your message and I\'m here to help!';

            // Extract token usage if available
            $usageMetadata = $data['usageMetadata'] ?? [];
            $inputTokens = $usageMetadata['promptTokenCount'] ?? 0;
            $outputTokens = $usageMetadata['candidatesTokenCount'] ?? 0;

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
