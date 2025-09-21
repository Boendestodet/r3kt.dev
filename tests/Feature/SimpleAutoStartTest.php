<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->project = Project::factory()->create([
        'user_id' => $this->user->id,
        'generated_code' => json_encode([
            'package.json' => '{"name": "test-project", "scripts": {"dev": "next dev"}}',
            'app/page.tsx' => '<div>Test Page</div>',
            'app/layout.tsx' => '<html><body>{children}</body></html>',
        ]),
    ]);
});

it('can create a prompt with auto-start container enabled', function () {
    $response = $this->post("/projects/{$this->project->id}/prompts", [
        'prompt' => 'Create a modern portfolio website',
        'auto_start_container' => true,
    ]);

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'prompt',
        'message',
        'auto_start_container',
    ]);

    $response->assertJson([
        'auto_start_container' => true,
    ]);

    // Verify prompt was created with auto_start_container = true
    $this->assertDatabaseHas('prompts', [
        'project_id' => $this->project->id,
        'prompt' => 'Create a modern portfolio website',
        'auto_start_container' => true,
    ]);
});

it('can create a prompt with auto-start container disabled', function () {
    $response = $this->post("/projects/{$this->project->id}/prompts", [
        'prompt' => 'Create a modern portfolio website',
        'auto_start_container' => false,
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'auto_start_container' => false,
    ]);

    // Verify prompt was created with auto_start_container = false
    $this->assertDatabaseHas('prompts', [
        'project_id' => $this->project->id,
        'prompt' => 'Create a modern portfolio website',
        'auto_start_container' => false,
    ]);
});

it('defaults auto-start container to false when not provided', function () {
    $response = $this->post("/projects/{$this->project->id}/prompts", [
        'prompt' => 'Create a modern portfolio website',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'auto_start_container' => false,
    ]);

    // Verify prompt was created with auto_start_container = false
    $this->assertDatabaseHas('prompts', [
        'project_id' => $this->project->id,
        'prompt' => 'Create a modern portfolio website',
        'auto_start_container' => false,
    ]);
});
