<?php

use App\Models\Container;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// This test focuses specifically on Docker container creation
// It uses pre-generated code to test container functionality without AI costs

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Create a project with pre-generated code (no AI needed)
    $this->project = Project::factory()->create([
        'user_id' => $this->user->id,
        'generated_code' => json_encode([
            'package.json' => '{"name": "test-project", "scripts": {"dev": "next dev"}}',
            'app/page.tsx' => '<div>Test Page</div>',
            'app/layout.tsx' => '<html><body>{children}</body></html>',
            'app/globals.css' => 'body { margin: 0; }',
            'next.config.js' => 'module.exports = {}',
            'tsconfig.json' => '{"compilerOptions": {}}',
        ]),
        'status' => 'ready',
    ]);
});

it('can create a Docker container for a project with generated code', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    $response = $this->post("/api/projects/{$this->project->id}/docker/start");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'container_id',
            'url',
            'port',
            'status',
        ],
    ]);

    // Verify container was created in database
    $this->assertDatabaseHas('containers', [
        'project_id' => $this->project->id,
        'status' => 'running',
    ]);

    // Get the created container
    $container = $this->project->containers()->latest()->first();
    expect($container)->not->toBeNull();
    expect($container->status)->toBe('running');
    expect($container->url)->toContain('r3kt.dev'); // External URL should contain domain
});

it('can get preview URL for a running container', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    // First create a container
    $this->post("/api/projects/{$this->project->id}/docker/start");

    // Then get the preview URL
    $response = $this->get("/api/projects/{$this->project->id}/docker/preview");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data' => [
            'url',
            'port',
            'status',
        ],
    ]);

    $data = $response->json('data');
    expect($data['url'])->toContain('r3kt.dev'); // External URL should contain domain
    expect($data['port'])->toBeNumeric();
});

it('can stop a running container', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    // Create a container first
    $this->post("/api/projects/{$this->project->id}/docker/start");
    $container = $this->project->containers()->latest()->first();

    // Stop the container
    $response = $this->post("/api/containers/{$container->id}/docker/stop");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Container stopped successfully',
    ]);

    // Verify container status was updated
    $container->refresh();
    expect($container->status)->toBe('stopped');
});

it('can restart a stopped container', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    // Create and stop a container
    $this->post("/api/projects/{$this->project->id}/docker/start");
    $container = $this->project->containers()->latest()->first();
    $this->post("/api/containers/{$container->id}/docker/stop");

    // Restart the container
    $response = $this->post("/api/containers/{$container->id}/docker/restart");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Container restarted successfully',
    ]);

    // Verify container is running again
    $container->refresh();
    expect($container->status)->toBe('running');
});

it('can get container status and health', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    // Create a container
    $this->post("/api/projects/{$this->project->id}/docker/start");
    $container = $this->project->containers()->latest()->first();

    // Get container status
    $response = $this->get("/api/containers/{$container->id}/docker/status");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data' => [
            'container_id',
            'status',
            'health' => [
                'status',
                'message',
                'healthy',
            ],
            'stats' => [
                'cpu_usage',
                'memory_usage',
                'uptime',
            ],
        ],
    ]);
});

it('can get container logs', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    // Create a container
    $this->post("/api/projects/{$this->project->id}/docker/start");
    $container = $this->project->containers()->latest()->first();

    // Get container logs
    $response = $this->get("/api/containers/{$container->id}/docker/logs");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data' => [
            'logs',
        ],
    ]);

    $logs = $response->json('data.logs');
    expect($logs)->toBeString();
    expect($logs)->toContain('Container'); // Just check that logs contain some container info
});

it('can list all running containers', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    // Create a container
    $this->post("/api/projects/{$this->project->id}/docker/start");

    // List all running containers
    $response = $this->get('/api/docker/containers');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data' => [
            '*' => [
                'id',
                'name',
                'status',
            ],
        ],
    ]);

    $containers = $response->json('data');
    expect($containers)->toBeArray();
    expect($containers)->toHaveCount(1);
});

it('can cleanup Docker resources', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available');
    }

    // Create a container
    $this->post("/api/projects/{$this->project->id}/docker/start");

    // Cleanup resources
    $response = $this->post('/api/docker/cleanup');

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            'containers',
            'images',
            'errors',
        ],
    ]);
});

it('fails to create container for project without generated code', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Skip if Docker is not available (we can't test this properly without Docker)
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $this->markTestSkipped('Docker not available - cannot test container creation validation');
    }

    // Create a project without generated code
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'generated_code' => null,
        'status' => 'draft',
    ]);

    $response = $this->post("/api/projects/{$project->id}/docker/start");

    $response->assertStatus(400);
    $response->assertJson([
        'success' => false,
        'message' => 'Project has no generated code. Please generate a website first.',
    ]);
});

it('handles Docker service errors gracefully', function () {
    // This test will pass even if Docker is not available
    // because it tests error handling, not actual Docker functionality

    $response = $this->get('/api/docker/info');

    // Should return Docker info or error gracefully
    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'data' => [
            'available',
        ],
    ]);
});

it('returns service unavailable when Docker is not available', function () {
    // Bypass global mocking to use real Docker
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // This test specifically checks the behavior when Docker is not available
    $response = $this->post("/api/projects/{$this->project->id}/docker/start");

    // When Docker is not available, should return 503 Service Unavailable
    if (! app(\App\Services\DockerService::class)->isDockerAvailable()) {
        $response->assertStatus(503);
        $response->assertJson([
            'success' => false,
            'message' => 'Docker is not available on this system',
        ]);
    } else {
        // If Docker is available, the test should pass
        $response->assertSuccessful();
    }
});
