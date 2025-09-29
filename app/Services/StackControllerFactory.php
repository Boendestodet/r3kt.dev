<?php

namespace App\Services;

use App\Http\Controllers\AstroController;
use App\Http\Controllers\ExpressController;
use App\Http\Controllers\FastAPIController;
use App\Http\Controllers\NextJSController;
use App\Http\Controllers\NuxtController;
use App\Http\Controllers\SvelteKitController;
use App\Http\Controllers\ViteReactController;
use App\Http\Controllers\ViteVueController;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class StackControllerFactory
{
    /**
     * Get the appropriate stack controller for a project
     */
    public function getController(Project $project): AstroController|ExpressController|FastAPIController|NextJSController|NuxtController|ViteReactController|SvelteKitController|ViteVueController
    {
        $projectType = $this->getProjectType($project);

        return match ($projectType) {
            'astro' => new AstroController(app(DockerService::class)),
            'nodejs-express' => new ExpressController(app(DockerService::class)),
            'python-fastapi' => new FastAPIController(app(DockerService::class)),
            'nuxt3' => new NuxtController(app(DockerService::class)),
            'vite-react' => new ViteReactController(app(DockerService::class)),
            'vite-vue' => new ViteVueController(app(DockerService::class)),
            'sveltekit' => new SvelteKitController(app(DockerService::class)),
            'nextjs' => new NextJSController(app(DockerService::class)),
            default => new NextJSController(app(DockerService::class))
        };
    }

    /**
     * Get the appropriate stack controller by project type string
     */
    public function getControllerByType(string $projectType): AstroController|ExpressController|FastAPIController|NextJSController|NuxtController|ViteReactController|SvelteKitController|ViteVueController
    {
        return match ($projectType) {
            'astro' => new AstroController(app(DockerService::class)),
            'nodejs-express' => new ExpressController(app(DockerService::class)),
            'python-fastapi' => new FastAPIController(app(DockerService::class)),
            'nuxt3' => new NuxtController(app(DockerService::class)),
            'vite-react' => new ViteReactController(app(DockerService::class)),
            'vite-vue' => new ViteVueController(app(DockerService::class)),
            'sveltekit' => new SvelteKitController(app(DockerService::class)),
            'nextjs' => new NextJSController(app(DockerService::class)),
            default => new NextJSController(app(DockerService::class))
        };
    }

    /**
     * Get project type from project settings
     */
    public function getProjectType(Project $project): string
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        Log::info('Determining project type', [
            'project_id' => $project->id,
            'stack' => $stack,
            'stack_type' => gettype($stack),
            'stack_length' => strlen($stack),
            'settings' => $settings,
        ]);

        // Normalize and map stack names with flexible matching
        // Order matters - more specific patterns first
        $stackMappings = [
            'astro' => ['astro', 'astro + typescript', 'astro typescript'],
            'nodejs-express' => ['nodejs-express', 'node.js + express', 'nodejs + express', 'express', 'express.js'],
            'python-fastapi' => ['python-fastapi', 'python + fastapi', 'python + fastapi + async', 'fastapi', 'fastapi + async'],
            'nuxt3' => ['nuxt3', 'nuxt 3', 'nuxt + typescript', 'nuxt typescript'],
            'vite-vue' => ['vite-vue', 'vite + vue', 'vite + vue + typescript', 'vite vue'],
            'vite-react' => ['vite-react', 'vite + react', 'vite + react + typescript'],
            'nextjs' => ['next.js', 'nextjs + react', 'nextjs', 'next'],
            'sveltekit' => ['svelte + kit', 'sveltekit', 'svelte'],
            'vite' => ['vite'], // Generic vite fallback
        ];

        // Check for exact matches first, then partial matches
        foreach ($stackMappings as $type => $patterns) {
            foreach ($patterns as $pattern) {
                // For specific patterns like 'vite-vue', 'vite-react', use exact matching
                if (str_contains($pattern, '-') && $stack === $pattern) {
                    Log::info("Detected {$type} project type from exact stack match: {$stack}");
                    return $type;
                }
                
                // For other patterns, use partial matching
                if (str_contains($stack, $pattern)) {
                    Log::info("Detected {$type} project type from stack: {$stack}");

                    // If it's just 'vite', default to 'vite-react'
                    if ($type === 'vite') {
                        return 'vite-react';
                    }

                    return $type;
                }
            }
        }

        // Log unknown stack and default to Next.js for backward compatibility
        Log::warning("Unknown stack type: '{$stack}', defaulting to nextjs", [
            'project_id' => $project->id,
            'stack' => $stack,
            'settings' => $settings,
        ]);

        return 'nextjs';
    }

    /**
     * Get system prompt for a project
     */
    public function getSystemPrompt(Project $project): string
    {
        $controller = $this->getController($project);

        return $controller->getSystemPrompt();
    }

    /**
     * Get user prompt for a project
     */
    public function getUserPrompt(Project $project, string $prompt): string
    {
        $controller = $this->getController($project);

        return $controller->getUserPrompt($prompt);
    }

    /**
     * Get required files for a project
     */
    public function getRequiredFiles(Project $project): array
    {
        $controller = $this->getController($project);

        return $controller->getRequiredFiles();
    }

    /**
     * Generate mock project for a project
     */
    public function generateMockProject(Project $project, string $prompt, string $projectType = 'generic'): array
    {
        $controller = $this->getController($project);

        return $controller->generateMockProject($prompt, $projectType);
    }

    /**
     * Check if project is a specific stack type
     */
    public function isProjectType(Project $project, string $type): bool
    {
        $controller = $this->getController($project);

        return match ($type) {
            'astro' => $controller instanceof AstroController,
            'nodejs-express' => $controller instanceof ExpressController,
            'python-fastapi' => $controller instanceof FastAPIController,
            'nuxt3' => $controller instanceof NuxtController,
            'nextjs' => $controller instanceof NextJSController,
            'vite-react' => $controller instanceof ViteReactController,
            'vite-vue' => $controller instanceof ViteVueController,
            'sveltekit' => $controller instanceof SvelteKitController,
            default => false
        };
    }

    /**
     * Get Docker configuration for a project
     */
    public function getDockerConfig(Project $project): array
    {
        $controller = $this->getController($project);

        return $controller->getDockerConfig();
    }
}
