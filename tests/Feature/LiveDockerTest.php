<?php

use App\Models\Container;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// This test creates REAL Docker containers and shows you the results
// It bypasses the global mocking to use actual Docker

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Create a project with pre-generated code
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

it('creates a real Docker container and shows the results', function () {
    echo '🐳 Creating real Docker container...'.PHP_EOL;

    // Bypass global mocking
    $this->app->forgetInstance(\App\Services\DockerService::class);

    $response = $this->post("/api/projects/{$this->project->id}/docker/start");

    echo 'Response status: '.$response->status().PHP_EOL;
    echo 'Response body: '.$response->content().PHP_EOL;

    if ($response->status() === 503) {
        echo '❌ Docker is not available'.PHP_EOL;
        $this->markTestSkipped('Docker not available');
    }

    $response->assertSuccessful();

    $data = $response->json('data');
    echo '✅ Container created successfully!'.PHP_EOL;
    echo 'Container ID: '.$data['container_id'].PHP_EOL;
    echo 'Status: '.$data['status'].PHP_EOL;
    echo 'Port: '.$data['port'].PHP_EOL;
    echo 'Local URL: '.$data['external_url'].PHP_EOL;
    echo 'External URL: '.$data['url'].PHP_EOL;

    // Verify container was created in database
    $this->assertDatabaseHas('containers', [
        'project_id' => $this->project->id,
        'status' => 'running',
    ]);

    // Get the created container
    $container = $this->project->containers()->latest()->first();
    expect($container)->not->toBeNull();
    expect($container->status)->toBe('running');

    echo '✅ Database record created successfully!'.PHP_EOL;
    echo 'Container ID in DB: '.$container->id.PHP_EOL;
    echo 'Docker Container ID: '.$container->container_id.PHP_EOL;

    // Check if container is actually running
    $dockerContainers = \Illuminate\Support\Facades\Process::run('docker ps --format "table {{.ID}}\t{{.Names}}\t{{.Status}}\t{{.Ports}}"');
    echo '🐳 Running Docker containers:'.PHP_EOL;
    echo $dockerContainers->output().PHP_EOL;
});

it('can get preview URL for the running container', function () {
    // Bypass global mocking
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // First create a container
    $this->post("/api/projects/{$this->project->id}/docker/start");

    $response = $this->get("/api/projects/{$this->project->id}/docker/preview");

    if ($response->status() === 503) {
        $this->markTestSkipped('Docker not available');
    }

    $response->assertSuccessful();

    $data = $response->json('data');
    echo '✅ Preview URL retrieved: '.$data['url'].PHP_EOL;
    echo 'Port: '.$data['port'].PHP_EOL;
    echo 'Status: '.$data['status'].PHP_EOL;

    expect($data['url'])->toContain('r3kt.dev'); // External URL should contain domain
    expect($data['port'])->toBeNumeric();
});

it('can stop the running container', function () {
    // Bypass global mocking
    $this->app->forgetInstance(\App\Services\DockerService::class);

    // Create a container first
    $this->post("/api/projects/{$this->project->id}/docker/start");
    $container = $this->project->containers()->latest()->first();

    if (! $container) {
        $this->markTestSkipped('No container created - Docker not available');
    }

    echo '🛑 Stopping container: '.$container->container_id.PHP_EOL;

    $response = $this->post("/api/containers/{$container->id}/stop");

    $response->assertSuccessful();

    echo '✅ Container stopped successfully!'.PHP_EOL;
    echo 'Response: '.$response->content().PHP_EOL;

    // Verify container status was updated
    $container->refresh();
    expect($container->status)->toBe('stopped');

    // Check Docker containers again
    $dockerContainers = \Illuminate\Support\Facades\Process::run('docker ps --format "table {{.ID}}\t{{.Names}}\t{{.Status}}\t{{.Ports}}"');
    echo '🐳 Running Docker containers after stop:'.PHP_EOL;
    echo $dockerContainers->output().PHP_EOL;
});
