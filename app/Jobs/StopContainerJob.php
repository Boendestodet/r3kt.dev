<?php

namespace App\Jobs;

use App\Models\Container;
use App\Services\DockerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StopContainerJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Container $container
    ) {}

    /**
     * Execute the job.
     */
    public function handle(DockerService $dockerService): void
    {
        $dockerService->stopContainer($this->container);
    }
}
