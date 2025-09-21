<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromptRequest;
use App\Jobs\ProcessPromptJob;
use App\Models\Project;
use App\Models\Prompt;
use App\Services\CollaborationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CollaborationService $collaborationService
    ) {
        //
    }
    public function store(StorePromptRequest $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $prompt = $project->prompts()->create([
            'prompt' => $request->prompt,
            'status' => 'pending',
        ]);

        // Track AI generation start
        $this->collaborationService->aiGenerationStarted($project, auth()->user(), $request->prompt);

        // Queue AI processing job
        ProcessPromptJob::dispatch($prompt);

        return response()->json([
            'prompt' => $prompt,
            'message' => 'Prompt submitted successfully! AI is processing your request.',
        ]);
    }

    public function show(Prompt $prompt): JsonResponse
    {
        $this->authorize('view', $prompt->project);

        return response()->json($prompt);
    }

    public function status(Prompt $prompt): JsonResponse
    {
        $this->authorize('view', $prompt->project);

        return response()->json([
            'status' => $prompt->status,
            'response' => $prompt->response,
            'processed_at' => $prompt->processed_at,
        ]);
    }

    public function destroy(Prompt $prompt): RedirectResponse
    {
        $this->authorize('delete', $prompt->project);

        $prompt->delete();

        return redirect()->back()
            ->with('success', 'Prompt deleted successfully!');
    }
}
