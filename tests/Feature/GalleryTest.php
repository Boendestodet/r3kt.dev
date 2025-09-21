<?php

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('guests can view public gallery', function () {
    // Create some public projects
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'views_count' => 10
    ]);
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'views_count' => 5
    ]);
    Project::factory()->create([
        'is_public' => false,
        'status' => 'ready'
    ]);

    // Act as guest
    Auth::logout();

    $response = $this->get('/gallery');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => 
        $page->component('gallery/Index')
            ->has('projects.data', 2) // Only public projects
            ->has('featured')
    );
});

test('gallery shows only public and ready projects', function () {
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready'
    ]);
    Project::factory()->create([
        'is_public' => true,
        'status' => 'draft' // Not ready
    ]);
    Project::factory()->create([
        'is_public' => false,
        'status' => 'ready' // Not public
    ]);

    $response = $this->get('/gallery');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => 
        $page->component('gallery/Index')
            ->has('projects.data', 1) // Only public and ready
    );
});

test('gallery supports search functionality', function () {
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'name' => 'Portfolio Website',
        'description' => 'A beautiful portfolio'
    ]);
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'name' => 'E-commerce Store',
        'description' => 'Online shopping site'
    ]);

    $response = $this->get('/gallery?search=portfolio');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => 
        $page->component('gallery/Index')
            ->has('projects.data', 1)
            ->where('projects.data.0.name', 'Portfolio Website')
    );
});

test('gallery supports category filtering', function () {
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'name' => 'My Portfolio',
        'description' => 'Personal portfolio website'
    ]);
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'name' => 'Online Shop',
        'description' => 'E-commerce store'
    ]);

    $response = $this->get('/gallery?category=portfolio');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => 
        $page->component('gallery/Index')
            ->has('projects.data', 1)
            ->where('projects.data.0.name', 'My Portfolio')
    );
});

test('gallery supports sorting', function () {
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'name' => 'Project A',
        'created_at' => now()->subDays(2)
    ]);
    Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'name' => 'Project B',
        'created_at' => now()->subDay()
    ]);

    $response = $this->get('/gallery?sort=oldest');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => 
        $page->component('gallery/Index')
            ->has('projects.data', 2)
            ->where('projects.data.0.name', 'Project A')
    );
});

test('guests can view public project details', function () {
    $project = Project::factory()->create([
        'is_public' => true,
        'status' => 'ready',
        'views_count' => 5
    ]);

    // Act as guest
    Auth::logout();

    $response = $this->get("/gallery/{$project->id}");

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => 
        $page->component('gallery/Show')
            ->has('project')
            ->where('project.id', $project->id)
    );

    // Check that view count was incremented
    $project->refresh();
    expect($project->views_count)->toBe(6);
});

test('guests cannot view private project details', function () {
    $project = Project::factory()->create([
        'is_public' => false,
        'status' => 'ready'
    ]);

    // Act as guest
    Auth::logout();

    $response = $this->get("/gallery/{$project->id}");

    $response->assertNotFound();
});

test('user can toggle project public status', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'is_public' => false
    ]);

    $response = $this->postJson("/projects/{$project->id}/toggle-public");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'is_public' => true,
        'message' => 'Project is now public!'
    ]);

    $project->refresh();
    expect($project->is_public)->toBe(1);
});

test('user cannot toggle other users project public status', function () {
    $otherUser = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $otherUser->id,
        'is_public' => false
    ]);

    $response = $this->postJson("/projects/{$project->id}/toggle-public");

    $response->assertForbidden();
});

test('gallery stats endpoint works', function () {
    Project::factory()->create([
        'is_public' => true,
        'views_count' => 10
    ]);
    Project::factory()->create([
        'is_public' => true,
        'views_count' => 5
    ]);
    Project::factory()->create([
        'is_public' => false,
        'views_count' => 3
    ]);

    $response = $this->getJson('/api/gallery/stats');

    $response->assertSuccessful();
    $response->assertJson([
        'total_public_projects' => 2,
        'total_views' => 15
    ]);
    $response->assertJsonStructure([
        'total_public_projects',
        'total_views',
        'most_popular'
    ]);
});