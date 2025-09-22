<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Project;
use App\Services\DockerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DockerController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Get Docker system information
     */
    public function info(): JsonResponse
    {
        try {
            $info = $this->dockerService->getDockerInfo();

            return response()->json([
                'success' => true,
                'data' => $info,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get Docker info', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get Docker information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Start a container for a project
     */
    public function startContainer(Request $request, Project $project)
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

                // Always return Inertia response for this application
                return redirect()->back()->with([
                    'success' => 'Container started successfully',
                    'container' => [
                        'id' => $container->id,
                        'status' => $container->status,
                        'url' => $container->url,
                        'port' => $container->port,
                        'external_url' => $this->dockerService->getExternalUrl($container),
                    ],
                ]);
            }

            // Always return Inertia response for this application
            return redirect()->back()->withErrors(['error' => 'Failed to start container']);

        } catch (\Exception $e) {
            Log::error('Failed to start container', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            // Always return Inertia response for this application
            return redirect()->back()->withErrors(['error' => 'Failed to start container: '.$e->getMessage()]);
        }
    }

    /**
     * Stop a container
     */
    public function stopContainer(Container $container): JsonResponse
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
    public function restartContainer(Container $container): JsonResponse
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
                        'url' => $container->url,
                        'external_url' => $this->dockerService->getExternalUrl($container),
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
     * Get container status and health
     */
    public function getContainerStatus(Container $container): JsonResponse
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

            return response()->json([
                'success' => true,
                'data' => [
                    'container_id' => $container->id,
                    'status' => $container->status,
                    'health' => $health,
                    'stats' => $stats,
                    'url' => $container->url,
                    'external_url' => $this->dockerService->getExternalUrl($container),
                    'port' => $container->port,
                    'started_at' => $container->started_at,
                    'stopped_at' => $container->stopped_at,
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
    public function getContainerLogs(Container $container): JsonResponse
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

    /**
     * Get all running containers
     */
    public function getRunningContainers(): JsonResponse
    {
        try {
            $containers = $this->dockerService->getAllRunningContainers();

            return response()->json([
                'success' => true,
                'data' => $containers,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get running containers', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get running containers',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cleanup old containers and images
     */
    public function cleanup(): JsonResponse
    {
        try {
            $cleaned = $this->dockerService->cleanupOldResources();

            return response()->json([
                'success' => true,
                'message' => 'Docker cleanup completed',
                'data' => $cleaned,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup Docker resources', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cleanup Docker resources',
                'error' => $e->getMessage(),
            ], 500);
        }
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
                        'message' => 'Project deployed successfully!',
                    ]);
                }

                return redirect()->back()->with('success', 'Project deployed successfully!');
            }

            // For Inertia requests, return back with error
            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Failed to deploy project. Please try again.']);
            }

            // For AJAX requests, return JSON
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to deploy project. Please try again.',
                ], 400);
            }

            return redirect()->back()->withErrors(['error' => 'Failed to deploy project. Please try again.']);

        } catch (\Exception $e) {
            Log::error('Failed to deploy project', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            // For Inertia requests, return back with error
            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Deployment failed: '.$e->getMessage()]);
            }

            // For AJAX requests, return JSON
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Deployment failed: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Deployment failed: '.$e->getMessage()]);
        }
    }

    /**
     * Get project preview URL
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
                    'url' => $container->url,
                    'external_url' => $this->dockerService->getExternalUrl($container),
                    'port' => $container->port,
                    'status' => $container->status,
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
