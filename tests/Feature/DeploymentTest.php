<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Container;
use App\Services\DockerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
});

test('user can deploy their project', function () {
    $response = $this->postJson("/projects/{$this->project->id}/deploy");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Project deployed successfully!'
    ]);

    $this->assertDatabaseHas('containers', [
        'project_id' => $this->project->id,
        'status' => 'running'
    ]);

    $this->project->refresh();
    expect($this->project->status)->toBe('ready');
    expect($this->project->preview_url)->not->toBeNull();
});

test('user can check deployment status', function () {
    // First deploy the project
    $this->postJson("/projects/{$this->project->id}/deploy");

    $response = $this->getJson("/projects/{$this->project->id}/deployment/status");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'status',
        'url',
        'health' => ['status', 'message', 'healthy'],
        'stats' => ['status', 'cpu_usage', 'memory_usage', 'uptime'],
        'last_built_at'
    ]);
});

test('user can stop deployment', function () {
    // First deploy the project
    $this->postJson("/projects/{$this->project->id}/deploy");

    $response = $this->postJson("/projects/{$this->project->id}/deployment/stop");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Container stopped successfully!'
    ]);

    $this->assertDatabaseHas('containers', [
        'project_id' => $this->project->id,
        'status' => 'stopped'
    ]);
});

test('user can restart deployment', function () {
    // First deploy the project
    $this->postJson("/projects/{$this->project->id}/deploy");

    $response = $this->postJson("/projects/{$this->project->id}/deployment/restart");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Container restarted successfully!'
    ]);
});

test('user can view container logs', function () {
    // First deploy the project
    $this->postJson("/projects/{$this->project->id}/deploy");

    $response = $this->getJson("/projects/{$this->project->id}/deployment/logs");

    $response->assertSuccessful();
    $response->assertJsonStructure([
        'success',
        'logs'
    ]);
});

test('user cannot deploy other users project', function () {
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->postJson("/projects/{$otherProject->id}/deploy");

    $response->assertForbidden();
});

test('docker service can deploy project', function () {
    $dockerService = app(DockerService::class);
    
    $container = Container::factory()->create([
        'project_id' => $this->project->id,
        'status' => 'starting'
    ]);

    $result = $dockerService->deployProject($this->project);

    expect($result)->toBeTrue();
    
    $this->assertDatabaseHas('containers', [
        'project_id' => $this->project->id,
        'status' => 'running'
    ]);
});

test('docker service can check container health', function () {
    $dockerService = app(DockerService::class);
    
    $container = Container::factory()->create([
        'project_id' => $this->project->id,
        'container_id' => 'test-container-id'
    ]);

    $health = $dockerService->checkContainerHealth($container);

    expect($health)->toBeArray();
    expect($health)->toHaveKey('status');
    expect($health)->toHaveKey('message');
});

test('docker service can get container logs', function () {
    $dockerService = app(DockerService::class);
    
    $container = Container::factory()->create([
        'project_id' => $this->project->id,
        'container_id' => 'test-container-id'
    ]);

    $logs = $dockerService->getContainerLogs($container);

    expect($logs)->toBeString();
});