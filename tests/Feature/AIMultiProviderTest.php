<?php

use App\Models\Project;
use App\Models\Prompt;
use App\Models\User;
use App\Services\AIWebsiteGenerator;
use App\Services\OpenAIService;
use App\Services\ClaudeAIService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('ai generator falls back to mock when no providers configured', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Simple test website',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->metadata['ai_provider'])->toBe('mock');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('next');
});

test('ai generator tries providers in correct order', function () {
    // Mock the services to simulate configuration
    $openAIService = Mockery::mock(OpenAIService::class);
    $claudeAIService = Mockery::mock(ClaudeAIService::class);
    
    $openAIService->shouldReceive('isConfigured')->andReturn(true);
    $claudeAIService->shouldReceive('isConfigured')->andReturn(true);
    
    // Claude should be tried first, then OpenAI
    $claudeAIService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('Claude API error'));
    
    $openAIService->shouldReceive('generateWebsite')
        ->once()
        ->andReturn([
            'project' => ['package.json' => '{"name": "test"}'],
            'tokens_used' => 100,
            'model' => 'gpt-4'
        ]);

    $this->app->instance(OpenAIService::class, $openAIService);
    $this->app->instance(ClaudeAIService::class, $claudeAIService);

    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Test website',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->metadata['ai_provider'])->toBe('openai');
    expect($project->status)->toBe('ready');
});

test('ai generator falls back to mock when all providers fail', function () {
    // Mock the services to simulate configuration but failure
    $openAIService = Mockery::mock(OpenAIService::class);
    $claudeAIService = Mockery::mock(ClaudeAIService::class);
    
    $openAIService->shouldReceive('isConfigured')->andReturn(true);
    $claudeAIService->shouldReceive('isConfigured')->andReturn(true);
    
    // Both providers fail
    $claudeAIService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('Claude API error'));
    
    $openAIService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('OpenAI API error'));

    $this->app->instance(OpenAIService::class, $openAIService);
    $this->app->instance(ClaudeAIService::class, $claudeAIService);

    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Test website',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->metadata['ai_provider'])->toBe('mock');
    expect($project->status)->toBe('ready');
});

test('ai generator handles provider configuration correctly', function () {
    $generator = app(AIWebsiteGenerator::class);
    
    // Use reflection to access private method
    $reflection = new ReflectionClass($generator);
    $method = $reflection->getMethod('getAvailableProviders');
    $method->setAccessible(true);
    
    $providers = $method->invoke($generator);
    
    // Should return empty array when no providers configured
    expect($providers)->toBeEmpty();
});

test('ai generator creates valid nextjs project structure', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a simple homepage',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($project->status)->toBe('ready');
    
    $generatedCode = json_decode($project->generated_code, true);
    
    // Should contain essential Next.js files
    expect($generatedCode)->toHaveKey('package.json');
    expect($generatedCode)->toHaveKey('next.config.js');
    expect($generatedCode)->toHaveKey('tsconfig.json');
    expect($generatedCode)->toHaveKey('app/layout.tsx');
    expect($generatedCode)->toHaveKey('app/page.tsx');
    expect($generatedCode)->toHaveKey('app/globals.css');
    
    // package.json should be valid JSON
    $packageJson = json_decode($generatedCode['package.json'], true);
    expect($packageJson)->toHaveKey('name');
    expect($packageJson)->toHaveKey('dependencies');
    expect($packageJson['dependencies'])->toHaveKey('next');
});
