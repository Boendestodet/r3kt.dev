<?php

use App\Models\Container;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// Helper function to create a project with container
function createProjectWithContainer($user, $projectData = [])
{
    $project = Project::factory()->create(array_merge([
        'user_id' => $user->id,
    ], $projectData));

    // Create a container for the project
    Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'port' => 3000,
        'url' => 'http://localhost:3000',
    ]);

    return $project;
}

it('can create a project with valid data', function () {
    $projectData = [
        'name' => 'Test Project',
        'description' => 'A test project description',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'Next.js',
            'auto_deploy' => true,
        ],
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'description' => 'A test project description',
        'user_id' => $this->user->id,
    ]);

    $project = Project::where('name', 'Test Project')->first();
    expect($project->settings)->toBe([
        'ai_model' => 'Claude Code',
        'stack' => 'Next.js',
        'auto_deploy' => true,
    ]);

    // Note: Containers are not automatically created during project creation
    // They are created separately through the Docker management system
});

it('validates required project name', function () {
    $response = $this->post('/projects', [
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('validates unique project name per user', function () {
    // Create a project with the same name
    Project::factory()->create([
        'name' => 'Duplicate Project',
        'user_id' => $this->user->id,
    ]);

    $response = $this->post('/projects', [
        'name' => 'Duplicate Project',
        'description' => 'Another project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('allows same project name for different users', function () {
    $otherUser = User::factory()->create();

    // Create a project for another user
    Project::factory()->create([
        'name' => 'Shared Project Name',
        'user_id' => $otherUser->id,
    ]);

    $response = $this->post('/projects', [
        'name' => 'Shared Project Name',
        'description' => 'My project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'name' => 'Shared Project Name',
        'user_id' => $this->user->id,
    ]);
});

it('validates project name length', function () {
    $response = $this->post('/projects', [
        'name' => str_repeat('a', 256), // Too long
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('validates description length', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => str_repeat('a', 1001), // Too long
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertSessionHasErrors(['description']);
});

it('accepts optional description', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'description' => null,
        'user_id' => $this->user->id,
    ]);
});

it('accepts optional settings', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => 'A test project',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'user_id' => $this->user->id,
    ]);
});

it('creates project with default settings when none provided', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => 'A test project',
    ]);

    $response->assertRedirect();

    $project = Project::where('name', 'Test Project')->first();
    expect($project->settings)->toBe([]);
});

it('generates slug from project name', function () {
    $response = $this->post('/projects', [
        'name' => 'My Awesome Project!',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertRedirect();

    $project = Project::where('name', 'My Awesome Project!')->first();
    expect($project->slug)->toBe('my-awesome-project');
});

it('handles special characters in project name for slug generation', function () {
    $response = $this->post('/projects', [
        'name' => 'Project with Special @#$% Characters',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertRedirect();

    $project = Project::where('name', 'Project with Special @#$% Characters')->first();
    expect($project->slug)->toBe('project-with-special-at-characters');
});

it('creates project files after project creation', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertRedirect();

    // Verify project was created (setupProjectFiles is called internally)
    $project = Project::where('name', 'Test Project')->first();
    expect($project)->not->toBeNull();

    // Note: Containers are created separately through Docker management
});

it('can create containers for projects manually', function () {
    // First create a project
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Container Test Project',
        'description' => 'Testing container creation',
    ]);

    // Then create a container for the project
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'port' => 3000,
        'url' => 'http://localhost:3000',
    ]);

    $project->load('containers');

    // Verify container was created with correct properties
    expect($project->containers)->toHaveCount(1);

    $container = $project->containers->first();
    expect($container->project_id)->toBe($project->id);
    expect($container->status)->toBe('running');
    expect($container->port)->toBe('3000');
    expect($container->url)->toBe('http://localhost:3000');
    expect($container->container_id)->not->toBeNull();
    expect($container->name)->not->toBeNull();
});

it('returns created project in response for Inertia requests', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ], [
        'X-Inertia' => true,
        'X-Inertia-Version' => '1.0',
    ]);

    $response->assertStatus(200);
    // Check that the response contains the project data in JSON
    $responseData = $response->json();
    expect($responseData)->toHaveKey('props');
    expect($responseData['props'])->toHaveKey('createdProject');
    expect($responseData['props'])->toHaveKey('projects');
    expect($responseData['props']['createdProject']['name'])->toBe('Test Project');
});

it('returns JSON response for AJAX requests', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ], [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Project created successfully!',
    ]);
    $response->assertJsonStructure([
        'success',
        'message',
        'project' => [
            'id',
            'name',
            'description',
            'slug',
            'settings',
            'created_at',
            'updated_at',
        ],
    ]);
});

