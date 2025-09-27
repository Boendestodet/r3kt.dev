<?php

use App\Http\Controllers\NextJSController;
use App\Http\Controllers\SvelteKitController;
use App\Http\Controllers\ViteReactController;
use App\Http\Controllers\ViteVueController;
use App\Models\Project;
use App\Models\User;
use App\Services\StackControllerFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->factory = app(StackControllerFactory::class);
});

it('returns NextJSController for nextjs project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'nextjs'],
    ]);

    $controller = $this->factory->getController($project);

    expect($controller)->toBeInstanceOf(NextJSController::class);
});

it('returns ViteReactController for vite-react project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-react'],
    ]);

    $controller = $this->factory->getController($project);

    expect($controller)->toBeInstanceOf(ViteReactController::class);
});

it('returns ViteVueController for vite-vue project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-vue'],
    ]);

    $controller = $this->factory->getController($project);

    expect($controller)->toBeInstanceOf(ViteVueController::class);
});

it('returns SvelteKitController for sveltekit project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'sveltekit'],
    ]);

    $controller = $this->factory->getController($project);

    expect($controller)->toBeInstanceOf(SvelteKitController::class);
});

it('returns NextJSController as default for unknown project type', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'unknown-stack'],
    ]);

    $controller = $this->factory->getController($project);

    expect($controller)->toBeInstanceOf(NextJSController::class);
});

it('returns NextJSController for project without stack setting', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => [],
    ]);

    $controller = $this->factory->getController($project);

    expect($controller)->toBeInstanceOf(NextJSController::class);
});

it('can get controller by type string', function () {
    $nextjsController = $this->factory->getControllerByType('nextjs');
    $viteReactController = $this->factory->getControllerByType('vite-react');
    $viteVueController = $this->factory->getControllerByType('vite-vue');
    $svelteKitController = $this->factory->getControllerByType('sveltekit');
    $defaultController = $this->factory->getControllerByType('unknown');

    expect($nextjsController)->toBeInstanceOf(NextJSController::class);
    expect($viteReactController)->toBeInstanceOf(ViteReactController::class);
    expect($viteVueController)->toBeInstanceOf(ViteVueController::class);
    expect($svelteKitController)->toBeInstanceOf(SvelteKitController::class);
    expect($defaultController)->toBeInstanceOf(NextJSController::class);
});

it('correctly maps stack names to project types', function () {
    $testCases = [
        'nextjs' => 'nextjs',
        'next.js' => 'nextjs',
        'next' => 'nextjs',
        'vite-react' => 'vite-react',
        'vite + react' => 'vite-react',
        'vite' => 'vite-react',
        'vite-vue' => 'vite-vue',
        'vite + vue' => 'vite-vue',
        'sveltekit' => 'sveltekit',
        'svelte + kit' => 'sveltekit',
        'svelte' => 'sveltekit',
        'unknown' => 'nextjs', // Should default to nextjs
    ];

    foreach ($testCases as $input => $expected) {
        $project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => $input],
        ]);

        $projectType = $this->factory->getProjectType($project);
        expect($projectType)->toBe($expected, "Failed for input: '{$input}'");
    }
});

it('can check if project is of specific type', function () {
    $nextjsProject = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'nextjs'],
    ]);

    $viteReactProject = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-react'],
    ]);

    $viteVueProject = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-vue'],
    ]);

    $svelteKitProject = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'sveltekit'],
    ]);

    // Test Next.js project
    expect($this->factory->isProjectType($nextjsProject, 'nextjs'))->toBeTrue();
    expect($this->factory->isProjectType($nextjsProject, 'vite-react'))->toBeFalse();
    expect($this->factory->isProjectType($nextjsProject, 'vite-vue'))->toBeFalse();
    expect($this->factory->isProjectType($nextjsProject, 'sveltekit'))->toBeFalse();

    // Test Vite React project
    expect($this->factory->isProjectType($viteReactProject, 'nextjs'))->toBeFalse();
    expect($this->factory->isProjectType($viteReactProject, 'vite-react'))->toBeTrue();
    expect($this->factory->isProjectType($viteReactProject, 'vite-vue'))->toBeFalse();
    expect($this->factory->isProjectType($viteReactProject, 'sveltekit'))->toBeFalse();

    // Test Vite Vue project
    expect($this->factory->isProjectType($viteVueProject, 'nextjs'))->toBeFalse();
    expect($this->factory->isProjectType($viteVueProject, 'vite-react'))->toBeFalse();
    expect($this->factory->isProjectType($viteVueProject, 'vite-vue'))->toBeTrue();
    expect($this->factory->isProjectType($viteVueProject, 'sveltekit'))->toBeFalse();

    // Test SvelteKit project
    expect($this->factory->isProjectType($svelteKitProject, 'nextjs'))->toBeFalse();
    expect($this->factory->isProjectType($svelteKitProject, 'vite-react'))->toBeFalse();
    expect($this->factory->isProjectType($svelteKitProject, 'vite-vue'))->toBeFalse();
    expect($this->factory->isProjectType($svelteKitProject, 'sveltekit'))->toBeTrue();
});

it('handles case-insensitive stack name matching', function () {
    $testCases = [
        'NEXTJS' => 'nextjs',
        'Next.js' => 'nextjs',
        'VITE-REACT' => 'vite-react',
        'Vite + React' => 'vite-react',
        'VITE-VUE' => 'vite-vue',
        'Vite + Vue' => 'vite-vue',
        'SVELTEKIT' => 'sveltekit',
        'Svelte + Kit' => 'sveltekit',
    ];

    foreach ($testCases as $input => $expected) {
        $project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => $input],
        ]);

        $projectType = $this->factory->getProjectType($project);
        expect($projectType)->toBe($expected, "Failed for input: '{$input}'");
    }
});

it('handles projects with null settings', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => null,
    ]);

    $controller = $this->factory->getController($project);
    expect($controller)->toBeInstanceOf(NextJSController::class);

    $projectType = $this->factory->getProjectType($project);
    expect($projectType)->toBe('nextjs');
});

it('handles projects with empty settings array', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => [],
    ]);

    $controller = $this->factory->getController($project);
    expect($controller)->toBeInstanceOf(NextJSController::class);

    $projectType = $this->factory->getProjectType($project);
    expect($projectType)->toBe('nextjs');
});
