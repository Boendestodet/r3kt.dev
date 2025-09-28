<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class ProjectVerificationController extends Controller
{
    use AuthorizesRequests;

    /**
     * Verify project setup and file structure
     */
    public function verify(Project $project): JsonResponse|RedirectResponse
    {
        // Check if user owns the project
        if ($project->user_id !== auth()->id()) {
            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Unauthorized access to project']);
            }

            return response()->json(['success' => false, 'message' => 'Unauthorized access to project'], 403);
        }

        $verification = [
            'database_exists' => false,
            'folder_exists' => false,
            'required_files' => [],
            'all_files_present' => false,
            'overall_status' => 'failed',
        ];

        try {
            // 1. Check if project exists in database
            $verification['database_exists'] = $project->exists;

            // 2. Check if project folder exists
            $projectPath = storage_path("app/projects/{$project->id}");
            $verification['folder_exists'] = is_dir($projectPath);

            // 3. Check for required files based on project type
            $requiredFiles = $this->getRequiredFiles($project);
            $filesPresent = [];

            foreach ($requiredFiles as $file) {
                $filePath = $projectPath.'/'.$file;
                $filesPresent[$file] = file_exists($filePath);
            }

            $verification['required_files'] = $filesPresent;
            $verification['all_files_present'] = ! in_array(false, $filesPresent);

            // 4. Overall status
            if ($verification['database_exists'] && $verification['folder_exists'] && $verification['all_files_present']) {
                $verification['overall_status'] = 'success';
            } elseif ($verification['database_exists'] && $verification['folder_exists']) {
                $verification['overall_status'] = 'partial';
            } else {
                $verification['overall_status'] = 'failed';
            }

            // For Inertia requests, return back with verification data
            if (request()->header('X-Inertia')) {
                return redirect()->back()->with(['verification' => $verification]);
            }

            // For AJAX requests, return JSON
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'verification' => $verification,
                ]);
            }

            return response()->json(['verification' => $verification]);

        } catch (\Exception $e) {
            Log::error('Failed to verify project setup', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            $verification['error'] = $e->getMessage();

            // For Inertia requests, return back with error
            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Verification failed: '.$e->getMessage()]);
            }

            // For AJAX requests, return JSON
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification failed: '.$e->getMessage(),
                    'verification' => $verification,
                ], 500);
            }

            return response()->json(['verification' => $verification], 500);
        }
    }

    /**
     * Get required files based on project type
     */
    private function getRequiredFiles(Project $project): array
    {
        $stack = $project->settings['stack'] ?? 'nextjs';

        switch (strtolower($stack)) {
            case 'astro':
                return [
                    'package.json',
                    'astro.config.mjs',
                    'tsconfig.json',
                    'src/pages/index.astro',
                    'src/layouts/Layout.astro',
                    'src/components/Header.astro',
                    'src/components/Footer.astro',
                    'src/styles/global.css',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];
            case 'nuxt3':
                return [
                    'package.json',
                    'nuxt.config.ts',
                    'tsconfig.json',
                    'app.vue',
                    'pages/index.vue',
                    'components/Header.vue',
                    'components/Footer.vue',
                    'assets/css/main.css',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];
            case 'nodejs-express':
                return [
                    'package.json',
                    'tsconfig.json',
                    '.env',
                    'src/app.ts',
                    'src/routes/index.ts',
                    'src/routes/api.ts',
                    'src/middleware/cors.ts',
                    'src/middleware/errorHandler.ts',
                    'src/types/index.ts',
                    'src/utils/logger.ts',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];
            case 'python-fastapi':
                return [
                    'requirements.txt',
                    'pyproject.toml',
                    '.env',
                    'main.py',
                    'app/__init__.py',
                    'app/api/__init__.py',
                    'app/api/routes.py',
                    'app/api/dependencies.py',
                    'app/core/__init__.py',
                    'app/core/config.py',
                    'app/core/security.py',
                    'app/models/__init__.py',
                    'app/models/schemas.py',
                    'app/services/__init__.py',
                    'app/services/database.py',
                    'app/utils/__init__.py',
                    'app/utils/logger.py',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];
            case 'vite-react':
                return [
                    'package.json',
                    'vite.config.ts',
                    'tsconfig.json',
                    'index.html',
                    'src/main.tsx',
                    'src/App.tsx',
                    'src/App.css',
                    'src/index.css',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];
            case 'vite-vue':
                return [
                    'package.json',
                    'vite.config.ts',
                    'tsconfig.json',
                    'index.html',
                    'src/main.ts',
                    'src/App.vue',
                    'src/App.css',
                    'src/style.css',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];

            case 'sveltekit':
                return [
                    'package.json',
                    'svelte.config.js',
                    'tsconfig.json',
                    'src/app.html',
                    'src/routes/+layout.svelte',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];

            case 'nextjs':
            default:
                return [
                    'package.json',
                    'next.config.js',
                    'tsconfig.json',
                    'app/layout.tsx',
                    'app/page.tsx',
                    'Dockerfile',
                    'docker-compose.yml',
                    '.dockerignore',
                ];
        }
    }
}
