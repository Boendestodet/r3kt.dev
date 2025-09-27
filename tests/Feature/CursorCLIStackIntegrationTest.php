<?php

use App\Models\Project;
use App\Models\Prompt;
use App\Models\User;
use App\Services\AIWebsiteGenerator;
use App\Services\CursorAIService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('cursor cli works with nextjs stack', function () {
    $cursorAIService = Mockery::mock(CursorAIService::class);
    
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('generateWebsite')
        ->with('Create a Next.js blog', 'nextjs')
        ->andReturn([
            'project' => [
                'package.json' => '{"name": "nextjs-blog", "dependencies": {"next": "^14.0.0", "react": "^18.0.0"}}',
                'app/page.tsx' => 'export default function Home() { return <div>Blog Home</div>; }',
                'app/layout.tsx' => 'export default function RootLayout({ children }) { return <html><body>{children}</body></html>; }',
                'next.config.js' => 'module.exports = {}',
                'tsconfig.json' => '{"compilerOptions": {"target": "es5"}}',
            ],
            'tokens_used' => 200,
            'model' => 'cursor-cli',
        ]);

    $this->app->instance(CursorAIService::class, $cursorAIService);

    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'nextjs'],
    ]);
    
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a Next.js blog',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($project->status)->toBe('ready');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
    
    $generatedCode = json_decode($project->generated_code, true);
    expect($generatedCode)->toHaveKey('package.json');
    expect($generatedCode)->toHaveKey('app/page.tsx');
    expect($generatedCode)->toHaveKey('app/layout.tsx');
});

test('cursor cli works with vite-react stack', function () {
    $cursorAIService = Mockery::mock(CursorAIService::class);
    
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('generateWebsite')
        ->with('Create a Vite React app', 'vite-react')
        ->andReturn([
            'project' => [
                'package.json' => '{"name": "vite-react-app", "dependencies": {"react": "^18.0.0", "vite": "^4.0.0"}}',
                'src/App.tsx' => 'export default function App() { return <div>Vite React App</div>; }',
                'src/main.tsx' => 'import React from "react"; import App from "./App";',
                'index.html' => '<html><body><div id="root"></div></html>',
                'vite.config.ts' => 'import { defineConfig } from "vite"; export default defineConfig({});',
            ],
            'tokens_used' => 180,
            'model' => 'cursor-cli',
        ]);

    $this->app->instance(CursorAIService::class, $cursorAIService);

    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite-react'],
    ]);
    
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a Vite React app',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($project->status)->toBe('ready');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
    
    $generatedCode = json_decode($project->generated_code, true);
    expect($generatedCode)->toHaveKey('package.json');
    expect($generatedCode)->toHaveKey('src/App.tsx');
    expect($generatedCode)->toHaveKey('vite.config.ts');
});

test('cursor cli works with vite-vue stack', function () {
    $cursorAIService = Mockery::mock(CursorAIService::class);
    
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('generateWebsite')
        ->with('Create a Vite Vue app', 'vite-vue')
        ->andReturn([
            'project' => [
                'package.json' => '{"name": "vite-vue-app", "dependencies": {"vue": "^3.0.0", "vite": "^4.0.0"}}',
                'src/App.vue' => '<template><div>Vite Vue App</div></template>',
                'src/main.js' => 'import { createApp } from "vue"; import App from "./App.vue";',
                'index.html' => '<html><body><div id="app"></div></html>',
                'vite.config.js' => 'import { defineConfig } from "vite"; export default defineConfig({});',
            ],
            'tokens_used' => 160,
            'model' => 'cursor-cli',
        ]);

    $this->app->instance(CursorAIService::class, $cursorAIService);

    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'vite + vue'],
    ]);
    
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a Vite Vue app',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($project->status)->toBe('ready');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
    
    $generatedCode = json_decode($project->generated_code, true);
    expect($generatedCode)->toHaveKey('package.json');
    expect($generatedCode)->toHaveKey('src/App.vue');
    expect($generatedCode)->toHaveKey('vite.config.js');
});

