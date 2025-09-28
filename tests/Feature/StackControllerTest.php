<?php

use App\Http\Controllers\AstroController;
use App\Http\Controllers\ExpressController;
use App\Http\Controllers\FastAPIController;
use App\Http\Controllers\NextJSController;
use App\Http\Controllers\NuxtController;
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

describe('AstroController', function () {
    beforeEach(function () {
        $this->controller = new AstroController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'astro'],
        ]);
    });

    it('returns correct internal port', function () {
        expect($this->controller->getInternalPort())->toBe('4321');
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
        $projectDir = storage_path('app/test-astro-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'src/pages/index.astro' => '<div>Test Page</div>',
            'src/layouts/Layout.astro' => '<html><body><slot /></body></html>',
            'src/components/Header.astro' => '<header>Header</header>',
            'src/components/Footer.astro' => '<footer>Footer</footer>',
            'src/styles/global.css' => 'body { margin: 0; }',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/src/pages/index.astro"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/layouts/Layout.astro"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/components/Header.astro"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/components/Footer.astro"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/styles/global.css"))->toBeTrue();
        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/astro.config.mjs"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('creates basic fallback project', function () {
        $projectDir = storage_path('app/test-astro-fallback');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $this->controller->createBasicFallback($projectDir, $this->project);

        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/pages/index.astro"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/layouts/Layout.astro"))->toBeTrue();
        expect(file_exists("{$projectDir}/astro.config.mjs"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('generates correct system prompt', function () {
        $systemPrompt = $this->controller->getSystemPrompt();

        expect($systemPrompt)->toContain('Astro');
        expect($systemPrompt)->toContain('src/pages/index.astro');
        expect($systemPrompt)->toContain('src/layouts/Layout.astro');
        expect($systemPrompt)->toContain('src/components/Header.astro');
        expect($systemPrompt)->toContain('src/components/Footer.astro');
        expect($systemPrompt)->toContain('src/styles/global.css');
    });

    it('generates correct user prompt', function () {
        $testPrompt = 'Create a portfolio website';
        $userPrompt = $this->controller->getUserPrompt($testPrompt);

        expect($userPrompt)->toContain($testPrompt);
        expect($userPrompt)->toContain('Astro');
    });

    it('returns correct required files', function () {
        $requiredFiles = $this->controller->getRequiredFiles();

        expect($requiredFiles)->toContain('package.json');
        expect($requiredFiles)->toContain('astro.config.mjs');
        expect($requiredFiles)->toContain('tsconfig.json');
        expect($requiredFiles)->toContain('src/pages/index.astro');
        expect($requiredFiles)->toContain('src/layouts/Layout.astro');
        expect($requiredFiles)->toContain('src/components/Header.astro');
        expect($requiredFiles)->toContain('src/components/Footer.astro');
        expect($requiredFiles)->toContain('src/styles/global.css');
        expect($requiredFiles)->toContain('Dockerfile');
    });

    it('detects Astro project correctly', function () {
        expect($this->controller->isAstroProject($this->project))->toBeTrue();

        $nonAstroProject = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'nextjs'],
        ]);

        expect($this->controller->isAstroProject($nonAstroProject))->toBeFalse();
    });

    it('returns correct Docker configuration', function () {
        $dockerConfig = $this->controller->getDockerConfig();

        expect($dockerConfig['port'])->toBe(4321);
        expect($dockerConfig['dev_command'])->toBe('npm run dev');
        expect($dockerConfig['build_command'])->toBe('npm run build');
        expect($dockerConfig['start_command'])->toBe('npm run preview');
    });
});

