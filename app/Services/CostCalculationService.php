<?php

namespace App\Services;

class CostCalculationService
{
    /**
     * Calculate cost for OpenAI API calls
     */
    public function calculateOpenAICost(string $model, int $inputTokens, int $outputTokens): array
    {
        // OpenAI pricing per 1K tokens (as of 2024)
        $pricing = [
            'gpt-4' => ['input' => 0.03, 'output' => 0.06],
            'gpt-4o' => ['input' => 0.005, 'output' => 0.015],
            'gpt-4-turbo' => ['input' => 0.01, 'output' => 0.03],
            'gpt-3.5-turbo' => ['input' => 0.0015, 'output' => 0.002],
            'gpt-3.5-turbo-16k' => ['input' => 0.003, 'output' => 0.004],
        ];

        $modelPricing = $pricing[$model] ?? $pricing['gpt-3.5-turbo'];
        
        $inputCost = ($inputTokens / 1000) * $modelPricing['input'];
        $outputCost = ($outputTokens / 1000) * $modelPricing['output'];
        $totalCost = $inputCost + $outputCost;

        return [
            'cost' => $totalCost,
            'input_cost' => $inputCost,
            'output_cost' => $outputCost,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'currency' => 'USD',
        ];
    }

    /**
     * Calculate cost for Claude API calls
     */
    public function calculateClaudeCost(string $model, int $inputTokens, int $outputTokens): array
    {
        // Claude pricing per 1K tokens (as of 2024)
        $pricing = [
            'claude-3-5-sonnet-20241022' => ['input' => 0.003, 'output' => 0.015],
            'claude-3-opus-20240229' => ['input' => 0.015, 'output' => 0.075],
            'claude-3-sonnet-20240229' => ['input' => 0.003, 'output' => 0.015],
            'claude-3-haiku-20240307' => ['input' => 0.00025, 'output' => 0.00125],
        ];

        $modelPricing = $pricing[$model] ?? $pricing['claude-3-5-sonnet-20241022'];
        
        $inputCost = ($inputTokens / 1000) * $modelPricing['input'];
        $outputCost = ($outputTokens / 1000) * $modelPricing['output'];
        $totalCost = $inputCost + $outputCost;

        return [
            'cost' => $totalCost,
            'input_cost' => $inputCost,
            'output_cost' => $outputCost,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'currency' => 'USD',
        ];
    }

    /**
     * Calculate cost for Gemini API calls
     */
    public function calculateGeminiCost(string $model, int $inputTokens, int $outputTokens): array
    {
        // Gemini pricing per 1K tokens (as of 2024)
        $pricing = [
            'gemini-2.0-flash' => ['input' => 0.000075, 'output' => 0.0003],
            'gemini-1.5-pro' => ['input' => 0.00125, 'output' => 0.005],
            'gemini-1.5-flash' => ['input' => 0.000075, 'output' => 0.0003],
            'gemini-1.0-pro' => ['input' => 0.0005, 'output' => 0.0015],
        ];

        $modelPricing = $pricing[$model] ?? $pricing['gemini-2.0-flash'];
        
        $inputCost = ($inputTokens / 1000) * $modelPricing['input'];
        $outputCost = ($outputTokens / 1000) * $modelPricing['output'];
        $totalCost = $inputCost + $outputCost;

        return [
            'cost' => $totalCost,
            'input_cost' => $inputCost,
            'output_cost' => $outputCost,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'currency' => 'USD',
        ];
    }

    /**
     * Calculate cost for Cursor CLI (estimated)
     */
    public function calculateCursorCLICost(int $inputTokens, int $outputTokens): array
    {
        // Cursor CLI doesn't have public pricing, so we'll estimate based on similar models
        // Using GPT-4 pricing as a conservative estimate
        $inputCost = ($inputTokens / 1000) * 0.03;
        $outputCost = ($outputTokens / 1000) * 0.06;
        $totalCost = $inputCost + $outputCost;

        return [
            'cost' => $totalCost,
            'input_cost' => $inputCost,
            'output_cost' => $outputCost,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'currency' => 'USD',
        ];
    }

    /**
     * Estimate token count from text (rough approximation)
     */
    public function estimateTokens(string $text): int
    {
        // Rough estimation: 1 token ≈ 4 characters for English text
        // This is a simplified approximation
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Calculate cost for any provider
     */
    public function calculateCost(string $provider, string $model, int $inputTokens, int $outputTokens): array
    {
        return match ($provider) {
            'openai' => $this->calculateOpenAICost($model, $inputTokens, $outputTokens),
            'claude' => $this->calculateClaudeCost($model, $inputTokens, $outputTokens),
            'gemini' => $this->calculateGeminiCost($model, $inputTokens, $outputTokens),
            'cursor-cli' => $this->calculateCursorCLICost($inputTokens, $outputTokens),
            default => $this->calculateOpenAICost($model, $inputTokens, $outputTokens),
        };
    }

    /**
     * Format cost for display
     */
    public function formatCost(float $cost, string $currency = 'USD'): string
    {
        if ($cost < 0.001) {
            return '< $0.001 ' . $currency;
        }
        
        return '$' . number_format($cost, 6) . ' ' . $currency;
    }
}
