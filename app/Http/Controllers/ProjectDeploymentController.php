<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProjectDeploymentController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Deploy a project
     */
    public function deploy(Project $project)
    {
        try {
            // Check if user owns the project
            if ($project->user_id !== auth()->id()) {
                if (request()->header('X-Inertia')) {
                    return redirect()->back()->withErrors(['error' => 'Unauthorized access to project']);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to project',
                ], 403);
            }

            $success = $this->dockerService->deployProject($project);

            if ($success) {
                // For Inertia requests, return back with success message
                if (request()->header('X-Inertia')) {
                    return redirect()->back()->with('success', 'Project deployed successfully!');
                }

                // For AJAX requests, return JSON
                if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Project deployed successfully',
                        'data' => [
                            'project_id' => $project->id,
                            'status' => 'deployed',
                        ],
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Project deployed successfully',
                    'data' => $project,
                ]);
            }

            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Failed to deploy project']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to deploy project',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to deploy project', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Failed to deploy project: '.$e->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to deploy project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get preview URL for a project
     */
    public function getPreviewUrl(Project $project): JsonResponse
    {
        try {
            // Check if user owns the project
            if ($project->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to project',
                ], 403);
            }

            $container = $project->containers()->where('status', 'running')->first();

            if (! $container) {
                return response()->json([
                    'success' => false,
                    'message' => 'No running container found for this project',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $container->url ?? '',
                    'port' => $container->port ?? '',
                    'status' => $container->status ?? 'unknown',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get preview URL', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get preview URL',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
