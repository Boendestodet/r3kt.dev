<?php

namespace App\Jobs;

use App\Models\Prompt;
use App\Services\AIWebsiteGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPromptJob implements ShouldQueue
{
    use Queueable;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300; // 5 minutes

    public function __construct(
        public Prompt $prompt
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AIWebsiteGenerator $aiGenerator): void
    {
        $aiGenerator->processPrompt($this->prompt);
    }
}
