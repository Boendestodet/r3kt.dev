<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class ProjectSandboxController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the sandbox for the specified project
     */
    public function show(Project $project): Response
    {
        $this->authorize('view', $project);

        $project->load(['containers', 'prompts' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // Get active container for preview URL
        $activeContainer = $project->getActiveContainer();
        $previewUrl = $activeContainer ? $activeContainer->preview_url : null;

        // Determine project status
        $status = 'draft';
        if ($activeContainer) {
            $status = $activeContainer->status === 'running' ? 'ready' : 'building';
        } elseif ($project->containers->count() > 0) {
            $status = 'building';
        }

        // Get project settings
        $settings = $project->settings ?? [];
        $stack = $settings['stack'] ?? 'Next.js';
        $model = $settings['ai_model'] ?? 'Claude Code';

        return Inertia::render('projects/Sandbox', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'stack' => $stack,
                'model' => $model,
                'status' => $status,
                'preview_url' => $previewUrl,
                'created_at' => $project->created_at,
                'updated_at' => $project->updated_at,
                'containers' => $project->containers,
                'prompts' => $project->prompts,
                'generated_code' => $project->generated_code,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }
}
