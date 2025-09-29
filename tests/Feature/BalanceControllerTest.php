<?php

use App\Models\User;

test('gets user balance information', function () {
    $user = User::factory()->create(['balance' => 10.0, 'total_spent' => 2.5]);

    $response = $this->actingAs($user)->getJson('/api/balance');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'balance',
            'formatted_balance',
            'total_spent',
            'formatted_total_spent',
            'can_generate',
        ])
        ->assertJson([
            'balance' => 10.0,
            'total_spent' => 2.5,
            'can_generate' => true,
        ]);
});

test('gets cost estimates for different providers', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/balance/cost-estimates?tokens=1000');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'tokens_used',
            'costs' => [
                'gemini',
                'claude',
                'openai',
                'cursor-cli',
            ],
            'formatted_costs',
        ])
        ->assertJson([
            'tokens_used' => 1000,
        ]);
});

test('checks if user can afford generation', function () {
    $user = User::factory()->create(['balance' => 10.0]);

    $response = $this->actingAs($user)->getJson('/api/balance/can-afford?provider=gemini&tokens=1000');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'can_afford',
            'cost',
            'formatted_cost',
            'current_balance',
            'formatted_balance',
        ])
        ->assertJson([
            'can_afford' => true,
            'current_balance' => 10.0,
        ]);
});

test('returns false when user cannot afford generation', function () {
    $user = User::factory()->create(['balance' => 0.001]);

    $response = $this->actingAs($user)->getJson('/api/balance/can-afford?provider=gemini&tokens=1000');

    $response->assertSuccessful()
        ->assertJson([
            'can_afford' => false,
            'current_balance' => 0.001,
        ]);
});

test('requires authentication for balance endpoints', function () {
    $response = $this->getJson('/api/balance');

    $response->assertUnauthorized();
});

test('validates provider parameter for can-afford endpoint', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/balance/can-afford');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['provider']);
});

test('validates tokens parameter for can-afford endpoint', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/balance/can-afford?provider=gemini&tokens=invalid');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['tokens']);
});
