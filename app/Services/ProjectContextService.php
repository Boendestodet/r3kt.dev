<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Container;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProjectContextService
{
    /**
     * Get comprehensive project context for AI chat
     */
    public function getProjectContext(Project $project): array
    {
        $context = [
            'project_info' => $this->getProjectInfo($project),
            'project_structure' => $this->getProjectStructure($project),
            'recent_prompts' => $this->getRecentPrompts($project),
            'container_status' => $this->getContainerStatus($project),
            'generated_files' => $this->getGeneratedFiles($project),
        ];

        return $context;
    }

    /**
     * Get basic project information
     */
    private function getProjectInfo(Project $project): array
    {
        return [
            'name' => $project->name,
            'description' => $project->description,
            'stack' => $project->stack,
            'ai_model' => $project->settings['ai_model'] ?? $project->model,
            'status' => $project->status,
            'created_at' => $project->created_at->format('Y-m-d H:i:s'),
            'last_built_at' => $project->last_built_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get project file structure
     */
    private function getProjectStructure(Project $project): array
    {
        $structure = [];
        
        // Get container to find project files
        $container = $project->containers()->latest()->first();
        if (!$container) {
            return $structure;
        }

        $projectPath = $this->getProjectPath($container);
        if (!$projectPath || !File::exists($projectPath)) {
            return $structure;
        }

        $structure = $this->scanDirectory($projectPath, 0, 3); // Max depth of 3
        return $structure;
    }

    /**
     * Get recent prompts for context
     */
    private function getRecentPrompts(Project $project): array
    {
        $prompts = $project->prompts()
            ->latest()
            ->limit(5)
            ->get(['prompt', 'response', 'created_at'])
            ->map(function ($prompt) {
                return [
                    'prompt' => substr($prompt->prompt, 0, 200) . (strlen($prompt->prompt) > 200 ? '...' : ''),
                    'response_summary' => $this->extractResponseSummary($prompt->response),
                    'created_at' => $prompt->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return $prompts->toArray();
    }

    /**
     * Get container status and info
     */
    private function getContainerStatus(Project $project): array
    {
        $container = $project->containers()->latest()->first();
        if (!$container) {
            return ['status' => 'no_container'];
        }

        return [
            'status' => $container->status,
            'port' => $container->port,
            'url' => $container->url,
            'created_at' => $container->created_at->format('Y-m-d H:i:s'),
            'logs' => $this->getRecentLogs($container),
        ];
    }

    /**
     * Get generated files content
     */
    private function getGeneratedFiles(Project $project): array
    {
        $files = [];
        
        $container = $project->containers()->latest()->first();
        if (!$container) {
            return $files;
        }

        $projectPath = $this->getProjectPath($container);
        if (!$projectPath || !File::exists($projectPath)) {
            return $files;
        }

        // Get key files based on project type
        $keyFiles = $this->getKeyFilesForProjectType($project->stack, $projectPath);
        
        foreach ($keyFiles as $filePath) {
            if (File::exists($filePath)) {
                $content = File::get($filePath);
                $files[] = [
                    'path' => str_replace($projectPath . '/', '', $filePath),
                    'content' => $this->truncateContent($content, 2000), // Limit content size
                    'size' => strlen($content),
                ];
            }
        }

        return $files;
    }

    /**
     * Get project path from container
     */
    private function getProjectPath(Container $container): ?string
    {
        $containerId = $container->container_id;
        if (!$containerId) {
            return null;
        }

        // Try different possible paths
        $possiblePaths = [
            "/var/lib/docker/volumes/{$containerId}_app_data/_data",
            "/tmp/docker-projects/{$containerId}",
            storage_path("docker-projects/{$containerId}"),
        ];

        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Scan directory recursively
     */
    private function scanDirectory(string $path, int $depth, int $maxDepth): array
    {
        if ($depth >= $maxDepth) {
            return [];
        }

        $structure = [];
        $items = File::allFiles($path);
        
        foreach ($items as $item) {
            $relativePath = str_replace($path . '/', '', $item->getPathname());
            $structure[] = [
                'path' => $relativePath,
                'type' => $item->isFile() ? 'file' : 'directory',
                'size' => $item->isFile() ? $item->getSize() : null,
                'extension' => $item->isFile() ? $item->getExtension() : null,
            ];
        }

        return $structure;
    }

    /**
     * Get key files based on project type
     */
    private function getKeyFilesForProjectType(string $stack, string $projectPath): array
    {
        $keyFiles = [];

        switch ($stack) {
            case 'nextjs':
                $keyFiles = [
                    $projectPath . '/package.json',
                    $projectPath . '/next.config.js',
                    $projectPath . '/app/page.tsx',
                    $projectPath . '/app/layout.tsx',
                    $projectPath . '/tailwind.config.js',
                ];
                break;
            case 'vite-react':
                $keyFiles = [
                    $projectPath . '/package.json',
                    $projectPath . '/vite.config.js',
                    $projectPath . '/src/App.tsx',
                    $projectPath . '/src/main.tsx',
                    $projectPath . '/tailwind.config.js',
                ];
                break;
            case 'vite-vue':
                $keyFiles = [
                    $projectPath . '/package.json',
                    $projectPath . '/vite.config.js',
                    $projectPath . '/src/App.vue',
                    $projectPath . '/src/main.js',
                    $projectPath . '/tailwind.config.js',
                ];
                break;
            case 'sveltekit':
                $keyFiles = [
                    $projectPath . '/package.json',
                    $projectPath . '/svelte.config.js',
                    $projectPath . '/src/routes/+page.svelte',
                    $projectPath . '/src/app.html',
                ];
                break;
            case 'astro':
                $keyFiles = [
                    $projectPath . '/package.json',
                    $projectPath . '/astro.config.mjs',
                    $projectPath . '/src/pages/index.astro',
                    $projectPath . '/src/layouts/Layout.astro',
                ];
                break;
            case 'nuxt':
                $keyFiles = [
                    $projectPath . '/package.json',
                    $projectPath . '/nuxt.config.ts',
                    $projectPath . '/pages/index.vue',
                    $projectPath . '/app.vue',
                ];
                break;
            default:
                // Generic files
                $keyFiles = [
                    $projectPath . '/package.json',
                    $projectPath . '/index.html',
                    $projectPath . '/README.md',
                ];
        }

        return $keyFiles;
    }

    /**
     * Extract summary from prompt response
     */
    private function extractResponseSummary(?string $response): string
    {
        if (!$response) {
            return 'No response';
        }

        // Try to extract meaningful content from JSON response
        $decoded = json_decode($response, true);
        if ($decoded) {
            if (isset($decoded['description'])) {
                return substr($decoded['description'], 0, 100) . '...';
            }
            if (isset($decoded['name'])) {
                return "Generated: {$decoded['name']}";
            }
        }

        // Fallback to first 100 characters
        return substr(strip_tags($response), 0, 100) . '...';
    }

    /**
     * Get recent container logs
     */
    private function getRecentLogs(Container $container): string
    {
        if (!$container->logs) {
            return 'No logs available';
        }

        $logs = $container->logs;
        // Get last 500 characters of logs
        return strlen($logs) > 500 ? '...' . substr($logs, -500) : $logs;
    }

    /**
     * Truncate content for AI context
     */
    private function truncateContent(string $content, int $maxLength): string
    {
        if (strlen($content) <= $maxLength) {
            return $content;
        }

        return substr($content, 0, $maxLength) . "\n... [truncated]";
    }

    /**
     * Format context for AI prompt
     */
    public function formatContextForAI(array $context): string
    {
        $formatted = "## Project Context\n\n";
        
        // Project info
        $formatted .= "**Project:** {$context['project_info']['name']}\n";
        $formatted .= "**Description:** {$context['project_info']['description']}\n";
        $formatted .= "**Stack:** {$context['project_info']['stack']}\n";
        $formatted .= "**AI Model:** {$context['project_info']['ai_model']}\n";
        $formatted .= "**Status:** {$context['project_info']['status']}\n\n";

        // Recent prompts
        if (!empty($context['recent_prompts'])) {
            $formatted .= "## Recent Development Activity\n";
            foreach ($context['recent_prompts'] as $prompt) {
                $formatted .= "- **{$prompt['created_at']}:** {$prompt['prompt']}\n";
                $formatted .= "  *Result: {$prompt['response_summary']}*\n\n";
            }
        }

        // Container status
        if ($context['container_status']['status'] !== 'no_container') {
            $formatted .= "## Current Container Status\n";
            $formatted .= "- **Status:** {$context['container_status']['status']}\n";
            $formatted .= "- **URL:** {$context['container_status']['url']}\n";
            if (!empty($context['container_status']['logs'])) {
                $formatted .= "- **Recent Logs:** {$context['container_status']['logs']}\n\n";
            }
        }

        // Project files
        if (!empty($context['generated_files'])) {
            $formatted .= "## Key Project Files\n";
            foreach ($context['generated_files'] as $file) {
                $formatted .= "### {$file['path']}\n";
                $formatted .= "```\n{$file['content']}\n```\n\n";
            }
        }

        return $formatted;
    }
}
