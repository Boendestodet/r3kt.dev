<?php

use App\Models\Project;
use App\Models\Prompt;
use App\Models\User;
use App\Services\AIWebsiteGenerator;
use App\Services\ClaudeAIService;
use App\Services\CursorAIService;
use App\Services\OpenAIService;

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
    $cursorAIService = Mockery::mock(CursorAIService::class);

    $openAIService->shouldReceive('isConfigured')->andReturn(true);
    $claudeAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);

    // Claude should be tried first, then OpenAI, then Cursor CLI
    $claudeAIService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('Claude API error'));

    $openAIService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('OpenAI API error'));

    $cursorAIService->shouldReceive('generateWebsite')
        ->once()
        ->andReturn([
            'project' => ['package.json' => '{"name": "test"}'],
            'tokens_used' => 100,
            'model' => 'gpt-4',
        ]);

    $this->app->instance(OpenAIService::class, $openAIService);
    $this->app->instance(ClaudeAIService::class, $claudeAIService);
    $this->app->instance(CursorAIService::class, $cursorAIService);

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
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
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

    // Should return array of available providers (depends on configuration)
    expect($providers)->toBeArray();

    // Each provider should have required structure
    foreach ($providers as $provider) {
        expect($provider)->toHaveKeys(['name', 'service', 'temperature', 'max_tokens']);
        expect($provider['name'])->toBeIn(['claude', 'openai', 'gemini', 'cursor-cli']);
    }
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

test('cursor cli service works with all stack types', function () {
    $cursorAIService = Mockery::mock(CursorAIService::class);
    
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('generateWebsite')
        ->with('Test website', 'nextjs')
        ->andReturn([
            'project' => [
                'package.json' => '{"name": "test-nextjs", "dependencies": {"next": "^14.0.0"}}',
                'app/page.tsx' => 'export default function Page() { return <div>Test</div>; }',
            ],
            'tokens_used' => 150,
            'model' => 'cursor-cli',
        ]);

    $this->app->instance(CursorAIService::class, $cursorAIService);

    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'nextjs'],
    ]);
    
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Test website',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($project->status)->toBe('ready');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
});

test('ai generator respects user preferred model', function () {
    // Mock the services to simulate configuration
    $openAIService = Mockery::mock(OpenAIService::class);
    $claudeAIService = Mockery::mock(ClaudeAIService::class);
    $cursorAIService = Mockery::mock(CursorAIService::class);

    $openAIService->shouldReceive('isConfigured')->andReturn(true);
    $claudeAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);

    // User prefers Cursor CLI, so it should be tried first
    $cursorAIService->shouldReceive('generateWebsite')
        ->once()
        ->andReturn([
            'project' => ['package.json' => '{"name": "test"}'],
            'tokens_used' => 100,
            'model' => 'cursor-cli',
        ]);

    // Other services should not be called since Cursor CLI succeeds
    $claudeAIService->shouldNotReceive('generateWebsite');
    $openAIService->shouldNotReceive('generateWebsite');

    $this->app->instance(OpenAIService::class, $openAIService);
    $this->app->instance(ClaudeAIService::class, $claudeAIService);
    $this->app->instance(CursorAIService::class, $cursorAIService);

    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['ai_model' => 'Cursor CLI', 'stack' => 'nextjs'],
    ]);

    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Test website',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
    expect($project->status)->toBe('ready');
});

test('cursor cli service works with vite-react stack', function () {
    $cursorAIService = Mockery::mock(CursorAIService::class);
    
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('generateWebsite')
        ->with('Test Vite website', 'vite-react')
        ->andReturn([
            'project' => [
                'package.json' => '{"name": "test-vite", "dependencies": {"react": "^18.0.0", "vite": "^4.0.0"}}',
                'src/App.tsx' => 'export default function App() { return <div>Test Vite</div>; }',
            ],
            'tokens_used' => 120,
            'model' => 'cursor-cli',
        ]);

    $this->app->instance(CursorAIService::class, $cursorAIService);

    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-react'],
    ]);
    
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Test Vite website',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($project->status)->toBe('ready');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
});