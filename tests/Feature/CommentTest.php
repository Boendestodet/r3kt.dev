<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    $this->actingAs($this->user);
});

test('user can view comments for their project', function () {
    Comment::factory()->create([
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
        'content' => 'This is a test comment',
    ]);

    $response = $this->getJson("/projects/{$this->project->id}/comments");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'comments' => [
            [
                'content' => 'This is a test comment',
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ]
            ]
        ]
    ]);
});

test('user can create a comment', function () {
    $response = $this->postJson("/projects/{$this->project->id}/comments", [
        'content' => 'This is a new comment',
        'type' => 'general',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'comment' => [
            'content' => 'This is a new comment',
            'type' => 'general',
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]
        ]
    ]);

    $this->assertDatabaseHas('comments', [
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
        'content' => 'This is a new comment',
        'type' => 'general',
    ]);
});

test('user can create a reply to a comment', function () {
    $parentComment = Comment::factory()->create([
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->postJson("/projects/{$this->project->id}/comments", [
        'content' => 'This is a reply',
        'type' => 'general',
        'parent_id' => $parentComment->id,
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'comment' => [
            'content' => 'This is a reply',
            'parent_id' => $parentComment->id,
        ]
    ]);

    $this->assertDatabaseHas('comments', [
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
        'content' => 'This is a reply',
        'parent_id' => $parentComment->id,
    ]);
});

test('user can update their own comment', function () {
    $comment = Comment::factory()->create([
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
        'content' => 'Original content',
    ]);

    $response = $this->putJson("/comments/{$comment->id}", [
        'content' => 'Updated content',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'comment' => [
            'content' => 'Updated content',
        ]
    ]);

    $comment->refresh();
    expect($comment->content)->toBe('Updated content');
});

test('user can delete their own comment', function () {
    $comment = Comment::factory()->create([
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
    ]);

    $response = $this->deleteJson("/comments/{$comment->id}");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Comment deleted successfully'
    ]);

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('user can toggle comment resolved status', function () {
    $comment = Comment::factory()->create([
        'project_id' => $this->project->id,
        'user_id' => $this->user->id,
        'is_resolved' => false,
    ]);

    $response = $this->postJson("/comments/{$comment->id}/toggle-resolved");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'comment' => [
            'is_resolved' => true,
        ]
    ]);

    $comment->refresh();
    expect($comment->is_resolved)->toBeTrue();
});

test('user cannot update other users comment on different project', function () {
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
    $comment = Comment::factory()->create([
        'project_id' => $otherProject->id,
        'user_id' => $otherUser->id,
    ]);

    $response = $this->putJson("/comments/{$comment->id}", [
        'content' => 'Updated content',
    ]);

    $response->assertForbidden();
});

test('user cannot delete other users comment on different project', function () {
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
    $comment = Comment::factory()->create([
        'project_id' => $otherProject->id,
        'user_id' => $otherUser->id,
    ]);

    $response = $this->deleteJson("/comments/{$comment->id}");

    $response->assertForbidden();
});

test('project owner can update any comment on their project', function () {
    $otherUser = User::factory()->create();
    $comment = Comment::factory()->create([
        'project_id' => $this->project->id,
        'user_id' => $otherUser->id,
    ]);

    $response = $this->putJson("/comments/{$comment->id}", [
        'content' => 'Updated by project owner',
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'comment' => [
            'content' => 'Updated by project owner',
        ]
    ]);
});

test('project owner can delete any comment on their project', function () {
    $otherUser = User::factory()->create();
    $comment = Comment::factory()->create([
        'project_id' => $this->project->id,
        'user_id' => $otherUser->id,
    ]);

    $response = $this->deleteJson("/comments/{$comment->id}");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Comment deleted successfully'
    ]);
});

test('comment validation works', function () {
    $response = $this->postJson("/projects/{$this->project->id}/comments", [
        'content' => '', // Empty content
        'type' => 'invalid_type', // Invalid type
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['content', 'type']);
});

test('comment can have different types', function () {
    $types = ['general', 'code_review', 'suggestion', 'question'];
    
    foreach ($types as $type) {
        $response = $this->postJson("/projects/{$this->project->id}/comments", [
            'content' => "This is a {$type} comment",
            'type' => $type,
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'comment' => [
                'type' => $type,
            ]
        ]);
    }
});