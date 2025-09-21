<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// This test file uses REAL AI services - only run when you want to test actual AI integration
// WARNING: This will make real API calls and cost money!

uses(RefreshDatabase::class);

// Add test group to easily exclude from regular test runs
uses()->group('real-ai');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->project = Project::factory()->create([
        'user_id' => $this->user->id,
        'generated_code' => null, // Start with no generated code
    ]);
});

it('can generate a real website with Claude AI', function () {
    // Skip if Claude API key is not configured
    if (! config('services.claude.api_key')) {
        $this->markTestSkipped('Claude API key not configured');
    }

    $response = $this->post("/projects/{$this->project->id}/prompts", [
        'prompt' => 'Create a simple portfolio website with a hero section and about me section',
        'auto_start_container' => false, // Don't auto-start for real AI tests
    ]);

    $response->assertSuccessful();

    // Wait for AI processing to complete
    $this->waitForPromptCompletion($this->project);

    // Verify the project was updated with generated code
    $this->project->refresh();
    expect($this->project->generated_code)->not->toBeNull();
    expect($this->project->status)->toBe('ready');

    // Verify the generated code contains expected Next.js structure
    $generatedCode = json_decode($this->project->generated_code, true);
    expect($generatedCode)->toHaveKey('package.json');
    expect($generatedCode)->toHaveKey('app/page.tsx');
    expect($generatedCode)->toHaveKey('app/layout.tsx');
});

it('can generate a real website with OpenAI', function () {
    // Skip if OpenAI API key is not configured
    if (! config('services.openai.api_key')) {
        $this->markTestSkipped('OpenAI API key not configured');
    }

    $response = $this->post("/projects/{$this->project->id}/prompts", [
        'prompt' => 'Create a modern landing page for a tech startup with features section',
        'auto_start_container' => false,
    ]);

    $response->assertSuccessful();

    // Wait for AI processing to complete
    $this->waitForPromptCompletion($this->project);

    // Verify the project was updated with generated code
    $this->project->refresh();
    expect($this->project->generated_code)->not->toBeNull();
    expect($this->project->status)->toBe('ready');
});

it('can generate and auto-start container with real AI', function () {
    // Skip if no AI API keys are configured
    if (! config('services.claude.api_key') && ! config('services.openai.api_key')) {
        $this->markTestSkipped('No AI API keys configured');
    }

    $response = $this->post("/projects/{$this->project->id}/prompts", [
        'prompt' => 'Create a simple blog homepage with navigation and article list',
        'auto_start_container' => true,
    ]);

    $response->assertSuccessful();

    // Wait for AI processing to complete
    $this->waitForPromptCompletion($this->project);

    // Verify the project was updated with generated code
    $this->project->refresh();
    expect($this->project->generated_code)->not->toBeNull();
    expect($this->project->status)->toBe('ready');

    // Check if container was created (if Docker is available)
    $containers = $this->project->containers;
    if (count($containers) > 0) {
        expect($containers->first()->status)->toBeIn(['starting', 'running']);
    }
});

it('handles AI generation errors gracefully', function () {
    // This test will fail with real AI if there are API issues
    $response = $this->post("/projects/{$this->project->id}/prompts", [
        'prompt' => 'Create a website with invalid prompt that might cause AI errors',
        'auto_start_container' => false,
    ]);

    $response->assertSuccessful();

    // Wait a bit for processing
    sleep(2);

    // Check if prompt was marked as failed or completed
    $this->project->refresh();
    $prompt = $this->project->prompts()->latest()->first();
    expect($prompt->status)->toBeIn(['completed', 'failed']);
});

/**
 * Helper method to wait for prompt completion
 */
function waitForPromptCompletion($project, $maxWaitSeconds = 30)
{
    $startTime = time();

    while (time() - $startTime < $maxWaitSeconds) {
        $project->refresh();
        $prompt = $project->prompts()->latest()->first();

        if ($prompt && in_array($prompt->status, ['completed', 'failed'])) {
            return;
        }

        sleep(1);
    }

    throw new Exception('Prompt processing timed out after '.$maxWaitSeconds.' seconds');
}