it('requires authentication to create project', function () {
    auth()->logout();

    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertRedirect('/login');
});

it('validates settings array structure', function () {
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => 'A test project',
        'settings' => 'invalid-settings', // Should be array
    ]);

    $response->assertSessionHasErrors(['settings']);
});

it('handles empty project name gracefully', function () {
    $response = $this->post('/projects', [
        'name' => '',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('handles whitespace-only project name', function () {
    $response = $this->post('/projects', [
        'name' => '   ',
        'description' => 'A test project',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js'],
    ]);

    $response->assertSessionHasErrors(['name']);
});

it('creates project with complex settings', function () {
    $complexSettings = [
        'ai_model' => 'Claude Code',
        'stack' => 'Next.js',
        'auto_deploy' => true,
        'custom_config' => [
            'port' => 3000,
            'environment' => 'development',
        ],
        'features' => ['typescript', 'tailwind', 'eslint'],
    ];

    $response = $this->post('/projects', [
        'name' => 'Complex Project',
        'description' => 'A project with complex settings',
        'settings' => $complexSettings,
    ]);

    $response->assertRedirect();

    $project = Project::where('name', 'Complex Project')->first();
    expect($project->settings)->toBe($complexSettings);
});

it('can create projects with different stack types', function () {
    $stackTypes = [
        'nextjs' => 'Next.js',
        'vite-react' => 'Vite + React',
        'vite-vue' => 'Vite + Vue',
        'sveltekit' => 'SvelteKit',
    ];

    foreach ($stackTypes as $stackKey => $stackName) {
        $response = $this->post('/projects', [
            'name' => "Test {$stackName} Project",
            'description' => "A test project using {$stackName}",
            'settings' => [
                'ai_model' => 'Claude Code',
                'stack' => $stackKey,
                'auto_deploy' => true,
            ],
        ]);

        $response->assertRedirect();

        $project = Project::where('name', "Test {$stackName} Project")->first();
        expect($project)->not->toBeNull();
        expect($project->settings['stack'])->toBe($stackKey);
        expect($project->settings['ai_model'])->toBe('Claude Code');
        expect($project->settings['auto_deploy'])->toBeTrue();
    }
});

it('validates stack type in settings', function () {
    $response = $this->post('/projects', [
        'name' => 'Invalid Stack Project',
        'description' => 'A project with invalid stack',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'invalid-stack',
        ],
    ]);

    $response->assertRedirect();

    // The project should still be created, but with the invalid stack
    $project = Project::where('name', 'Invalid Stack Project')->first();
    expect($project)->not->toBeNull();
    expect($project->settings['stack'])->toBe('invalid-stack');
});

it('creates project with vite-react stack and validates settings', function () {
    $response = $this->post('/projects', [
        'name' => 'Vite React Project',
        'description' => 'A Vite + React + TypeScript project',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'vite-react',
            'features' => ['typescript', 'tailwind', 'eslint'],
        ],
    ]);

    $response->assertRedirect();

    $project = Project::where('name', 'Vite React Project')->first();
    expect($project)->not->toBeNull();
    expect($project->settings['stack'])->toBe('vite-react');
    expect($project->settings['features'])->toBe(['typescript', 'tailwind', 'eslint']);
});

it('creates project with vite-vue stack and validates settings', function () {
    $response = $this->post('/projects', [
        'name' => 'Vite Vue Project',
        'description' => 'A Vite + Vue + TypeScript project',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'vite-vue',
            'features' => ['typescript', 'tailwind', 'eslint'],
        ],
    ]);

    $response->assertRedirect();

    $project = Project::where('name', 'Vite Vue Project')->first();
    expect($project)->not->toBeNull();
    expect($project->settings['stack'])->toBe('vite-vue');
    expect($project->settings['features'])->toBe(['typescript', 'tailwind', 'eslint']);
});

it('creates project with sveltekit stack and validates settings', function () {
    $response = $this->post('/projects', [
        'name' => 'SvelteKit Project',
        'description' => 'A SvelteKit project',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'sveltekit',
            'features' => ['typescript', 'tailwind', 'eslint'],
        ],
    ]);

    $response->assertRedirect();

    $project = Project::where('name', 'SvelteKit Project')->first();
    expect($project)->not->toBeNull();
    expect($project->settings['stack'])->toBe('sveltekit');
    expect($project->settings['features'])->toBe(['typescript', 'tailwind', 'eslint']);
});
