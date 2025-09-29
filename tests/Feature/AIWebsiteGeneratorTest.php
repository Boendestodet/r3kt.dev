<?php

use App\Models\Project;
use App\Models\Prompt;
use App\Models\User;
use App\Services\AIWebsiteGenerator;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('generates portfolio website for portfolio prompt', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a portfolio website for a developer',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('package.json');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('next');
});

test('generates ecommerce website for shop prompt', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create an online store for selling electronics',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('package.json');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('next');
});

test('generates blog website for blog prompt', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a tech blog with articles',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('package.json');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('next');
});

test('generates landing page for marketing prompt', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a landing page for marketing campaign',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('package.json');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('next');
});

test('generates dashboard for admin prompt', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create an admin dashboard with analytics',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('package.json');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('next');
});

test('generates generic website for unknown prompt', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a random website about cats',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('package.json');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('next');
});

test('correctly maps different stack names to project types', function () {
    $generator = app(AIWebsiteGenerator::class);

    // Test the getProjectType method directly
    $reflection = new ReflectionClass($generator);
    $method = $reflection->getMethod('getProjectType');
    $method->setAccessible(true);

    // Test various stack name mappings
    $testCases = [
        'Next.js' => 'nextjs',
        'nextjs' => 'nextjs',
        'next' => 'nextjs',
        'Vite + React' => 'vite-react',
        'vite + react' => 'vite-react',
        'vite' => 'vite-react',
        'Vite + Vue' => 'vite-vue',
        'vite + vue' => 'vite-vue',
        'SvelteKit' => 'sveltekit',
        'sveltekit' => 'sveltekit',
        'svelte' => 'sveltekit',
        'Unknown Stack' => 'nextjs', // Should default to nextjs
    ];

    foreach ($testCases as $input => $expected) {
        $result = $method->invoke($generator, $input);
        expect($result)->toBe($expected, "Failed for input: '{$input}'");
    }
});

test('generates website for vite-react project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-react'],
    ]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a portfolio website with Vite and React',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('index.html');
    expect($prompt->response)->toContain('src/main.tsx');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('vite');
});

test('generates website for vite-vue project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-vue'],
    ]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a portfolio website with Vite and Vue',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('index.html');
    expect($prompt->response)->toContain('src/main.ts');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('vite');
});

test('generates website for sveltekit project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'sveltekit'],
    ]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a portfolio website with SvelteKit',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->response)->toContain('package.json');
    expect($project->status)->toBe('ready');
    expect($project->generated_code)->toContain('svelte');
});
