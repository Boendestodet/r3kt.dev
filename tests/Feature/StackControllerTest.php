<?php

use App\Http\Controllers\NextJSController;
use App\Http\Controllers\SvelteKitController;
use App\Http\Controllers\ViteReactController;
use App\Http\Controllers\ViteVueController;
use App\Models\Project;
use App\Models\User;
use App\Services\DockerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->dockerService = app(DockerService::class);
    Storage::fake('local');
});

describe('NextJSController', function () {
    beforeEach(function () {
        $this->controller = new NextJSController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'nextjs'],
        ]);
    });

    it('returns correct internal port', function () {
        expect($this->controller->getInternalPort())->toBe('3000');
    });

    it('has required methods', function () {
        expect(method_exists($this->controller, 'createProjectFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createDockerfile'))->toBeTrue();
        expect(method_exists($this->controller, 'hasRequiredFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createBasicFallback'))->toBeTrue();
        expect(method_exists($this->controller, 'getSystemPrompt'))->toBeTrue();
        expect(method_exists($this->controller, 'getUserPrompt'))->toBeTrue();
    });

    it('creates project files correctly', function () {
        $projectDir = storage_path('app/test-nextjs-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'app/page.tsx' => '<div>Test Page</div>',
            'app/layout.tsx' => '<html><body>{children}</body></html>',
            'app/globals.css' => 'body { margin: 0; }',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/app/page.tsx"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/layout.tsx"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/globals.css"))->toBeTrue();
        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('checks for required files correctly', function () {
        $projectDir = storage_path('app/test-nextjs-required');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        // Initially should not have required files
        expect($this->controller->hasRequiredFiles($projectDir))->toBeFalse();

        // Create required files
        file_put_contents("{$projectDir}/package.json", '{}');
        file_put_contents("{$projectDir}/next.config.js", 'module.exports = {}');
        file_put_contents("{$projectDir}/tsconfig.json", '{}');
        file_put_contents("{$projectDir}/Dockerfile", 'FROM node:18');

        expect($this->controller->hasRequiredFiles($projectDir))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('creates basic fallback project', function () {
        $projectDir = storage_path('app/test-nextjs-fallback');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $this->controller->createBasicFallback($projectDir, $this->project);

        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/page.tsx"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });
});

describe('ViteReactController', function () {
    beforeEach(function () {
        $this->controller = new ViteReactController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'vite-react'],
        ]);
    });

    it('returns correct internal port', function () {
        expect($this->controller->getInternalPort())->toBe('5173');
    });

    it('has required methods', function () {
        expect(method_exists($this->controller, 'createProjectFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createDockerfile'))->toBeTrue();
        expect(method_exists($this->controller, 'hasRequiredFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createBasicFallback'))->toBeTrue();
        expect(method_exists($this->controller, 'getSystemPrompt'))->toBeTrue();
        expect(method_exists($this->controller, 'getUserPrompt'))->toBeTrue();
    });

    it('creates project files correctly', function () {
        $projectDir = storage_path('app/test-vite-react-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'src/App.tsx' => '<div>Test App</div>',
            'src/main.tsx' => 'import React from "react"',
            'src/App.css' => 'body { margin: 0; }',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/src/App.tsx"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/main.tsx"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/App.css"))->toBeTrue();
        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/vite.config.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('checks for required files correctly', function () {
        $projectDir = storage_path('app/test-vite-react-required');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        // Initially should not have required files
        expect($this->controller->hasRequiredFiles($projectDir))->toBeFalse();

        // Create required files
        file_put_contents("{$projectDir}/package.json", '{}');
        file_put_contents("{$projectDir}/vite.config.ts", 'export default {}');
        file_put_contents("{$projectDir}/tsconfig.json", '{}');
        file_put_contents("{$projectDir}/index.html", '<html></html>');
        file_put_contents("{$projectDir}/Dockerfile", 'FROM node:18');

        expect($this->controller->hasRequiredFiles($projectDir))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });
});

describe('ViteVueController', function () {
    beforeEach(function () {
        $this->controller = new ViteVueController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'vite-vue'],
        ]);
    });

    it('returns correct internal port', function () {
        expect($this->controller->getInternalPort())->toBe('5173');
    });

    it('has required methods', function () {
        expect(method_exists($this->controller, 'createProjectFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createDockerfile'))->toBeTrue();
        expect(method_exists($this->controller, 'hasRequiredFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createBasicFallback'))->toBeTrue();
        expect(method_exists($this->controller, 'getSystemPrompt'))->toBeTrue();
        expect(method_exists($this->controller, 'getUserPrompt'))->toBeTrue();
    });

    it('creates project files correctly', function () {
        $projectDir = storage_path('app/test-vite-vue-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'src/App.vue' => '<template><div>Test App</div></template>',
            'src/main.ts' => 'import { createApp } from "vue"',
            'src/style.css' => 'body { margin: 0; }',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/src/App.vue"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/main.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/style.css"))->toBeTrue();
        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/vite.config.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });
});

describe('SvelteKitController', function () {
    beforeEach(function () {
        $this->controller = new SvelteKitController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'sveltekit'],
        ]);
    });

    it('returns correct internal port', function () {
        expect($this->controller->getInternalPort())->toBe('5173');
    });

    it('has required methods', function () {
        expect(method_exists($this->controller, 'createProjectFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createDockerfile'))->toBeTrue();
        expect(method_exists($this->controller, 'hasRequiredFiles'))->toBeTrue();
        expect(method_exists($this->controller, 'createBasicFallback'))->toBeTrue();
        expect(method_exists($this->controller, 'getSystemPrompt'))->toBeTrue();
        expect(method_exists($this->controller, 'getUserPrompt'))->toBeTrue();
    });

    it('creates project files correctly', function () {
        $projectDir = storage_path('app/test-sveltekit-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'src/routes/+page.svelte' => '<div>Test Page</div>',
            'src/app.html' => '<html><body>%sveltekit.body%</body></html>',
            'src/app.css' => 'body { margin: 0; }',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/src/routes/+page.svelte"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/app.html"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/app.css"))->toBeTrue();
        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/svelte.config.js"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });
});

describe('Controller Prompts', function () {
    it('generates appropriate system prompts for each controller', function () {
        $nextjsController = new NextJSController($this->dockerService);
        $viteReactController = new ViteReactController($this->dockerService);
        $viteVueController = new ViteVueController($this->dockerService);
        $svelteKitController = new SvelteKitController($this->dockerService);

        $nextjsPrompt = $nextjsController->getSystemPrompt();
        $viteReactPrompt = $viteReactController->getSystemPrompt();
        $viteVuePrompt = $viteVueController->getSystemPrompt();
        $svelteKitPrompt = $svelteKitController->getSystemPrompt();

        // Each should contain their respective technology
        expect($nextjsPrompt)->toContain('Next.js');
        expect($viteReactPrompt)->toContain('Vite');
        expect($viteReactPrompt)->toContain('React');
        expect($viteVuePrompt)->toContain('Vite');
        expect($viteVuePrompt)->toContain('Vue');
        expect($svelteKitPrompt)->toContain('SvelteKit');

        // Each should be different
        expect($nextjsPrompt)->not->toBe($viteReactPrompt);
        expect($viteReactPrompt)->not->toBe($viteVuePrompt);
        expect($viteVuePrompt)->not->toBe($svelteKitPrompt);
    });

    it('generates appropriate user prompts for each controller', function () {
        $nextjsController = new NextJSController($this->dockerService);
        $viteReactController = new ViteReactController($this->dockerService);
        $viteVueController = new ViteVueController($this->dockerService);
        $svelteKitController = new SvelteKitController($this->dockerService);

        $testPrompt = 'Create a portfolio website';

        $nextjsUserPrompt = $nextjsController->getUserPrompt($testPrompt);
        $viteReactUserPrompt = $viteReactController->getUserPrompt($testPrompt);
        $viteVueUserPrompt = $viteVueController->getUserPrompt($testPrompt);
        $svelteKitUserPrompt = $svelteKitController->getUserPrompt($testPrompt);

        // Each should contain the original prompt
        expect($nextjsUserPrompt)->toContain($testPrompt);
        expect($viteReactUserPrompt)->toContain($testPrompt);
        expect($viteVueUserPrompt)->toContain($testPrompt);
        expect($svelteKitUserPrompt)->toContain($testPrompt);

        // Each should be different
        expect($nextjsUserPrompt)->not->toBe($viteReactUserPrompt);
        expect($viteReactUserPrompt)->not->toBe($viteVueUserPrompt);
        expect($viteVueUserPrompt)->not->toBe($svelteKitUserPrompt);
    });
});
