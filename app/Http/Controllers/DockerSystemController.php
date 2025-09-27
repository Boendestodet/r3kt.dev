<?php

namespace App\Http\Controllers;

use App\Services\DockerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DockerSystemController extends Controller
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
     * Get all running containers
     */
    public function getRunningContainers(): JsonResponse
    {
        try {
            $containers = $this->dockerService->getRunningContainers();

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
     * Clean up Docker resources
     */
    public function cleanup(): JsonResponse
    {
        try {
            $result = $this->dockerService->cleanup();

            return response()->json([
                'success' => true,
                'message' => 'Docker cleanup completed',
                'data' => $result,
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
}
