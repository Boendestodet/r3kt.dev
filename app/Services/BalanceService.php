<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    /**
     * Calculate the cost for AI generation based on provider and project complexity
     */
    public function calculateGenerationCost(string $provider, int $tokensUsed = 1000): float
    {
        $costPerToken = match ($provider) {
            'gemini' => 0.00125 / 1000, // $0.00125 per 1K tokens
            'claude' => 0.003 / 1000,   // $0.003 per 1K tokens
            'openai' => 0.03 / 1000,    // $0.03 per 1K tokens
            'cursor-cli' => 0.001 / 1000, // Estimated cost for Cursor CLI
            'mock' => 0.0005 / 1000,    // Small cost for mock generation
            default => 0.01 / 1000,      // Default fallback cost
        };

        return $tokensUsed * $costPerToken;
    }

    /**
     * Check if user has sufficient balance for AI generation
     */
    public function canAffordGeneration(User $user, string $provider, int $tokensUsed = 1000): bool
    {
        $cost = $this->calculateGenerationCost($provider, $tokensUsed);

        return $user->hasSufficientBalance($cost);
    }

    /**
     * Deduct cost from user's balance for AI generation
     */
    public function deductGenerationCost(User $user, string $provider, int $tokensUsed = 1000): bool
    {
        $cost = $this->calculateGenerationCost($provider, $tokensUsed);

        if (! $user->hasSufficientBalance($cost)) {
            Log::warning('Insufficient balance for user', [
                'user_id' => $user->id,
                'required_cost' => $cost,
                'current_balance' => $user->balance,
                'provider' => $provider,
                'tokens_used' => $tokensUsed,
            ]);

            return false;
        }

        $success = $user->deductBalance($cost);

        if ($success) {
            // Refresh the user object to get updated balance
            $user->refresh();

            Log::info('Balance deducted for AI generation', [
                'user_id' => $user->id,
                'cost' => $cost,
                'new_balance' => $user->balance,
                'provider' => $provider,
                'tokens_used' => $tokensUsed,
            ]);
        }

        return $success;
    }

    /**
     * Add credits to user's balance
     */
    public function addCredits(User $user, float $amount): void
    {
        $user->addBalance($amount);

        Log::info('Credits added to user balance', [
            'user_id' => $user->id,
            'amount' => $amount,
            'new_balance' => $user->balance,
        ]);
    }

    /**
     * Get user's balance information
     */
    public function getBalanceInfo(User $user): array
    {
        return [
            'balance' => $user->balance,
            'formatted_balance' => $user->getFormattedBalance(),
            'total_spent' => $user->total_spent,
            'formatted_total_spent' => $user->getFormattedTotalSpent(),
            'can_generate' => $this->canAffordGeneration($user, 'gemini'), // Check with cheapest provider
        ];
    }

    /**
     * Calculate the cost for chat interactions based on provider and tokens
     */
    public function calculateChatCost(string $provider, string $model, int $inputTokens, int $outputTokens): float
    {
        // Use the same pricing as CostCalculationService for consistency
        $costCalculationService = app(CostCalculationService::class);
        $costInfo = $costCalculationService->calculateCost($provider, $model, $inputTokens, $outputTokens);
        
        return $costInfo['cost'];
    }

    /**
     * Check if user has sufficient balance for chat interaction
     */
    public function canAffordChat(User $user, string $provider, string $model, int $inputTokens, int $outputTokens): bool
    {
        $cost = $this->calculateChatCost($provider, $model, $inputTokens, $outputTokens);
        return $user->hasSufficientBalance($cost);
    }

    /**
     * Deduct cost from user's balance for chat interaction
     */
    public function deductChatCost(User $user, string $provider, string $model, int $inputTokens, int $outputTokens): bool
    {
        $cost = $this->calculateChatCost($provider, $model, $inputTokens, $outputTokens);

        if (! $user->hasSufficientBalance($cost)) {
            Log::warning('Insufficient balance for chat interaction', [
                'user_id' => $user->id,
                'required_cost' => $cost,
                'current_balance' => $user->balance,
                'provider' => $provider,
                'model' => $model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
            ]);

            return false;
        }

        $success = $user->deductBalance($cost);

        if ($success) {
            // Refresh the user object to get updated balance
            $user->refresh();

            Log::info('Balance deducted for chat interaction', [
                'user_id' => $user->id,
                'cost' => $cost,
                'new_balance' => $user->balance,
                'provider' => $provider,
                'model' => $model,
                'input_tokens' => $inputTokens,
                'output_tokens' => $outputTokens,
            ]);
        }

        return $success;
    }

    /**
     * Estimate cost for different providers
     */
    public function getCostEstimates(int $tokensUsed = 1000): array
    {
        return [
            'gemini' => $this->calculateGenerationCost('gemini', $tokensUsed),
            'claude' => $this->calculateGenerationCost('claude', $tokensUsed),
            'openai' => $this->calculateGenerationCost('openai', $tokensUsed),
            'cursor-cli' => $this->calculateGenerationCost('cursor-cli', $tokensUsed),
        ];
    }
}
