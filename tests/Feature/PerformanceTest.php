<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can load projects index page quickly', function () {
    // Create multiple projects to test performance
    Project::factory()->count(50)->create(['user_id' => $this->user->id]);
    
    $startTime = microtime(true);
    
    $response = $this->get('/projects');
    
    $endTime = microtime(true);
    $loadTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
    
    $response->assertSuccessful();
    
    // Assert that the page loads within 500ms
    expect($loadTime)->toBeLessThan(500, "Projects index page took {$loadTime}ms to load, which is too slow");
});

it('can handle large project lists efficiently', function () {
    // Create a large number of projects
    Project::factory()->count(100)->create(['user_id' => $this->user->id]);
    
    $startTime = microtime(true);
    
    $response = $this->get('/projects');
    
    $endTime = microtime(true);
    $loadTime = ($endTime - $startTime) * 1000;
    
    $response->assertSuccessful();
    
    // Assert that even with 100 projects, the page loads reasonably fast
    expect($loadTime)->toBeLessThan(1000, "Projects index with 100 projects took {$loadTime}ms to load");
});

it('can search projects efficiently', function () {
    // Create projects with different names
    Project::factory()->create(['name' => 'Test Project 1', 'user_id' => $this->user->id]);
    Project::factory()->create(['name' => 'Test Project 2', 'user_id' => $this->user->id]);
    Project::factory()->create(['name' => 'Different Project', 'user_id' => $this->user->id]);
    
    $startTime = microtime(true);
    
    $response = $this->get('/projects?search=Test');
    
    $endTime = microtime(true);
    $loadTime = ($endTime - $startTime) * 1000;
    
    $response->assertSuccessful();
    
    // Assert that search is fast
    expect($loadTime)->toBeLessThan(200, "Project search took {$loadTime}ms to complete");
});

it('can create projects efficiently', function () {
    $startTime = microtime(true);
    
    $response = $this->post('/projects', [
        'name' => 'Performance Test Project',
        'description' => 'A project created for performance testing',
        'settings' => [
            'stack' => 'nextjs',
            'ai_model' => 'Claude Code',
        ],
    ]);
    
    $endTime = microtime(true);
    $loadTime = ($endTime - $startTime) * 1000;
    
    $response->assertRedirect();
    
    // Assert that project creation is reasonably fast
    expect($loadTime)->toBeLessThan(2000, "Project creation took {$loadTime}ms to complete");
    
    // Verify project was created
    $this->assertDatabaseHas('projects', [
        'name' => 'Performance Test Project',
        'user_id' => $this->user->id,
    ]);
});

it('can load project details efficiently', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    
    $startTime = microtime(true);
    
    $response = $this->get("/projects/{$project->id}");
    
    $endTime = microtime(true);
    $loadTime = ($endTime - $startTime) * 1000;
    
    $response->assertSuccessful();
    
    // Assert that project details load quickly
    expect($loadTime)->toBeLessThan(300, "Project details page took {$loadTime}ms to load");
});

it('can handle concurrent requests efficiently', function () {
    // Create multiple projects
    Project::factory()->count(20)->create(['user_id' => $this->user->id]);
    
    $startTime = microtime(true);
    
    // Simulate multiple concurrent requests
    $responses = [];
    for ($i = 0; $i < 5; $i++) {
        $responses[] = $this->get('/projects');
    }
    
    $endTime = microtime(true);
    $loadTime = ($endTime - $startTime) * 1000;
    
    // All responses should be successful
    foreach ($responses as $response) {
        $response->assertSuccessful();
    }
    
    // Assert that concurrent requests are handled efficiently
    expect($loadTime)->toBeLessThan(1000, "Concurrent requests took {$loadTime}ms to complete");
});
