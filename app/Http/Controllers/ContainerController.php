<?php

namespace App\Http\Controllers;

use App\Jobs\StartContainerJob;
use App\Jobs\StopContainerJob;
use App\Models\Container;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContainerController extends Controller
{
    use AuthorizesRequests;
    public function store(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $container = $project->containers()->create([
            'container_id' => 'lovable-' . Str::random(12),
            'name' => $project->name . ' Container',
            'status' => 'starting',
            'environment' => [
                'PROJECT_ID' => $project->id,
                'PROJECT_NAME' => $project->name,
            ],
        ]);

        // Start Docker container
        StartContainerJob::dispatch($container);

        return response()->json([
            'container' => $container,
            'message' => 'Container is starting up...',
        ]);
    }

    public function show(Container $container): JsonResponse
    {
        $this->authorize('view', $container->project);

        return response()->json($container);
    }

    public function start(Container $container): JsonResponse
    {
        $this->authorize('update', $container->project);

        $container->update([
            'status' => 'starting',
            'started_at' => now(),
        ]);

        // Start Docker container
        StartContainerJob::dispatch($container);

        return response()->json([
            'message' => 'Container is starting...',
            'status' => $container->status,
        ]);
    }

    public function stop(Container $container): JsonResponse
    {
        $this->authorize('update', $container->project);

        $container->update([
            'status' => 'stopped',
            'stopped_at' => now(),
        ]);

        // Stop Docker container
        StopContainerJob::dispatch($container);

        return response()->json([
            'message' => 'Container stopped successfully.',
            'status' => $container->status,
        ]);
    }

    public function destroy(Container $container): RedirectResponse
    {
        $this->authorize('delete', $container->project);

        // Stop and remove Docker container
        StopContainerJob::dispatch($container);

        $container->delete();

        return redirect()->back()
            ->with('success', 'Container deleted successfully!');
    }
}