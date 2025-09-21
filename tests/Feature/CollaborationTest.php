<?php

use App\Models\Project;
use App\Models\User;
use App\Services\CollaborationService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    $this->collaborationService = app(CollaborationService::class);
    $this->actingAs($this->user);
});

test('tracks user activity on project', function () {
    $this->collaborationService->trackUserActivity(
        $this->project,
        $this->user,
        'test_action',
        ['test' => 'data']
    );

    $collaborators = $this->collaborationService->getActiveCollaborators($this->project);
    
    expect($collaborators)->toHaveCount(1);
    expect($collaborators[0]['user_id'])->toBe($this->user->id);
    expect($collaborators[0]['action'])->toBe('test_action');
});

test('tracks user joining project', function () {
    $this->collaborationService->userJoined($this->project, $this->user);

    $collaborators = $this->collaborationService->getActiveCollaborators($this->project);
    
    expect($collaborators)->toHaveCount(1);
    expect($collaborators[0]['action'])->toBe('joined');
});

test('tracks user leaving project', function () {
    // First join
    $this->collaborationService->userJoined($this->project, $this->user);
    
    // Then leave
    $this->collaborationService->userLeft($this->project, $this->user);

    $collaborators = $this->collaborationService->getActiveCollaborators($this->project);
    
    expect($collaborators)->toHaveCount(0);
});

test('tracks project updates', function () {
    $changes = ['name' => 'Updated Project Name'];
    
    $this->collaborationService->projectUpdated($this->project, $this->user, $changes);

    $collaborators = $this->collaborationService->getActiveCollaborators($this->project);
    
    expect($collaborators)->toHaveCount(1);
    expect($collaborators[0]['action'])->toBe('project_updated');
    expect($collaborators[0]['data']['changes'])->toBe($changes);
});

test('tracks AI generation start', function () {
    $prompt = 'Create a portfolio website';
    
    $this->collaborationService->aiGenerationStarted($this->project, $this->user, $prompt);

    $collaborators = $this->collaborationService->getActiveCollaborators($this->project);
    
    expect($collaborators)->toHaveCount(1);
    expect($collaborators[0]['action'])->toBe('ai_generation_started');
    expect($collaborators[0]['data']['prompt'])->toBe($prompt);
});

test('tracks AI generation completion', function () {
    $status = 'completed';
    
    $this->collaborationService->aiGenerationCompleted($this->project, $this->user, $status);

    $collaborators = $this->collaborationService->getActiveCollaborators($this->project);
    
    expect($collaborators)->toHaveCount(1);
    expect($collaborators[0]['action'])->toBe('ai_generation_completed');
    expect($collaborators[0]['data']['status'])->toBe($status);
});

test('tracks code editing', function () {
    $section = 'header';
    
    $this->collaborationService->codeEditing($this->project, $this->user, $section);

    $collaborators = $this->collaborationService->getActiveCollaborators($this->project);
    
    expect($collaborators)->toHaveCount(1);
    expect($collaborators[0]['action'])->toBe('code_editing');
    expect($collaborators[0]['data']['section'])->toBe($section);
});

test('returns collaboration history', function () {
    $history = $this->collaborationService->getCollaborationHistory($this->project);
    
    expect($history)->toBeArray();
    expect($history)->toHaveCount(3); // Mock data returns 3 items
    expect($history[0])->toHaveKeys(['user', 'action', 'message', 'timestamp']);
});

test('project show page includes collaboration data', function () {
    $response = $this->get(route('projects.show', $this->project));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => 
        $page->has('activeCollaborators')
            ->has('collaborationHistory')
    );
});
