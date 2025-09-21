<?php

namespace App\Services;

class AICostEstimator
{
    /**
     * Estimate the cost of generating a website with different AI providers
     */
    public function estimateCost(string $prompt, string $provider = 'openai'): array
    {
        $promptTokens = $this->estimateTokens($prompt);
        $estimatedResponseTokens = 2000; // Typical response size for a Next.js project
        
        $totalTokens = $promptTokens + $estimatedResponseTokens;
        
        $costs = [
            'openai' => [
                'model' => 'gpt-4',
                'input_cost_per_1k' => 0.03, // $0.03 per 1K input tokens
                'output_cost_per_1k' => 0.06, // $0.06 per 1K output tokens
                'estimated_cost' => ($promptTokens / 1000 * 0.03) + ($estimatedResponseTokens / 1000 * 0.06)
            ],
            'claude' => [
                'model' => 'claude-3-sonnet-20240229',
                'input_cost_per_1k' => 0.003, // $0.003 per 1K input tokens
                'output_cost_per_1k' => 0.015, // $0.015 per 1K output tokens
                'estimated_cost' => ($promptTokens / 1000 * 0.003) + ($estimatedResponseTokens / 1000 * 0.015)
            ]
        ];
        
        if (!isset($costs[$provider])) {
            throw new \InvalidArgumentException("Unknown provider: {$provider}");
        }
        
        $providerCost = $costs[$provider];
        
        return [
            'provider' => $provider,
            'model' => $providerCost['model'],
            'prompt_tokens' => $promptTokens,
            'estimated_response_tokens' => $estimatedResponseTokens,
            'total_estimated_tokens' => $totalTokens,
            'estimated_cost_usd' => round($providerCost['estimated_cost'], 6),
            'cost_breakdown' => [
                'input_cost' => round($promptTokens / 1000 * $providerCost['input_cost_per_1k'], 6),
                'output_cost' => round($estimatedResponseTokens / 1000 * $providerCost['output_cost_per_1k'], 6)
            ],
            'cost_per_1k_tokens' => [
                'input' => $providerCost['input_cost_per_1k'],
                'output' => $providerCost['output_cost_per_1k']
            ]
        ];
    }
    
    /**
     * Estimate tokens for a given text (rough approximation)
     */
    private function estimateTokens(string $text): int
    {
        // Rough estimation: ~4 characters per token for English text
        return (int) ceil(strlen($text) / 4);
    }
    
    /**
     * Get cost comparison for all providers
     */
    public function compareCosts(string $prompt): array
    {
        $providers = ['openai', 'claude'];
        $comparison = [];
        
        foreach ($providers as $provider) {
            $comparison[$provider] = $this->estimateCost($prompt, $provider);
        }
        
        // Sort by cost (cheapest first)
        uasort($comparison, function($a, $b) {
            return $a['estimated_cost_usd'] <=> $b['estimated_cost_usd'];
        });
        
        return $comparison;
    }
    
    /**
     * Get monthly cost estimate based on usage
     */
    public function estimateMonthlyCost(int $websitesPerMonth, string $averagePromptLength = 'medium'): array
    {
        $promptLengths = [
            'short' => 'Create a simple homepage',
            'medium' => 'Create a modern e-commerce website for selling handmade jewelry with product catalog, shopping cart, and payment integration',
            'long' => 'Create a comprehensive business website for a consulting firm with services, team profiles, case studies, blog, contact forms, and client testimonials'
        ];
        
        $prompt = $promptLengths[$averagePromptLength] ?? $promptLengths['medium'];
        $comparison = $this->compareCosts($prompt);
        
        $monthly = [];
        foreach ($comparison as $provider => $costs) {
            $monthly[$provider] = [
                'provider' => $provider,
                'model' => $costs['model'],
                'cost_per_website' => $costs['estimated_cost_usd'],
                'monthly_cost' => round($costs['estimated_cost_usd'] * $websitesPerMonth, 2),
                'yearly_cost' => round($costs['estimated_cost_usd'] * $websitesPerMonth * 12, 2)
            ];
        }
        
        return $monthly;
    }
}
