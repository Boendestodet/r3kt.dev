<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Container;
use App\Services\DockerService;
use Illuminate\Support\Facades\Process;

test('docker service can deploy a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'generated_code' => '<html><body><h1>Test Project</h1></body></html>'
    ]);

    $dockerService = app(DockerService::class);
    $success = $dockerService->deployProject($project);

    expect($success)->toBeTrue();
    
    $project->refresh();
    expect($project->status)->toBe('ready');
    expect($project->preview_url)->not->toBeNull();
    
    $container = $project->containers()->first();
    expect($container)->not->toBeNull();
    expect($container->status)->toBe('running');
});

test('docker service can stop a container', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $dockerService = app(DockerService::class);
    $success = $dockerService->stopContainer($container);

    expect($success)->toBeTrue();
    
    $container->refresh();
    expect($container->status)->toBe('stopped');
});

test('docker service can restart a container', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $dockerService = app(DockerService::class);
    $success = $dockerService->restartContainer($container);

    expect($success)->toBeTrue();
    
    $container->refresh();
    expect($container->status)->toBe('running');
});

test('docker service can get container logs', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $dockerService = app(DockerService::class);
    $logs = $dockerService->getContainerLogs($container);

    expect($logs)->toContain('Test container logs');
});

test('docker service can check container health', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $dockerService = app(DockerService::class);
    $health = $dockerService->checkContainerHealth($container);

    expect($health)->toHaveKey('status');
    expect($health)->toHaveKey('message');
    expect($health)->toHaveKey('healthy');
    expect($health['status'])->toBe('running');
});

test('docker service can get container stats', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $dockerService = app(DockerService::class);
    $stats = $dockerService->getContainerStats($container);

    expect($stats)->toHaveKey('status');
    expect($stats)->toHaveKey('cpu_usage');
    expect($stats)->toHaveKey('memory_usage');
    expect($stats)->toHaveKey('uptime');
});

test('docker service can get all running containers', function () {
    $dockerService = app(DockerService::class);
    $containers = $dockerService->getAllRunningContainers();

    expect($containers)->toBeArray();
    expect($containers)->toHaveCount(1);
    expect($containers[0])->toHaveKey('id');
    expect($containers[0])->toHaveKey('name');
    expect($containers[0])->toHaveKey('status');
});

test('docker service can cleanup old resources', function () {
    $dockerService = app(DockerService::class);
    $result = $dockerService->cleanupOldResources();

    expect($result)->toHaveKey('containers');
    expect($result)->toHaveKey('images');
    expect($result)->toHaveKey('errors');
});

test('deployment controller can deploy a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'generated_code' => '<html><body><h1>Test Project</h1></body></html>'
    ]);

    $response = $this->actingAs($user)
        ->postJson("/projects/{$project->id}/deploy");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Project deployed successfully!'
    ]);
});

test('deployment controller can get deployment status', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $response = $this->actingAs($user)
        ->getJson("/projects/{$project->id}/deployment/status");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'status',
        'url',
        'health' => ['status', 'message', 'healthy'],
        'stats' => ['status', 'cpu_usage', 'memory_usage', 'uptime']
    ]);
});

test('deployment controller can restart a container', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $response = $this->actingAs($user)
        ->postJson("/projects/{$project->id}/deployment/restart");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Container restarted successfully!'
    ]);
});

test('deployment controller can stop a container', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $response = $this->actingAs($user)
        ->postJson("/projects/{$project->id}/deployment/stop");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Container stopped successfully!'
    ]);
});

test('deployment controller can get container logs', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $user->id,
        'status' => 'ready'
    ]);
    
    $container = Container::factory()->create([
        'project_id' => $project->id,
        'status' => 'running',
        'container_id' => 'test-container-123'
    ]);

    $response = $this->actingAs($user)
        ->getJson("/projects/{$project->id}/deployment/logs");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true
    ]);
    $response->assertJsonStructure([
        'success',
        'logs'
    ]);
});

test('deployment controller can get all containers', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->getJson('/api/containers');

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true
    ]);
    $response->assertJsonStructure([
        'success',
        'containers' => [
            '*' => ['id', 'name', 'status']
        ]
    ]);
});

test('deployment controller can cleanup docker resources', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/docker/cleanup');

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Cleanup completed successfully'
    ]);
    $response->assertJsonStructure([
        'success',
        'message',
        'result' => ['containers', 'images', 'errors']
    ]);
});

test('deployment controller requires authentication', function () {
    $project = Project::factory()->create();

    $response = $this->postJson("/projects/{$project->id}/deploy");
    $response->assertUnauthorized();

    $response = $this->getJson("/projects/{$project->id}/deployment/status");
    $response->assertUnauthorized();

    $response = $this->postJson("/projects/{$project->id}/deployment/restart");
    $response->assertUnauthorized();

    $response = $this->postJson("/projects/{$project->id}/deployment/stop");
    $response->assertUnauthorized();

    $response = $this->getJson("/projects/{$project->id}/deployment/logs");
    $response->assertUnauthorized();
});

test('deployment controller enforces project ownership', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $otherUser->id
    ]);

    // Act as the first user (not the project owner)
    $this->actingAs($user);

    $response = $this->postJson("/projects/{$project->id}/deploy");
    $response->assertForbidden();

    $response = $this->getJson("/projects/{$project->id}/deployment/status");
    $response->assertForbidden();

    $response = $this->postJson("/projects/{$project->id}/deployment/restart");
    $response->assertForbidden();

    $response = $this->postJson("/projects/{$project->id}/deployment/stop");
    $response->assertForbidden();

    $response = $this->getJson("/projects/{$project->id}/deployment/logs");
    $response->assertForbidden();
});
