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
use Inertia\Inertia;
use Inertia\Response;

class PromptController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CollaborationService $collaborationService
    ) {
        //
    }

    public function store(StorePromptRequest $request, Project $project)
    {
        $this->authorize('update', $project);

        $prompt = $project->prompts()->create([
            'prompt' => $request->prompt,
            'status' => 'pending',
            'auto_start_container' => $request->boolean('auto_start_container', false),
        ]);

        // Track AI generation start
        $this->collaborationService->aiGenerationStarted($project, auth()->user(), $request->prompt);

        // Queue AI processing job
        ProcessPromptJob::dispatch($prompt);

        $message = $request->boolean('auto_start_container', false)
            ? 'Prompt submitted successfully! AI is processing your request and will automatically start a container when ready.'
            : 'Prompt submitted successfully! AI is processing your request.';

        // For Inertia requests, return back with the prompt data
        if (request()->header('X-Inertia')) {
            return redirect()->back()->with([
                'prompt' => $prompt,
                'message' => $message,
                'auto_start_container' => $request->boolean('auto_start_container', false),
            ]);
        }

        // For AJAX requests, return JSON
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'prompt' => $prompt,
                'message' => $message,
                'auto_start_container' => $request->boolean('auto_start_container', false),
            ]);
        }

        // Fallback JSON response
        return response()->json([
            'prompt' => $prompt,
            'message' => $message,
            'auto_start_container' => $request->boolean('auto_start_container', false),
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
