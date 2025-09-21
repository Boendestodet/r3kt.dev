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
