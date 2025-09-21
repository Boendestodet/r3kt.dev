<?php

namespace Tests;

use App\Services\ClaudeAIService;
use App\Services\DockerService;
use App\Services\OpenAIService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock AI services to prevent real API calls in tests
        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(false);
            $mock->shouldReceive('generateWebsite')->andReturn([
                'project' => [
                    'package.json' => '{"name": "test-project", "scripts": {"dev": "next dev"}}',
                    'app/page.tsx' => '<div>Test Page</div>',
                    'app/layout.tsx' => '<html><body>{children}</body></html>',
                ],
                'tokens_used' => 100,
                'model' => 'test-model',
            ]);
        });

        $this->mock(ClaudeAIService::class, function ($mock) {
            $mock->shouldReceive('isConfigured')->andReturn(false);
            $mock->shouldReceive('generateWebsite')->andReturn([
                'project' => [
                    'package.json' => '{"name": "test-project", "scripts": {"dev": "next dev"}}',
                    'app/page.tsx' => '<div>Test Page</div>',
                    'app/layout.tsx' => '<html><body>{children}</body></html>',
                ],
                'tokens_used' => 100,
                'model' => 'test-model',
            ]);
        });

        $this->mock(DockerService::class, function ($mock) {
            $mock->shouldReceive('isDockerAvailable')->andReturn(false);
            $mock->shouldReceive('startContainer')->andReturn(false);
            $mock->shouldReceive('cleanupProject')->andReturn(true);
            $mock->shouldReceive('stopContainer')->andReturn(true);
            $mock->shouldReceive('restartContainer')->andReturn(true);
            $mock->shouldReceive('getContainerStatus')->andReturn(['status' => 'stopped']);
            $mock->shouldReceive('getContainerLogs')->andReturn('Container logs');
            $mock->shouldReceive('getAllRunningContainers')->andReturn([]);
            $mock->shouldReceive('cleanup')->andReturn(true);

            // Additional Docker methods for comprehensive mocking
            $mock->shouldReceive('deployProject')->andReturn(true);
            $mock->shouldReceive('checkContainerHealth')->andReturn([
                'status' => 'healthy',
                'message' => 'Container is running',
                'healthy' => true,
            ]);
            $mock->shouldReceive('getContainerStats')->andReturn([
                'status' => 'running',
                'cpu_usage' => '10%',
                'memory_usage' => '512MB',
                'uptime' => '1h 30m',
            ]);
            $mock->shouldReceive('cleanupOldResources')->andReturn([
                'containers' => 0,
                'images' => 0,
                'errors' => [],
            ]);
            $mock->shouldReceive('getDockerInfo')->andReturn([
                'available' => false,
                'version' => 'Docker not available',
                'containers' => 0,
                'images' => 0,
            ]);
        });
    }
}
