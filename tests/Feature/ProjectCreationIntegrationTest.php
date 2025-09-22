<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Container;
use App\Models\Prompt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    
    // Mock the file system
    Storage::fake('local');
});

it('creates complete project with all related data', function () {
    $projectData = [
        'name' => 'Complete Test Project',
        'description' => 'A comprehensive test project',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'Next.js',
            'auto_deploy' => true,
            'features' => ['typescript', 'tailwind', 'eslint']
        ]
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    // Verify project was created
    $project = Project::where('name', 'Complete Test Project')->first();
    expect($project)->not->toBeNull();
    expect($project->user_id)->toBe($this->user->id);
    expect($project->description)->toBe('A comprehensive test project');
    expect($project->settings)->toBe($projectData['settings']);
    expect($project->slug)->toBe('complete-test-project');
    
    // Note: Containers are created separately through Docker management
});

it('creates project with container and prompt relationships', function () {
    $projectData = [
        'name' => 'Project with Relations',
        'description' => 'A project to test relationships',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', 'Project with Relations')->first();
    
    // Create related data
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running'
    ]);
    
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a simple React component',
        'status' => 'completed'
    ]);

    // Refresh relationships
    $project->load(['containers', 'prompts']);

    expect($project->containers)->toHaveCount(1);
    expect($project->containers->first()->id)->toBe($container->id);
    expect($project->prompts)->toHaveCount(1);
    expect($project->prompts->first()->id)->toBe($prompt->id);
});

it('handles project creation with Inertia request', function () {
    $projectData = [
        'name' => 'Inertia Test Project',
        'description' => 'Testing Inertia response',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData, [
        'X-Inertia' => true,
        'X-Inertia-Version' => '1.0'
    ]);

    $response->assertStatus(200);
    // Check that the response contains the project data in JSON
    $responseData = $response->json();
    expect($responseData)->toHaveKey('props');
    expect($responseData['props'])->toHaveKey('createdProject');
    expect($responseData['props'])->toHaveKey('projects');
    expect($responseData['props']['createdProject']['name'])->toBe('Inertia Test Project');
});

it('handles project creation with AJAX request', function () {
    $projectData = [
        'name' => 'AJAX Test Project',
        'description' => 'Testing AJAX response',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData, [
        'X-Requested-With' => 'XMLHttpRequest'
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'message' => 'Project created successfully!',
    ]);
    
    $responseData = $response->json();
    expect($responseData['project']['name'])->toBe('AJAX Test Project');
    expect($responseData['project']['user_id'])->toBe($this->user->id);
});