describe('NuxtController', function () {
    beforeEach(function () {
        $this->controller = new NuxtController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'nuxt3'],
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
        $projectDir = storage_path('app/test-nuxt-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'app.vue' => '<template><div>Test App</div></template>',
            'pages/index.vue' => '<template><div>Test Page</div></template>',
            'components/Header.vue' => '<template><header>Header</header></template>',
            'components/Footer.vue' => '<template><footer>Footer</footer></template>',
            'assets/css/main.css' => 'body { margin: 0; }',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/app.vue"))->toBeTrue();
        expect(file_exists("{$projectDir}/pages/index.vue"))->toBeTrue();
        expect(file_exists("{$projectDir}/components/Header.vue"))->toBeTrue();
        expect(file_exists("{$projectDir}/components/Footer.vue"))->toBeTrue();
        expect(file_exists("{$projectDir}/assets/css/main.css"))->toBeTrue();
        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/nuxt.config.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('creates basic fallback project', function () {
        $projectDir = storage_path('app/test-nuxt-fallback');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $this->controller->createBasicFallback($projectDir, $this->project);

        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/app.vue"))->toBeTrue();
        expect(file_exists("{$projectDir}/pages/index.vue"))->toBeTrue();
        expect(file_exists("{$projectDir}/nuxt.config.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('generates correct system prompt', function () {
        $systemPrompt = $this->controller->getSystemPrompt();

        expect($systemPrompt)->toContain('Nuxt 3');
        expect($systemPrompt)->toContain('app.vue');
        expect($systemPrompt)->toContain('pages/index.vue');
        expect($systemPrompt)->toContain('components/Header.vue');
        expect($systemPrompt)->toContain('components/Footer.vue');
        expect($systemPrompt)->toContain('assets/css/main.css');
    });

    it('generates correct user prompt', function () {
        $testPrompt = 'Create a portfolio website';
        $userPrompt = $this->controller->getUserPrompt($testPrompt);

        expect($userPrompt)->toContain($testPrompt);
        expect($userPrompt)->toContain('Nuxt 3');
    });

    it('returns correct required files', function () {
        $requiredFiles = $this->controller->getRequiredFiles();

        expect($requiredFiles)->toContain('package.json');
        expect($requiredFiles)->toContain('nuxt.config.ts');
        expect($requiredFiles)->toContain('tsconfig.json');
        expect($requiredFiles)->toContain('app.vue');
        expect($requiredFiles)->toContain('pages/index.vue');
        expect($requiredFiles)->toContain('components/Header.vue');
        expect($requiredFiles)->toContain('components/Footer.vue');
        expect($requiredFiles)->toContain('assets/css/main.css');
        expect($requiredFiles)->toContain('Dockerfile');
    });

    it('detects Nuxt project correctly', function () {
        expect($this->controller->isNuxtProject($this->project))->toBeTrue();

        $nonNuxtProject = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'nextjs'],
        ]);

        expect($this->controller->isNuxtProject($nonNuxtProject))->toBeFalse();
    });

    it('returns correct Docker configuration', function () {
        $dockerConfig = $this->controller->getDockerConfig();

        expect($dockerConfig['port'])->toBe(3000);
        expect($dockerConfig['dev_command'])->toBe('npm run dev');
        expect($dockerConfig['build_command'])->toBe('npm run build');
        expect($dockerConfig['start_command'])->toBe('npm run preview');
    });
});

describe('ExpressController', function () {
    beforeEach(function () {
        $this->controller = new ExpressController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'nodejs-express'],
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
        $projectDir = storage_path('app/test-express-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'src/app.ts' => 'import express from "express"; const app = express(); export default app;',
            'src/routes/index.ts' => 'import { Router } from "express"; const router = Router(); export default router;',
            'src/routes/api.ts' => 'import { Router } from "express"; const router = Router(); export default router;',
            'src/middleware/cors.ts' => 'import cors from "cors"; export default cors();',
            'src/middleware/errorHandler.ts' => 'export const errorHandler = (err, req, res, next) => {};',
            'src/types/index.ts' => 'export interface User { id: string; }',
            'src/utils/logger.ts' => 'export const logger = console;',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/src/app.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/routes/index.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/routes/api.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/middleware/cors.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/middleware/errorHandler.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/types/index.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/utils/logger.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/tsconfig.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/.env"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('creates basic fallback project', function () {
        $projectDir = storage_path('app/test-express-fallback');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $this->controller->createBasicFallback($projectDir, $this->project);

        expect(file_exists("{$projectDir}/package.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/src/app.ts"))->toBeTrue();
        expect(file_exists("{$projectDir}/tsconfig.json"))->toBeTrue();
        expect(file_exists("{$projectDir}/.env"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('generates correct system prompt', function () {
        $systemPrompt = $this->controller->getSystemPrompt();

        expect($systemPrompt)->toContain('Express.js');
        expect($systemPrompt)->toContain('src/app.ts');
        expect($systemPrompt)->toContain('src/routes/index.ts');
        expect($systemPrompt)->toContain('src/routes/api.ts');
        expect($systemPrompt)->toContain('src/middleware/cors.ts');
        expect($systemPrompt)->toContain('src/middleware/errorHandler.ts');
        expect($systemPrompt)->toContain('src/types/index.ts');
        expect($systemPrompt)->toContain('src/utils/logger.ts');
    });

    it('generates correct user prompt', function () {
        $testPrompt = 'Create a REST API for user management';
        $userPrompt = $this->controller->getUserPrompt($testPrompt);

        expect($userPrompt)->toContain($testPrompt);
        expect($userPrompt)->toContain('Express.js');
    });

    it('returns correct required files', function () {
        $requiredFiles = $this->controller->getRequiredFiles();

        expect($requiredFiles)->toContain('package.json');
        expect($requiredFiles)->toContain('tsconfig.json');
        expect($requiredFiles)->toContain('.env');
        expect($requiredFiles)->toContain('src/app.ts');
        expect($requiredFiles)->toContain('src/routes/index.ts');
        expect($requiredFiles)->toContain('src/routes/api.ts');
        expect($requiredFiles)->toContain('src/middleware/cors.ts');
        expect($requiredFiles)->toContain('src/middleware/errorHandler.ts');
        expect($requiredFiles)->toContain('src/types/index.ts');
        expect($requiredFiles)->toContain('src/utils/logger.ts');
        expect($requiredFiles)->toContain('Dockerfile');
    });

    it('detects Express project correctly', function () {
        expect($this->controller->isExpressProject($this->project))->toBeTrue();

        $nonExpressProject = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'nextjs'],
        ]);

        expect($this->controller->isExpressProject($nonExpressProject))->toBeFalse();
    });

    it('returns correct Docker configuration', function () {
        $dockerConfig = $this->controller->getDockerConfig();

        expect($dockerConfig['port'])->toBe(3000);
        expect($dockerConfig['dev_command'])->toBe('npm run dev');
        expect($dockerConfig['build_command'])->toBe('npm run build');
        expect($dockerConfig['start_command'])->toBe('npm start');
    });
});

describe('FastAPIController', function () {
    beforeEach(function () {
        $this->controller = new FastAPIController($this->dockerService);
        $this->project = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'python-fastapi'],
        ]);
    });

    it('returns correct internal port', function () {
        expect($this->controller->getInternalPort())->toBe('8000');
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
        $projectDir = storage_path('app/test-fastapi-project');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $projectFiles = [
            'main.py' => 'from fastapi import FastAPI; app = FastAPI();',
            'app/api/routes.py' => 'from fastapi import APIRouter; router = APIRouter();',
            'app/api/dependencies.py' => 'from fastapi import Depends; def get_db(): pass',
            'app/core/config.py' => 'from pydantic_settings import BaseSettings; class Settings(BaseSettings): pass',
            'app/core/security.py' => 'from passlib.context import CryptContext; pwd_context = CryptContext(schemes=["bcrypt"])',
            'app/models/schemas.py' => 'from pydantic import BaseModel; class UserBase(BaseModel): pass',
            'app/services/database.py' => 'from sqlalchemy import create_engine; engine = create_engine("sqlite:///./app.db")',
            'app/utils/logger.py' => 'import logging; logger = logging.getLogger(__name__)',
        ];

        $this->controller->createProjectFiles($projectDir, $projectFiles);

        expect(file_exists("{$projectDir}/main.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/api/routes.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/api/dependencies.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/core/config.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/core/security.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/models/schemas.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/services/database.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/app/utils/logger.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/requirements.txt"))->toBeTrue();
        expect(file_exists("{$projectDir}/pyproject.toml"))->toBeTrue();
        expect(file_exists("{$projectDir}/.env"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('creates basic fallback project', function () {
        $projectDir = storage_path('app/test-fastapi-fallback');

        if (is_dir($projectDir)) {
            exec("rm -rf {$projectDir}");
        }
        mkdir($projectDir, 0755, true);

        $this->controller->createBasicFallback($projectDir, $this->project);

        expect(file_exists("{$projectDir}/main.py"))->toBeTrue();
        expect(file_exists("{$projectDir}/requirements.txt"))->toBeTrue();
        expect(file_exists("{$projectDir}/.env"))->toBeTrue();
        expect(file_exists("{$projectDir}/Dockerfile"))->toBeTrue();

        // Cleanup
        exec("rm -rf {$projectDir}");
    });

    it('generates correct system prompt', function () {
        $systemPrompt = $this->controller->getSystemPrompt();

        expect($systemPrompt)->toContain('FastAPI');
        expect($systemPrompt)->toContain('main.py');
        expect($systemPrompt)->toContain('app/api/routes.py');
        expect($systemPrompt)->toContain('app/api/dependencies.py');
        expect($systemPrompt)->toContain('app/core/config.py');
        expect($systemPrompt)->toContain('app/core/security.py');
        expect($systemPrompt)->toContain('app/models/schemas.py');
        expect($systemPrompt)->toContain('app/services/database.py');
        expect($systemPrompt)->toContain('app/utils/logger.py');
    });

    it('generates correct user prompt', function () {
        $testPrompt = 'Create a REST API for user management with authentication';
        $userPrompt = $this->controller->getUserPrompt($testPrompt);

        expect($userPrompt)->toContain($testPrompt);
        expect($userPrompt)->toContain('FastAPI');
    });

    it('returns correct required files', function () {
        $requiredFiles = $this->controller->getRequiredFiles();

        expect($requiredFiles)->toContain('requirements.txt');
        expect($requiredFiles)->toContain('pyproject.toml');
        expect($requiredFiles)->toContain('.env');
        expect($requiredFiles)->toContain('main.py');
        expect($requiredFiles)->toContain('app/api/routes.py');
        expect($requiredFiles)->toContain('app/api/dependencies.py');
        expect($requiredFiles)->toContain('app/core/config.py');
        expect($requiredFiles)->toContain('app/core/security.py');
        expect($requiredFiles)->toContain('app/models/schemas.py');
        expect($requiredFiles)->toContain('app/services/database.py');
        expect($requiredFiles)->toContain('app/utils/logger.py');
        expect($requiredFiles)->toContain('Dockerfile');
    });

    it('detects FastAPI project correctly', function () {
        expect($this->controller->isFastAPIProject($this->project))->toBeTrue();

        $nonFastAPIProject = Project::factory()->create([
            'user_id' => $this->user->id,
            'settings' => ['stack' => 'nextjs'],
        ]);

        expect($this->controller->isFastAPIProject($nonFastAPIProject))->toBeFalse();
    });

    it('returns correct Docker configuration', function () {
        $dockerConfig = $this->controller->getDockerConfig();

        expect($dockerConfig['port'])->toBe(8000);
        expect($dockerConfig['dev_command'])->toBe('uvicorn main:app --host 0.0.0.0 --port 8000 --reload');
        expect($dockerConfig['build_command'])->toBe('pip install -r requirements.txt');
        expect($dockerConfig['start_command'])->toBe('uvicorn main:app --host 0.0.0.0 --port 8000');
    });
});
