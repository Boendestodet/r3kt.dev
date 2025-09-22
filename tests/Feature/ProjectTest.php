<?php

use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('user can view projects index', function () {
    $response = $this->get(route('projects.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/Index'));
});

test('user can create a project', function () {
    $projectData = [
        'name' => 'Test Project',
        'description' => 'A test project description',
    ];

    $response = $this->post(route('projects.store'), $projectData);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'name' => 'Test Project',
        'description' => 'A test project description',
        'user_id' => $this->user->id,
    ]);
});

test('user can view their project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->get(route('projects.show', $project));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('projects/Sandbox'));
});

test('user can update their project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $updateData = [
        'name' => 'Updated Project Name',
        'description' => 'Updated description',
    ];

    $response = $this->put(route('projects.update', $project), $updateData);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'name' => 'Updated Project Name',
        'description' => 'Updated description',
    ]);
});

test('user can delete their project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->delete(route('projects.destroy', $project));

    $response->assertRedirect();
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test('user cannot view other users projects', function () {
    $otherUser = User::factory()->create();
    $project = Project::factory()->create([
        'user_id' => $otherUser->id,
        'is_public' => false
    ]);

    $response = $this->get(route('projects.show', $project));

    $response->assertForbidden();
});

test('user can duplicate a project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->post(route('projects.duplicate', $project));

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'name' => $project->name . ' (Copy)',
        'user_id' => $this->user->id,
    ]);
});
