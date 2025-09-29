<?php

use App\Models\User;
use App\Services\BalanceService;

test('calculates generation cost correctly for different providers', function () {
    $balanceService = new BalanceService;

    $geminiCost = $balanceService->calculateGenerationCost('gemini', 1000);
    $claudeCost = $balanceService->calculateGenerationCost('claude', 1000);
    $openaiCost = $balanceService->calculateGenerationCost('openai', 1000);
    $cursorCost = $balanceService->calculateGenerationCost('cursor-cli', 1000);

    expect($geminiCost)->toBeLessThan($claudeCost);
    expect($claudeCost)->toBeLessThan($openaiCost);
    expect($cursorCost)->toBeLessThan($openaiCost);

    // Test specific values
    expect($geminiCost)->toBe(0.00125);
    expect($claudeCost)->toBe(0.003);
    expect($openaiCost)->toBe(0.03);
    expect($cursorCost)->toBe(0.001);
});

test('checks if user can afford generation', function () {
    $balanceService = new BalanceService;

    // User with sufficient balance
    $userWithBalance = User::factory()->create(['balance' => 10.0]);
    expect($balanceService->canAffordGeneration($userWithBalance, 'gemini', 1000))->toBeTrue();

    // User with insufficient balance
    $userWithoutBalance = User::factory()->create(['balance' => 0.001]);
    expect($balanceService->canAffordGeneration($userWithoutBalance, 'gemini', 1000))->toBeFalse();
});

test('deducts generation cost from user balance', function () {
    $balanceService = new BalanceService;
    $user = User::factory()->create(['balance' => 10.0, 'total_spent' => 0.0]);

    $success = $balanceService->deductGenerationCost($user, 'gemini', 1000);

    expect($success)->toBeTrue();
    expect($user->fresh()->balance)->toBe(9.99875);
    expect($user->fresh()->total_spent)->toBe(0.00125);
});

test('fails to deduct when insufficient balance', function () {
    $balanceService = new BalanceService;
    $user = User::factory()->create(['balance' => 0.001, 'total_spent' => 0.0]);

    $success = $balanceService->deductGenerationCost($user, 'gemini', 1000);

    expect($success)->toBeFalse();
    expect($user->fresh()->balance)->toBe(0.001);
    expect($user->fresh()->total_spent)->toBe(0.0);
});

test('adds credits to user balance', function () {
    $balanceService = new BalanceService;
    $user = User::factory()->create(['balance' => 5.0]);

    $balanceService->addCredits($user, 10.0);

    expect($user->fresh()->balance)->toBe(15.0);
});

test('gets balance info correctly', function () {
    $balanceService = new BalanceService;
    $user = User::factory()->create(['balance' => 10.0, 'total_spent' => 2.5]);

    $balanceInfo = $balanceService->getBalanceInfo($user);

    expect($balanceInfo)->toHaveKeys(['balance', 'formatted_balance', 'total_spent', 'formatted_total_spent', 'can_generate']);
    expect($balanceInfo['balance'])->toBe(10.0);
    expect($balanceInfo['total_spent'])->toBe(2.5);
    expect($balanceInfo['can_generate'])->toBeTrue();
});

test('gets cost estimates for all providers', function () {
    $balanceService = new BalanceService;
    $estimates = $balanceService->getCostEstimates(1000);

    expect($estimates)->toHaveKeys(['gemini', 'claude', 'openai', 'cursor-cli']);
    expect($estimates['gemini'])->toBeLessThan($estimates['claude']);
    expect($estimates['claude'])->toBeLessThan($estimates['openai']);
});
