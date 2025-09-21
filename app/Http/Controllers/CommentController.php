<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Services\CollaborationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CollaborationService $collaborationService
    ) {}

    /**
     * Get comments for a project
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $comments = Comment::with(['user', 'replies.user'])
            ->where('project_id', $project->id)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'in:general,code_review,suggestion,question',
            'metadata' => 'nullable|array',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        $comment = Comment::create([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'content' => $request->content,
            'type' => $request->type ?? 'general',
            'metadata' => $request->metadata,
            'parent_id' => $request->parent_id,
        ]);

        $comment->load('user');

        // Broadcast comment event
        $this->collaborationService->commentAdded($project, $comment);

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    /**
     * Update a comment
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $request->validate([
            'content' => 'required|string|max:2000',
            'metadata' => 'nullable|array',
        ]);

        $comment->update([
            'content' => $request->content,
            'metadata' => $request->metadata,
        ]);

        $comment->load('user');

        // Broadcast comment update event
        $this->collaborationService->commentUpdated($comment->project, $comment);

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $project = $comment->project;
        $comment->delete();

        // Broadcast comment deletion event
        $this->collaborationService->commentDeleted($project, $comment->id);

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    /**
     * Toggle comment resolved status
     */
    public function toggleResolved(Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $comment->update([
            'is_resolved' => !$comment->is_resolved
        ]);

        $comment->load('user');

        // Broadcast comment update event
        $this->collaborationService->commentUpdated($comment->project, $comment);

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }
}