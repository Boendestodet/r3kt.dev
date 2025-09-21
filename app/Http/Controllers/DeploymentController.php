<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Container;
use App\Services\DockerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DeploymentController extends Controller
{
    use AuthorizesRequests;

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
        $this->authorize('update', $project);

        try {
            $success = $this->dockerService->deployProject($project);

            if ($success) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Project deployed successfully!'
                    ]);
                }
                return redirect()->back()->with('success', 'Project deployed successfully!');
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to deploy project. Please try again.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Failed to deploy project. Please try again.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deployment failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Deployment failed: ' . $e->getMessage());
        }
    }

    /**
     * Get deployment status
     */
    public function status(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $container = $project->containers()->where('status', 'running')->first();
        
        if (!$container) {
            return response()->json([
                'status' => 'not_deployed',
                'message' => 'Project is not deployed'
            ]);
        }

        $health = $this->dockerService->checkContainerHealth($container);
        $stats = $this->dockerService->getContainerStats($container);

        return response()->json([
            'status' => $container->status,
            'url' => $container->url,
            'health' => $health,
            'stats' => $stats,
            'last_built_at' => $project->last_built_at
        ]);
    }

    /**
     * Restart a project container
     */
    public function restart(Project $project)
    {
        $this->authorize('update', $project);

        try {
            $container = $project->containers()->where('status', 'running')->first();
            
            if (!$container) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No running container found for this project'
                    ], 404);
                }
                return redirect()->back()->with('error', 'No running container found for this project');
            }

            $success = $this->dockerService->restartContainer($container);

            if ($success) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Container restarted successfully!'
                    ]);
                }
                return redirect()->back()->with('success', 'Container restarted successfully!');
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restart container. Please try again.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Failed to restart container. Please try again.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restart failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Restart failed: ' . $e->getMessage());
        }
    }

    /**
     * Stop a project container
     */
    public function stop(Project $project)
    {
        $this->authorize('update', $project);

        try {
            $container = $project->containers()->where('status', 'running')->first();
            
            if (!$container) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No running container found for this project'
                    ], 404);
                }
                return redirect()->back()->with('error', 'No running container found for this project');
            }

            $success = $this->dockerService->stopContainer($container);

            if ($success) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Container stopped successfully!'
                    ]);
                }
                return redirect()->back()->with('success', 'Container stopped successfully!');
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to stop container. Please try again.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Failed to stop container. Please try again.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stop failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Stop failed: ' . $e->getMessage());
        }
    }

    /**
     * Get container logs
     */
    public function logs(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        try {
            $container = $project->containers()->where('status', 'running')->first();
            
            if (!$container) {
                return response()->json([
                    'success' => false,
                    'message' => 'No running container found for this project'
                ], 404);
            }

            $logs = $this->dockerService->getContainerLogs($container);

            return response()->json([
                'success' => true,
                'logs' => $logs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve logs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all running containers
     */
    public function containers(): JsonResponse
    {
        try {
            $containers = $this->dockerService->getAllRunningContainers();

            return response()->json([
                'success' => true,
                'containers' => $containers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve containers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup old Docker resources
     */
    public function cleanup(): JsonResponse
    {
        try {
            $result = $this->dockerService->cleanupOldResources();

            return response()->json([
                'success' => true,
                'message' => 'Cleanup completed successfully',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
}