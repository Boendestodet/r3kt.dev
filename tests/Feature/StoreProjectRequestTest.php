<?php

use App\Http\Requests\StoreProjectRequest;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('validates required name field', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

it('validates name is string', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 123
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

it('validates name max length', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => str_repeat('a', 256)
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

it('validates name uniqueness per user', function () {
    // Create existing project for the user
    Project::factory()->create([
        'name' => 'Existing Project',
        'user_id' => $this->user->id
    ]);

    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Existing Project'
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('name'))->toBeTrue();
});

it('allows same name for different users', function () {
    $otherUser = User::factory()->create();
    
    // Create project for another user
    Project::factory()->create([
        'name' => 'Shared Name',
        'user_id' => $otherUser->id
    ]);

    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Shared Name'
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('validates description is nullable', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Test Project'
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('validates description is string when provided', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Test Project',
        'description' => 123
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('description'))->toBeTrue();
});

it('validates description max length', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Test Project',
        'description' => str_repeat('a', 1001)
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('description'))->toBeTrue();
});

it('validates settings is array when provided', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Test Project',
        'settings' => 'invalid-settings'
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('settings'))->toBeTrue();
});

it('validates settings is nullable', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Test Project'
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('validates valid array settings', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Test Project',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'Next.js',
            'auto_deploy' => true
        ]
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('validates nested array settings', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $validator = Validator::make([
        'name' => 'Test Project',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'Next.js',
            'custom_config' => [
                'port' => 3000,
                'environment' => 'development'
            ]
        ]
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('authorizes request for authenticated users', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    expect($request->authorize())->toBeTrue();
});

it('authorizes request for unauthenticated users', function () {
    auth()->logout();

    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    expect($request->authorize())->toBeTrue();
});

it('has correct validation rules', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $rules = $request->rules();

    expect($rules)->toHaveKey('name');
    expect($rules)->toHaveKey('description');
    expect($rules)->toHaveKey('settings');
    
    expect($rules['name'])->toContain('required');
    expect($rules['name'])->toContain('string');
    expect($rules['name'])->toContain('max:255');
    
    expect($rules['description'])->toContain('nullable');
    expect($rules['description'])->toContain('string');
    expect($rules['description'])->toContain('max:1000');
    
    expect($rules['settings'])->toContain('nullable');
    expect($rules['settings'])->toContain('array');
});

it('has correct custom error messages', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $messages = $request->messages();

    expect($messages)->toHaveKey('name.required');
    expect($messages)->toHaveKey('name.max');
    expect($messages)->toHaveKey('name.unique');
    expect($messages)->toHaveKey('description.max');

    expect($messages['name.required'])->toBe('Project name is required.');
    expect($messages['name.max'])->toBe('Project name must not exceed 255 characters.');
    expect($messages['name.unique'])->toBe('A project with this name already exists. Please choose a different name.');
    expect($messages['description.max'])->toBe('Description must not exceed 1000 characters.');
});

it('validates complex project data', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    $complexData = [
        'name' => 'Complex Test Project',
        'description' => 'A project with complex settings and configuration',
        'settings' => [
            'ai_model' => 'Claude Code',
            'stack' => 'Next.js',
            'auto_deploy' => true,
            'custom_config' => [
                'port' => 3000,
                'environment' => 'development',
                'features' => ['typescript', 'tailwind', 'eslint']
            ],
            'deployment' => [
                'platform' => 'vercel',
                'region' => 'us-east-1'
            ]
        ]
    ];

    $validator = Validator::make($complexData, $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('validates edge case project names', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    // Test minimum valid name
    $validator = Validator::make([
        'name' => 'A'
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();

    // Test maximum valid name
    $validator = Validator::make([
        'name' => str_repeat('a', 255)
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});

it('validates edge case descriptions', function () {
    $request = new StoreProjectRequest();
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    // Test maximum valid description
    $validator = Validator::make([
        'name' => 'Test Project',
        'description' => str_repeat('a', 1000)
    ], $request->rules(), $request->messages());

    expect($validator->fails())->toBeFalse();
});