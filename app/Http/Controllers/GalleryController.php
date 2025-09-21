<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display the public gallery
     */
    public function index(Request $request): Response
    {
        $query = Project::with(['user', 'containers'])
            ->where('is_public', true)
            ->where('status', 'ready');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filtering (based on project name/description keywords)
        if ($request->has('category')) {
            $category = $request->get('category');
            $query->where(function ($q) use ($category) {
                switch ($category) {
                    case 'portfolio':
                        $q->where('name', 'like', '%portfolio%')
                          ->orWhere('description', 'like', '%portfolio%');
                        break;
                    case 'ecommerce':
                        $q->where('name', 'like', '%shop%')
                          ->orWhere('name', 'like', '%store%')
                          ->orWhere('description', 'like', '%ecommerce%');
                        break;
                    case 'blog':
                        $q->where('name', 'like', '%blog%')
                          ->orWhere('description', 'like', '%blog%');
                        break;
                    case 'landing':
                        $q->where('name', 'like', '%landing%')
                          ->orWhere('description', 'like', '%marketing%');
                        break;
                }
            });
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default: // latest
                $query->orderBy('created_at', 'desc');
        }

        $projects = $query->paginate(12);

        // Get featured projects (most viewed)
        $featured = Project::with(['user', 'containers'])
            ->where('is_public', true)
            ->where('status', 'ready')
            ->orderBy('views_count', 'desc')
            ->limit(6)
            ->get();

        return Inertia::render('gallery/Index', [
            'projects' => $projects,
            'featured' => $featured,
            'filters' => [
                'search' => $request->get('search'),
                'category' => $request->get('category'),
                'sort' => $request->get('sort', 'latest'),
            ],
            'categories' => [
                'all' => 'All Projects',
                'portfolio' => 'Portfolio',
                'ecommerce' => 'E-commerce',
                'blog' => 'Blog',
                'landing' => 'Landing Page',
            ],
            'sortOptions' => [
                'latest' => 'Latest',
                'popular' => 'Most Popular',
                'oldest' => 'Oldest',
                'name' => 'Name A-Z',
            ],
        ]);
    }

    /**
     * Display a public project
     */
    public function show(Project $project): Response
    {
        // Only allow viewing public projects
        if (!$project->is_public) {
            abort(404);
        }

        // Increment view count
        $project->increment('views_count');

        $project->load(['user', 'containers', 'prompts' => function ($query) {
            $query->latest()->limit(5);
        }]);

        // Get related projects
        $related = Project::with(['user', 'containers'])
            ->where('is_public', true)
            ->where('status', 'ready')
            ->where('id', '!=', $project->id)
            ->where('user_id', $project->user_id)
            ->limit(4)
            ->get();

        return Inertia::render('gallery/Show', [
            'project' => $project,
            'related' => $related,
        ]);
    }

    /**
     * Toggle project public status
     */
    public function togglePublic(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $project->update([
            'is_public' => !$project->is_public
        ]);

        return response()->json([
            'success' => true,
            'is_public' => $project->is_public,
            'message' => $project->is_public 
                ? 'Project is now public!' 
                : 'Project is now private'
        ]);
    }

    /**
     * Get project stats for dashboard
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_public_projects' => Project::where('is_public', true)->count(),
            'total_views' => Project::where('is_public', true)->sum('views_count'),
            'most_popular' => Project::with('user')
                ->where('is_public', true)
                ->orderBy('views_count', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}