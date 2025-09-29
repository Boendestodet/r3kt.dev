<?php

namespace App\Http\Controllers;

use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function __construct(
        private BalanceService $balanceService
    ) {
        //
    }

    /**
     * Get user's balance information
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $balanceInfo = $this->balanceService->getBalanceInfo($user);

        return response()->json($balanceInfo);
    }

    /**
     * Get cost estimates for different AI providers
     */
    public function costEstimates(Request $request): JsonResponse
    {
        $tokensUsed = $request->input('tokens', 1000);
        $estimates = $this->balanceService->getCostEstimates($tokensUsed);

        return response()->json([
            'tokens_used' => $tokensUsed,
            'costs' => $estimates,
            'formatted_costs' => array_map(fn ($cost) => '$'.number_format($cost, 4), $estimates),
        ]);
    }

    /**
     * Add credits to user's balance (admin only)
     */
    public function addCredits(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:1000',
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user is admin (you can implement your own admin check)
        if (! Auth::user()->is_admin ?? false) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = \App\Models\User::findOrFail($request->user_id);
        $this->balanceService->addCredits($user, $request->amount);

        return response()->json([
            'message' => 'Credits added successfully',
            'new_balance' => $user->fresh()->getFormattedBalance(),
        ]);
    }

    /**
     * Check if user can afford generation with specific provider
     */
    public function canAfford(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|string|in:gemini,claude,openai,cursor-cli',
            'tokens' => 'integer|min:1|max:10000',
        ]);

        $user = Auth::user();
        $provider = $request->input('provider');
        $tokens = $request->input('tokens', 1000);

        $canAfford = $this->balanceService->canAffordGeneration($user, $provider, $tokens);
        $cost = $this->balanceService->calculateGenerationCost($provider, $tokens);

        return response()->json([
            'can_afford' => $canAfford,
            'cost' => $cost,
            'formatted_cost' => '$'.number_format($cost, 4),
            'current_balance' => $user->balance,
            'formatted_balance' => $user->getFormattedBalance(),
        ]);
    }
}