test('cursor cli works with sveltekit stack', function () {
    $cursorAIService = Mockery::mock(CursorAIService::class);
    
    $cursorAIService->shouldReceive('isConfigured')->andReturn(true);
    $cursorAIService->shouldReceive('generateWebsite')
        ->with('Create a SvelteKit app', 'sveltekit')
        ->andReturn([
            'project' => [
                'package.json' => '{"name": "sveltekit-app", "dependencies": {"@sveltejs/kit": "^1.0.0", "svelte": "^4.0.0"}}',
                'src/routes/+page.svelte' => '<h1>SvelteKit App</h1>',
                'src/app.html' => '<!DOCTYPE html><html><body>%sveltekit.body%</body></html>',
                'src/app.css' => 'body { margin: 0; }',
                'svelte.config.js' => 'import adapter from "@sveltejs/adapter-auto"; export default { kit: { adapter: adapter() } };',
            ],
            'tokens_used' => 170,
            'model' => 'cursor-cli',
        ]);

    $this->app->instance(CursorAIService::class, $cursorAIService);

    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'settings' => ['stack' => 'sveltekit'],
    ]);
    
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Create a SvelteKit app',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($project->status)->toBe('ready');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
    
    $generatedCode = json_decode($project->generated_code, true);
    expect($generatedCode)->toHaveKey('package.json');
    expect($generatedCode)->toHaveKey('src/routes/+page.svelte');
    expect($generatedCode)->toHaveKey('svelte.config.js');
});

test('cursor cli follows provider fallback order', function () {
    // Mock all services to simulate the fallback order
    $claudeService = Mockery::mock(\App\Services\ClaudeAIService::class);
    $openaiService = Mockery::mock(\App\Services\OpenAIService::class);
    $geminiService = Mockery::mock(\App\Services\GeminiAIService::class);
    $cursorService = Mockery::mock(CursorAIService::class);

    // All services are configured
    $claudeService->shouldReceive('isConfigured')->andReturn(true);
    $openaiService->shouldReceive('isConfigured')->andReturn(true);
    $geminiService->shouldReceive('isConfigured')->andReturn(true);
    $cursorService->shouldReceive('isConfigured')->andReturn(true);

    // Claude fails, OpenAI fails, Gemini fails, Cursor CLI succeeds
    $claudeService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('Claude API error'));

    $openaiService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('OpenAI API error'));

    $geminiService->shouldReceive('generateWebsite')
        ->once()
        ->andThrow(new Exception('Gemini API error'));

    $cursorService->shouldReceive('generateWebsite')
        ->once()
        ->andReturn([
            'project' => ['package.json' => '{"name": "cursor-success"}'],
            'tokens_used' => 100,
            'model' => 'cursor-cli',
        ]);

    $this->app->instance(\App\Services\ClaudeAIService::class, $claudeService);
    $this->app->instance(\App\Services\OpenAIService::class, $openaiService);
    $this->app->instance(\App\Services\GeminiAIService::class, $geminiService);
    $this->app->instance(CursorAIService::class, $cursorService);

    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $prompt = Prompt::factory()->create([
        'project_id' => $project->id,
        'prompt' => 'Test fallback order',
    ]);

    $generator = app(AIWebsiteGenerator::class);
    $generator->processPrompt($prompt);

    $prompt->refresh();
    $project->refresh();

    expect($prompt->status)->toBe('completed');
    expect($prompt->metadata['ai_provider'])->toBe('cursor-cli');
    expect($project->status)->toBe('ready');
});

test('cursor cli handles installation check correctly', function () {
    $cursorService = app(CursorAIService::class);
    
    // Test that the service can check if Cursor CLI is available
    $isAvailable = $cursorService->isCursorCliAvailable();
    expect($isAvailable)->toBeBool();
    
    // Test that the service reports configuration status
    $isConfigured = $cursorService->isConfigured();
    expect($isConfigured)->toBeBool();
    
    // Test that the service returns the correct model name
    $model = $cursorService->getModel();
    expect($model)->toBe('cursor-cli');
});