it('creates project with file system setup', function () {
    $projectData = [
        'name' => 'File System Project',
        'description' => 'Testing file system setup',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    // Verify project was created (setupProjectFiles is called internally)
    $project = Project::where('name', 'File System Project')->first();
    expect($project)->not->toBeNull();
});

it('handles multiple projects for same user', function () {
    $projects = [
        [
            'name' => 'First Project',
            'description' => 'First project description',
            'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
        ],
        [
            'name' => 'Second Project',
            'description' => 'Second project description',
            'settings' => ['ai_model' => 'GPT-4', 'stack' => 'React']
        ],
        [
            'name' => 'Third Project',
            'description' => 'Third project description',
            'settings' => ['ai_model' => 'Gemini', 'stack' => 'Vue']
        ]
    ];

    foreach ($projects as $projectData) {
        $response = $this->post('/projects', $projectData);
        $response->assertRedirect();
    }

    $userProjects = Project::where('user_id', $this->user->id)->get();
    expect($userProjects)->toHaveCount(3);
    
    $projectNames = $userProjects->pluck('name')->toArray();
    expect($projectNames)->toContain('First Project');
    expect($projectNames)->toContain('Second Project');
    expect($projectNames)->toContain('Third Project');
    
    // Note: Containers are created separately through Docker management
    // Projects are created without containers initially
});

it('handles concurrent project creation', function () {
    $projectData = [
        'name' => 'Concurrent Project',
        'description' => 'Testing concurrent creation',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    // Simulate concurrent requests
    $responses = [];
    for ($i = 0; $i < 3; $i++) {
        $responses[] = $this->post('/projects', array_merge($projectData, [
            'name' => $projectData['name'] . ' ' . ($i + 1)
        ]));
    }

    foreach ($responses as $response) {
        $response->assertRedirect();
    }

    $userProjects = Project::where('user_id', $this->user->id)
        ->where('name', 'like', 'Concurrent Project%')
        ->get();
    
    expect($userProjects)->toHaveCount(3);
});

it('validates project creation with complex settings', function () {
    $complexSettings = [
        'ai_model' => 'Claude Code',
        'stack' => 'Next.js',
        'auto_deploy' => true,
        'custom_config' => [
            'port' => 3000,
            'environment' => 'development',
            'database' => [
                'type' => 'postgresql',
                'host' => 'localhost',
                'port' => 5432
            ]
        ],
        'features' => ['typescript', 'tailwind', 'eslint', 'prettier'],
        'deployment' => [
            'platform' => 'vercel',
            'region' => 'us-east-1',
            'domain' => 'myproject.vercel.app'
        ]
    ];

    $projectData = [
        'name' => 'Complex Settings Project',
        'description' => 'A project with complex configuration',
        'settings' => $complexSettings
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', 'Complex Settings Project')->first();
    expect($project->settings)->toBe($complexSettings);
});

it('handles project creation with special characters', function () {
    $specialName = 'Project with Special @#$% Characters & Symbols!';
    $specialDescription = 'A project with émojis 🚀 and spëcial çharacters';

    $projectData = [
        'name' => $specialName,
        'description' => $specialDescription,
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', $specialName)->first();
    expect($project)->not->toBeNull();
    expect($project->description)->toBe($specialDescription);
    expect($project->slug)->toBe('project-with-special-at-characters-symbols');
});

it('handles project creation with unicode characters', function () {
    $unicodeName = 'Projét with Ünicode Çharacters 项目';
    $unicodeDescription = 'Un projet avec des caractères spéciaux 项目描述';

    $projectData = [
        'name' => $unicodeName,
        'description' => $unicodeDescription,
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', $unicodeName)->first();
    expect($project)->not->toBeNull();
    expect($project->description)->toBe($unicodeDescription);
});

it('handles project creation with empty settings', function () {
    $projectData = [
        'name' => 'Empty Settings Project',
        'description' => 'A project with empty settings',
        'settings' => []
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', 'Empty Settings Project')->first();
    expect($project->settings)->toBe([]);
});

it('handles project creation with null settings', function () {
    $projectData = [
        'name' => 'Null Settings Project',
        'description' => 'A project with null settings'
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', 'Null Settings Project')->first();
    expect($project->settings)->toBe([]);
});

it('handles project creation with very long name', function () {
    $longName = str_repeat('a', 255); // Maximum allowed length

    $projectData = [
        'name' => $longName,
        'description' => 'A project with maximum length name',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', $longName)->first();
    expect($project)->not->toBeNull();
});

it('handles project creation with very long description', function () {
    $longDescription = str_repeat('a', 1000); // Maximum allowed length

    $projectData = [
        'name' => 'Long Description Project',
        'description' => $longDescription,
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ];

    $response = $this->post('/projects', $projectData);

    $response->assertRedirect();
    
    $project = Project::where('name', 'Long Description Project')->first();
    expect($project->description)->toBe($longDescription);
});

it('can create projects with containers for testing', function () {
    // Create a project
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Project with Container',
        'description' => 'Testing project with container',
        'settings' => ['ai_model' => 'Claude Code', 'stack' => 'Next.js']
    ]);

    // Create multiple containers for the project
    $container1 = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'port' => 3000,
        'url' => 'http://localhost:3000',
    ]);

    $container2 = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'stopped',
        'port' => 3001,
        'url' => 'http://localhost:3001',
    ]);

    $project->load('containers');
    
    // Verify project has containers
    expect($project->containers)->toHaveCount(2);
    
    // Verify container properties
    $runningContainer = $project->containers->where('status', 'running')->first();
    expect($runningContainer)->not->toBeNull();
    expect($runningContainer->port)->toBe('3000');
    expect($runningContainer->url)->toBe('http://localhost:3000');
    
    $stoppedContainer = $project->containers->where('status', 'stopped')->first();
    expect($stoppedContainer)->not->toBeNull();
    expect($stoppedContainer->port)->toBe('3001');
    expect($stoppedContainer->url)->toBe('http://localhost:3001');
    
    // Test active container method
    $activeContainer = $project->getActiveContainer();
    expect($activeContainer)->not->toBeNull();
    expect($activeContainer->status)->toBe('running');
});