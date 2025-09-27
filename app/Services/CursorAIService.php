<?php

namespace App\Services;

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

            // Parse the JSON response
            $projectData = json_decode($result['output'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                // If JSON parsing fails, try to extract JSON from the output
                $projectData = $this->extractJsonFromOutput($result['output']);

                if (! $projectData) {
                    throw new \Exception('Invalid JSON response from Cursor CLI: '.json_last_error_msg());
                }
            }

            return [
                'project' => $projectData,
                'tokens_used' => $this->estimateTokensUsed($combinedPrompt, $result['output']),
                'model' => 'cursor-cli',
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
     * Execute Cursor CLI command with the given prompt
     */
    private function executeCursorCommand(string $prompt, string $projectType): array
    {
        try {
            // Add more specific instructions for JSON output
            $enhancedPrompt = $prompt . "\n\nIMPORTANT: You must respond with ONLY valid JSON. Do not include any explanations, descriptions, or additional text. Start your response with { and end with }. The JSON should contain the exact file contents as strings.";
            
            // Execute cursor-agent command with the enhanced prompt
            $process = Process::run([
                'cursor-agent',
                '--print',
                '--output-format', 'json',
                'agent',
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
     * Extract JSON from Cursor CLI output that might contain additional text
     */
    private function extractJsonFromOutput(string $output): ?array
    {
        // First, try to parse the entire output as JSON (in case it's already clean)
        $decoded = json_decode($output, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
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
}
