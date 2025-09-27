<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Project;
use App\Services\DockerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContainerController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Start a container for a project
     */
    public function start(Request $request, Project $project)
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

            // Check if Docker is available
            if (! $this->dockerService->isDockerAvailable()) {
                if (request()->header('X-Inertia')) {
                    return redirect()->back()->withErrors(['error' => 'Docker is not available on this system']);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Docker is not available on this system',
                ], 503);
            }

            // Check if project has generated code
            if (! $project->generated_code) {
                if (request()->header('X-Inertia')) {
                    return redirect()->back()->withErrors(['error' => 'Project has no generated code. Please generate a website first.']);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Project has no generated code. Please generate a website first.',
                ], 400);
            }

            // Create or get existing container
            $container = $project->containers()->where('status', '!=', 'stopped')->first();

            if (! $container) {
                $container = $project->containers()->create([
                    'status' => 'starting',
                ]);
            }

            // Start the container
            $success = $this->dockerService->startContainer($container);

            if ($success) {
                $container->refresh();
                // For Inertia requests, return back with success message
                if (request()->header('X-Inertia')) {
                    return redirect()->back()->with('success', 'Container started successfully!');
                }

                // For AJAX requests, return JSON
                if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Container started successfully',
                        'data' => [
                            'container_id' => $container->id,
                            'status' => $container->status,
                            'url' => $container->url,
                            'port' => $container->port,
                        ],
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Container started successfully',
                    'data' => $container,
                ]);
            }

            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Failed to start container']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to start container',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to start container', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Failed to start container: '.$e->getMessage()]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to start container',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stop a container
     */
    public function stop(Container $container): JsonResponse
    {
        try {
            // Check if user owns the container's project
            if ($container->project->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to container',
                ], 403);
            }

            $success = $this->dockerService->stopContainer($container);

            if ($success) {
                $container->refresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Container stopped successfully',
                    'data' => [
                        'container_id' => $container->id,
                        'status' => $container->status,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to stop container',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to stop container', [
                'container_id' => $container->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to stop container',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restart a container
     */
    public function restart(Container $container): JsonResponse
    {
        try {
            // Check if user owns the container's project
            if ($container->project->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to container',
                ], 403);
            }

            $success = $this->dockerService->restartContainer($container);

            if ($success) {
                $container->refresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Container restarted successfully',
                    'data' => [
                        'container_id' => $container->id,
                        'status' => $container->status,
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to restart container',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to restart container', [
                'container_id' => $container->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to restart container',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get container status
     */
    public function status(Container $container): JsonResponse
    {
        try {
            // Check if user owns the container's project
            if ($container->project->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to container',
                ], 403);
            }

            $health = $this->dockerService->checkContainerHealth($container);
            $stats = $this->dockerService->getContainerStats($container);
            $status = $health['status'] ?? 'unknown';

            return response()->json([
                'success' => true,
                'data' => [
                    'container_id' => $container->id,
                    'status' => $status,
                    'is_running' => $status === 'running',
                    'health' => $health,
                    'stats' => $stats,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get container status', [
                'container_id' => $container->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get container status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get container logs
     */
    public function logs(Container $container): JsonResponse
    {
        try {
            // Check if user owns the container's project
            if ($container->project->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to container',
                ], 403);
            }

            $logs = $this->dockerService->getContainerLogs($container);

            return response()->json([
                'success' => true,
                'data' => [
                    'container_id' => $container->id,
                    'logs' => $logs,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get container logs', [
                'container_id' => $container->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get container logs',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
