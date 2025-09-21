<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\CollaborationService;
use App\Services\DockerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CollaborationService $collaborationService,
        private DockerService $dockerService
    ) {
        //
    }
    public function index(): Response
    {
        $projects = auth()->user()->projects()
            ->with(['containers', 'prompts'])
            ->latest()
            ->paginate(12);

        return Inertia::render('projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function show(Project $project): Response
    {
        $this->authorize('view', $project);

        $project->load(['containers', 'prompts' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // Track user joining the project
        $this->collaborationService->userJoined($project, auth()->user());

        // Get collaboration data
        $activeCollaborators = $this->collaborationService->getActiveCollaborators($project);
        $collaborationHistory = $this->collaborationService->getCollaborationHistory($project);

        return Inertia::render('projects/Show', [
            'project' => $project,
            'activeCollaborators' => $activeCollaborators,
            'collaborationHistory' => $collaborationHistory,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('projects/Create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = auth()->user()->projects()->create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
            'settings' => $request->settings ?? [],
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        return Inertia::render('projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $oldData = $project->toArray();
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'settings' => $request->settings ?? $project->settings,
        ]);
        
        // Track project changes
        $newData = $project->fresh()->toArray();
        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                $changes[$key] = $value;
            }
        }
        $this->collaborationService->projectUpdated($project, auth()->user(), $changes);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        // Clean up Docker resources before deleting the project
        $this->dockerService->cleanupProject($project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }

    public function duplicate(Project $project): RedirectResponse
    {
        $this->authorize('view', $project);

        $newProject = $project->replicate();
        $newProject->name = $project->name . ' (Copy)';
        $newProject->slug = Str::slug($newProject->name);
        $newProject->status = 'draft';
        $newProject->preview_url = null;
        $newProject->last_built_at = null;
        $newProject->subdomain = null; // Clear subdomain to generate a new one
        $newProject->custom_domain = null; // Clear custom domain
        $newProject->dns_configured = false; // Reset DNS status
        $newProject->save();

        return redirect()->route('projects.show', $newProject)
            ->with('success', 'Project duplicated successfully!');
    }
}
