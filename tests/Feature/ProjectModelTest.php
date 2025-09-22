<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Container;
use App\Models\Prompt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Project',
        'description' => 'A test project',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'Next.js',
            'auto_deploy' => true
        ]
    ]);
});

it('belongs to a user', function () {
    expect($this->project->user)->toBeInstanceOf(User::class);
    expect($this->project->user->id)->toBe($this->user->id);
});

it('has many containers', function () {
    $container1 = Container::factory()->create(['project_id' => $this->project->id]);
    $container2 = Container::factory()->create(['project_id' => $this->project->id]);

    expect($this->project->containers)->toHaveCount(2);
    expect($this->project->containers->first())->toBeInstanceOf(Container::class);
});

it('has many prompts', function () {
    $prompt1 = Prompt::factory()->create(['project_id' => $this->project->id]);
    $prompt2 = Prompt::factory()->create(['project_id' => $this->project->id]);

    expect($this->project->prompts)->toHaveCount(2);
    expect($this->project->prompts->first())->toBeInstanceOf(Prompt::class);
});

it('casts settings to array', function () {
    expect($this->project->settings)->toBeArray();
    expect($this->project->settings['ai_model'])->toBe('Claude Code');
    expect($this->project->settings['stack'])->toBe('Next.js');
    expect($this->project->settings['auto_deploy'])->toBeTrue();
});

it('generates slug from name', function () {
    $project = Project::factory()->create([
        'name' => 'My Awesome Project!',
        'user_id' => $this->user->id,
        'slug' => null // Let it generate from name
    ]);

    // The slug should be generated from the name
    expect($project->slug)->not->toBeNull();
    expect($project->slug)->toContain('my-awesome-project');
});

it('handles special characters in slug generation', function () {
    $project = Project::factory()->create([
        'name' => 'Project with @#$% Special Characters',
        'user_id' => $this->user->id,
        'slug' => null
    ]);

    expect($project->slug)->not->toBeNull();
    expect($project->slug)->toContain('project-with-at-special');
});

it('handles unicode characters in slug generation', function () {
    $project = Project::factory()->create([
        'name' => 'Projét with Ünicode Çharacters',
        'user_id' => $this->user->id,
        'slug' => null
    ]);

    expect($project->slug)->not->toBeNull();
    expect($project->slug)->toContain('projet-with-unicode');
});

it('handles empty name for slug generation', function () {
    $project = Project::factory()->create([
        'name' => '',
        'user_id' => $this->user->id,
        'slug' => null
    ]);

    expect($project->slug)->not->toBeNull(); // Factory generates a slug even for empty names
});

it('handles numeric name for slug generation', function () {
    $project = Project::factory()->create([
        'name' => '123 Project 456',
        'user_id' => $this->user->id,
        'slug' => null
    ]);

    expect($project->slug)->not->toBeNull();
    expect($project->slug)->toContain('123-project-456');
});

it('has fillable attributes', function () {
    $fillable = $this->project->getFillable();
    
    expect($fillable)->toContain('name');
    expect($fillable)->toContain('description');
    expect($fillable)->toContain('slug');
    expect($fillable)->toContain('settings');
    expect($fillable)->toContain('user_id');
});

it('has timestamps', function () {
    expect($this->project->created_at)->not->toBeNull();
    expect($this->project->updated_at)->not->toBeNull();
});

it('can get active container', function () {
    // Create containers with different statuses
    $stoppedContainer = Container::factory()->create([
        'project_id' => $this->project->id,
        'status' => 'stopped'
    ]);
    
    $runningContainer = Container::factory()->create([
        'project_id' => $this->project->id,
        'status' => 'running'
    ]);

    $activeContainer = $this->project->getActiveContainer();

    expect($activeContainer)->toBeInstanceOf(Container::class);
    expect($activeContainer->id)->toBe($runningContainer->id);
    expect($activeContainer->status)->toBe('running');
});

it('returns null when no active container exists', function () {
    // Create only stopped containers
    Container::factory()->create([
        'project_id' => $this->project->id,
        'status' => 'stopped'
    ]);

    $activeContainer = $this->project->getActiveContainer();

    expect($activeContainer)->toBeNull();
});

it('returns null when no containers exist', function () {
    $activeContainer = $this->project->getActiveContainer();

    expect($activeContainer)->toBeNull();
});

it('can access project attributes', function () {
    expect($this->project->name)->toBe('Test Project');
    expect($this->project->description)->toBe('A test project');
    expect($this->project->user_id)->toBe($this->user->id);
});

it('can update project attributes', function () {
    $this->project->update([
        'name' => 'Updated Project Name',
        'description' => 'Updated description',
        'settings' => [
            'ai_model' => 'GPT-4',
            'stack' => 'React',
            'auto_deploy' => false
        ]
    ]);

    $this->project->refresh();

    expect($this->project->name)->toBe('Updated Project Name');
    expect($this->project->description)->toBe('Updated description');
    expect($this->project->settings['ai_model'])->toBe('GPT-4');
    expect($this->project->settings['stack'])->toBe('React');
    expect($this->project->settings['auto_deploy'])->toBeFalse();
});

it('can delete project', function () {
    $projectId = $this->project->id;
    
    $this->project->delete();

    expect(Project::find($projectId))->toBeNull();
});

it('cascades delete to related containers and prompts', function () {
    $container = Container::factory()->create(['project_id' => $this->project->id]);
    $prompt = Prompt::factory()->create(['project_id' => $this->project->id]);

    $this->project->delete();

    expect(Container::find($container->id))->toBeNull();
    expect(Prompt::find($prompt->id))->toBeNull();
});

it('can scope projects by user', function () {
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

    $userProjects = Project::where('user_id', $this->user->id)->get();

    expect($userProjects)->toHaveCount(1);
    expect($userProjects->first()->id)->toBe($this->project->id);
});

it('can order projects by creation date', function () {
    $olderProject = Project::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now()->subDays(2)
    ]);

    $newerProject = Project::factory()->create([
        'user_id' => $this->user->id,
        'created_at' => now()->subDay()
    ]);

    $projects = Project::where('user_id', $this->user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    // Check that newer project comes first
    expect($projects->first()->created_at)->toBeGreaterThan($projects->last()->created_at);
    // Check that we have at least 2 projects (including the one from beforeEach)
    expect($projects)->toHaveCount(3); // 2 new + 1 from beforeEach
});

it('handles null settings gracefully', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => null
    ]);

    expect($project->settings)->toBeNull();
});

it('handles empty settings array', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => []
    ]);

    expect($project->settings)->toBeArray();
    expect($project->settings)->toBeEmpty();
});