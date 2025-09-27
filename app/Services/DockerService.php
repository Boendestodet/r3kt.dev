<?php

namespace App\Services;

use App\Models\Container;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class DockerService
{
    public function __construct(
        private CloudflareService $cloudflareService
    ) {
        //
    }

    /**
     * Start a Docker container for a project
     */
    public function startContainer(Container $container): bool
    {
        try {
            $container->update([
                'status' => 'starting',
                'started_at' => now(),
            ]);

            // Create project files
            $htmlContent = $container->project->generated_code ?? $this->getDefaultHtml();
            $this->createProjectFiles($container, $htmlContent);

            // Get available port once and use it for both Docker and database
            $port = $this->getAvailablePort();

            // Build and start Docker container with the allocated port
            $containerId = $this->buildAndRunContainer($container, $port);

            if ($containerId) {
                $url = $this->getProjectUrl($container->project, $port);

                $container->update([
                    'container_id' => $containerId,
                    'status' => 'running',
                    'port' => $port,
                    'url' => $url,
                ]);

                // Update project status
                $container->project->update([
                    'status' => 'ready',
                    'preview_url' => $url,
                    'last_built_at' => now(),
                ]);

                Log::info('Container started successfully', [
                    'container_id' => $containerId,
                    'project_id' => $container->project_id,
                    'url' => $url,
                ]);

                return true;
            }

            $container->update(['status' => 'error']);

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to start container', [
                'container_id' => $container->id,
                'project_id' => $container->project_id,
                'error' => $e->getMessage(),
            ]);

            $container->update([
                'status' => 'error',
                'logs' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Stop a Docker container
     */
    public function stopContainer(Container $container): bool
    {
        try {
            if ($container->container_id) {
                Process::run("docker stop {$container->container_id}");
                Process::run("docker rm {$container->container_id}");
            }

            $container->update([
                'status' => 'stopped',
                'stopped_at' => now(),
            ]);

            return true;

        } catch (\Exception $e) {
            $container->update([
                'status' => 'error',
                'logs' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Create project files for the container
     */
    private function createProjectFiles(Container $container, string $projectData): void
    {
        $projectDir = storage_path("app/projects/{$container->project->id}");

        // Create project directory with proper permissions
        FilePermissionService::createDirectory($projectDir, 0755);

        // Get the appropriate stack controller
        $stackController = app(\App\Services\StackControllerFactory::class)->getController($container->project);

        Log::info('DockerService using stack controller', [
            'project_id' => $container->project->id,
            'stack' => $container->project->settings['stack'] ?? 'NOT SET',
            'controller' => get_class($stackController),
            'project_data_length' => strlen($projectData),
        ]);

        // Parse the project data
        $projectFiles = json_decode($projectData, true);

        if ($projectFiles && is_array($projectFiles)) {
            // Use stack controller to create project files
            $stackController->createProjectFiles($projectDir, $projectFiles);
        } else {
            // Check if files already exist (created by ProjectController)
            if ($stackController->hasRequiredFiles($projectDir)) {
                Log::info('Project files already exist, skipping fallback creation', [
                    'project_id' => $container->project->id,
                    'project_dir' => $projectDir,
                ]);
            } else {
                // Fallback: create basic structure if no generated code
                $stackController->createBasicFallback($projectDir, $container->project);
            }
        }
    }

    /**
     * Get the internal port for the project based on its type
     */
    private function getInternalPort(string $projectDir): string
    {
        // Try to determine project type from files
        if (file_exists("{$projectDir}/next.config.js") || file_exists("{$projectDir}/app")) {
            return '3000'; // Next.js
        }

        if (file_exists("{$projectDir}/vite.config.ts") || file_exists("{$projectDir}/vite.config.js")) {
            return '5173'; // Vite (React or Vue)
        }

        if (file_exists("{$projectDir}/svelte.config.js")) {
            return '5173'; // SvelteKit
        }

        // Default to Next.js port
        return '3000';
    }

    /**
     * Build and run Docker container for the project
     */
    private function buildAndRunContainer(Container $container, int $port): ?string
    {
        try {
            // In testing environment, simulate Docker container creation
            if (app()->environment('testing')) {
                return "test-container-{$container->id}";
            }

            // Check if Docker is available
            $dockerCheck = Process::run('docker --version');
            if (! $dockerCheck->successful()) {
                Log::warning('Docker not available, using fallback method', [
                    'project_id' => $container->project_id,
                    'error' => $dockerCheck->errorOutput(),
                ]);

                return $this->runFallbackServer($container, $port);
            }

            $projectDir = storage_path("app/projects/{$container->project->id}");
            $imageName = "lovable-project-{$container->project->id}";
            $containerName = "lovable-container-{$container->project->id}";

            // Ensure project directory exists
            if (! is_dir($projectDir)) {
                Log::error('Project directory not found', [
                    'project_id' => $container->project_id,
                    'directory' => $projectDir,
                ]);

                return null;
            }

            // Build Docker image with timeout
            $buildCommand = "docker build -t {$imageName} {$projectDir}";
            $buildResult = Process::timeout(300)->run($buildCommand);

            if (! $buildResult->successful()) {
                Log::error('Docker build failed', [
                    'project_id' => $container->project_id,
                    'command' => $buildCommand,
                    'error' => $buildResult->errorOutput(),
                    'output' => $buildResult->output(),
                ]);

                return $this->runFallbackServer($container, $port);
            }

            Log::info('Docker image built successfully', [
                'project_id' => $container->project_id,
                'image_name' => $imageName,
            ]);

            // Stop and remove existing container if it exists
            $this->cleanupExistingContainer($containerName);

            // Determine the internal port based on project type
            $internalPort = $this->getInternalPort($projectDir);

            // Run Docker container with timeout
            $runCommand = "docker run -d --name {$containerName} -p {$port}:{$internalPort} --restart=unless-stopped {$imageName}";
            $runResult = Process::timeout(60)->run($runCommand);

            if (! $runResult->successful()) {
                $errorOutput = $runResult->errorOutput();

                // Check if it's a port conflict error
                if (strpos($errorOutput, 'address already in use') !== false) {
                    Log::error('Docker run failed due to port conflict', [
                        'project_id' => $container->project_id,
                        'command' => $runCommand,
                        'port' => $port,
                        'error' => $errorOutput,
                    ]);

                    // Try to find another available port
                    $newPort = $this->findAlternativePort($port);
                    if ($newPort && $newPort !== $port) {
                        Log::info('Retrying with alternative port', [
                            'project_id' => $container->project_id,
                            'old_port' => $port,
                            'new_port' => $newPort,
                        ]);

                        // Update the container with the new port
                        $container->update(['port' => $newPort]);

                        // Try again with the new port
                        $retryCommand = "docker run -d --name {$containerName} -p {$newPort}:{$internalPort} --restart=unless-stopped {$imageName}";
                        $retryResult = Process::timeout(60)->run($retryCommand);

                        if ($retryResult->successful()) {
                            $containerId = trim($retryResult->output());
                            Log::info('Docker container started successfully on retry', [
                                'project_id' => $container->project_id,
                                'container_id' => $containerId,
                                'container_name' => $containerName,
                                'port' => $newPort,
                            ]);

                            return $containerName;
                        }
                    }
                }

                Log::error('Docker run failed', [
                    'project_id' => $container->project_id,
                    'command' => $runCommand,
                    'error' => $errorOutput,
                    'output' => $runResult->output(),
                ]);

                return $this->runFallbackServer($container, $port);
            }

            $containerId = trim($runResult->output());
            Log::info('Docker container started successfully', [
                'project_id' => $container->project_id,
                'container_id' => $containerId,
                'container_name' => $containerName,
                'port' => $port,
            ]);

            return $containerName;

        } catch (\Exception $e) {
            Log::error('Docker container creation failed', [
                'project_id' => $container->project_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->runFallbackServer($container, $port);
        }
    }

    /**
     * Fallback server when Docker is not available
     */
    private function runFallbackServer(Container $container, int $port): string
    {
        $projectDir = storage_path("app/projects/{$container->project->id}");

        // Use PHP's built-in server as fallback
        $command = "cd {$projectDir} && php -S 0.0.0.0:{$port} > /dev/null 2>&1 &";
        Process::run($command);

        return "fallback-server-{$container->id}";
    }

    /**
     * Get an available port
     */
    private function getAvailablePort(): int
    {
        $basePort = 8000;
        $maxPorts = 1000;

        // Get ports already in use by our containers
        $usedPorts = Container::whereNotNull('port')
            ->where('status', 'running')
            ->pluck('port')
            ->toArray();

        // Also check for ports used by Docker containers
        $dockerPorts = $this->getDockerPortsInUse();

        // Combine both lists
        $allUsedPorts = array_unique(array_merge($usedPorts, $dockerPorts));

        // Find an available port
        for ($i = 0; $i < $maxPorts; $i++) {
            $port = $basePort + $i;

            // Check if port is not used by our containers or Docker
            if (! in_array($port, $allUsedPorts) && $this->isPortAvailable($port)) {
                Log::info('Found available port', [
                    'port' => $port,
                    'used_ports' => $allUsedPorts,
                ]);

                return $port;
            }
        }

        // Fallback to random port if no port is available
        $randomPort = $basePort + rand(0, $maxPorts - 1);
        Log::warning("No available ports found, using random port: {$randomPort}");

        return $randomPort;
    }

    /**
     * Get ports currently in use by Docker containers
     */
    private function getDockerPortsInUse(): array
    {
        try {
            // Get all running Docker containers and their port mappings
            $result = Process::run('docker ps --format "table {{.Ports}}" | grep -o "0.0.0.0:[0-9]*" | cut -d: -f2');

            if ($result->successful()) {
                $ports = array_filter(array_map('intval', explode("\n", trim($result->output()))));
                Log::info('Docker ports in use', ['ports' => $ports]);

                return $ports;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get Docker ports in use', ['error' => $e->getMessage()]);
        }

        return [];
    }

    /**
     * Find an alternative port when the original port is in use
     */
    private function findAlternativePort(int $originalPort): ?int
    {
        $basePort = 8000;
        $maxPorts = 1000;

        // Get all used ports
        $usedPorts = Container::whereNotNull('port')
            ->where('status', 'running')
            ->pluck('port')
            ->toArray();

        $dockerPorts = $this->getDockerPortsInUse();
        $allUsedPorts = array_unique(array_merge($usedPorts, $dockerPorts));

        // Try to find a port starting from the original port + 1
        for ($i = 1; $i < 100; $i++) {
            $port = $originalPort + $i;

            // Don't exceed the maximum port range
            if ($port > $basePort + $maxPorts) {
                break;
            }

            if (! in_array($port, $allUsedPorts) && $this->isPortAvailable($port)) {
                return $port;
            }
        }

        // If no port found after the original, try before it
        for ($i = 1; $i < 100; $i++) {
            $port = $originalPort - $i;

            // Don't go below the base port
            if ($port < $basePort) {
                break;
            }

            if (! in_array($port, $allUsedPorts) && $this->isPortAvailable($port)) {
                return $port;
            }
        }

        return null;
    }

    /**
     * Check if a port is available
     */
    private function isPortAvailable(int $port): bool
    {
        // Try to bind to the port to check if it's available
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (! $socket) {
            return false;
        }

        $result = @socket_bind($socket, '0.0.0.0', $port);
        @socket_close($socket);

        return $result !== false;
    }

    /**
     * Get the number of running containers
     */
    private function getContainerCount(): int
    {
        return Container::where('status', 'running')->count();
    }

    /**
     * Generate project URL
     */
    private function getProjectUrl(Project $project, int $port): string
    {
        // Use subdomain if available, otherwise fall back to port
        if ($project->subdomain) {
            return $project->getProjectUrl();
        }

        // For development, use localhost with port
        if (app()->environment('local', 'testing')) {
            return "http://localhost:{$port}";
        }

        // For production, use the app URL with port
        $baseUrl = config('app.url');

        return "{$baseUrl}:{$port}";
    }

    /**
     * Get external URL for container (accessible from outside Docker)
     */
    public function getExternalUrl(Container $container): string
    {
        if (! $container->port) {
            return '';
        }

        // For development, use localhost
        if (app()->environment('local', 'testing')) {
            return "http://localhost:{$container->port}";
        }

        // For production, use the server's external IP or domain
        $host = config('app.url');

        return "{$host}:{$container->port}";
    }

    /**
     * Get default HTML content for projects without generated code
     */
    private function getDefaultHtml(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Loading...</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .loading { color: #666; }
    </style>
</head>
<body>
    <h1 class="loading">Project is being generated...</h1>
    <p>Please wait while your project is being created.</p>
</body>
</html>';
    }

    /**
     * Generate a subdomain for the project
     */
    private function generateSubdomain(Project $project): string
    {
        // Create a URL-safe subdomain from project name
        $subdomain = strtolower($project->name);
        $subdomain = preg_replace('/[^a-z0-9-]/', '-', $subdomain);
        $subdomain = preg_replace('/-+/', '-', $subdomain);
        $subdomain = trim($subdomain, '-');

        // Add project ID to ensure uniqueness
        return "{$subdomain}-{$project->id}";
    }

    /**
     * Deploy project to production
     */
    public function deployProject(Project $project): bool
    {
        try {
            $container = $project->containers()->where('status', 'running')->first();

            if (! $container) {
                // Create a new container if none exists
                $container = $project->containers()->create([
                    'status' => 'starting',
                ]);
            }

            $success = $this->startContainer($container);

            if ($success && $project->subdomain) {
                // Configure DNS for subdomain
                $this->configureDnsForProject($project);
            }

            return $success;

        } catch (\Exception $e) {
            Log::error('Project deployment failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Configure DNS for project subdomain
     */
    private function configureDnsForProject(Project $project): void
    {
        try {
            if (! $project->subdomain || $project->dns_configured) {
                return;
            }

            $result = $this->cloudflareService->createDnsRecord($project->subdomain);

            if ($result['success']) {
                $project->update([
                    'dns_configured' => true,
                    'preview_url' => $project->getProjectUrl(),
                ]);

                Log::info('DNS configured for project', [
                    'project_id' => $project->id,
                    'subdomain' => $project->subdomain,
                    'url' => $project->getProjectUrl(),
                ]);
            } else {
                Log::warning('Failed to configure DNS for project', [
                    'project_id' => $project->id,
                    'subdomain' => $project->subdomain,
                    'error' => $result['message'],
                ]);
            }

        } catch (\Exception $e) {
            Log::error('DNS configuration failed', [
                'project_id' => $project->id,
                'subdomain' => $project->subdomain,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get container logs
     */
    public function getContainerLogs(Container $container): string
    {
        if (! $container->container_id) {
            return 'No container ID available';
        }

        // In testing environment, return mock logs
        if (app()->environment('testing')) {
            return "Test container logs for container {$container->container_id}\nContainer started successfully\nServer running on port 8000";
        }

        try {
            $result = Process::run("docker logs {$container->container_id}");

            return $result->output();
        } catch (\Exception $e) {
            return "Error retrieving logs: {$e->getMessage()}";
        }
    }

    /**
     * Check container health
     */
    public function checkContainerHealth(Container $container): array
    {
        if (! $container->container_id) {
            return ['status' => 'error', 'message' => 'No container ID'];
        }

        // In testing environment, return mock health status
        if (app()->environment('testing')) {
            return [
                'status' => 'running',
                'message' => 'Container is running (test mode)',
                'healthy' => true,
            ];
        }

        try {
            $result = Process::run("docker inspect {$container->container_id} --format='{{.State.Status}}'");

            if ($result->successful()) {
                $status = trim($result->output());

                return [
                    'status' => $status,
                    'message' => "Container is {$status}",
                    'healthy' => $status === 'running',
                ];
            }

            return ['status' => 'error', 'message' => 'Failed to check container status'];

        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Cleanup existing container
     */
    private function cleanupExistingContainer(string $containerName): void
    {
        try {
            // Stop container if running
            Process::run("docker stop {$containerName} 2>/dev/null || true");
            // Remove container
            Process::run("docker rm {$containerName} 2>/dev/null || true");

            Log::info('Cleaned up existing container', [
                'container_name' => $containerName,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to cleanup existing container', [
                'container_name' => $containerName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Restart a Docker container
     */
    public function restartContainer(Container $container): bool
    {
        try {
            if (! $container->container_id) {
                Log::warning('No container ID available for restart', [
                    'container_id' => $container->id,
                ]);

                return false;
            }

            // In testing environment, simulate restart
            if (app()->environment('testing')) {
                $container->update(['status' => 'running']);

                return true;
            }

            $result = Process::run("docker restart {$container->container_id}");

            if ($result->successful()) {
                $container->update([
                    'status' => 'running',
                    'started_at' => now(),
                ]);

                Log::info('Container restarted successfully', [
                    'container_id' => $container->container_id,
                    'project_id' => $container->project_id,
                ]);

                return true;
            }

            Log::error('Failed to restart container', [
                'container_id' => $container->container_id,
                'error' => $result->errorOutput(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Container restart failed', [
                'container_id' => $container->container_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get container statistics
     */
    public function getContainerStats(Container $container): array
    {
        if (! $container->container_id) {
            return ['error' => 'No container ID available'];
        }

        // In testing environment, return mock stats
        if (app()->environment('testing')) {
            return [
                'status' => 'running',
                'cpu_usage' => '0.5%',
                'memory_usage' => '50MB',
                'uptime' => '2 minutes',
            ];
        }

        try {
            // Get container stats with a simpler format
            $result = Process::run("docker stats {$container->container_id} --no-stream --format '{{.CPUPerc}}|{{.MemUsage}}|{{.Status}}'");

            if ($result->successful()) {
                $output = trim($result->output());
                if (! empty($output)) {
                    $stats = explode('|', $output);

                    return [
                        'status' => trim($stats[2] ?? 'unknown'),
                        'cpu_usage' => trim($stats[0] ?? '0%'),
                        'memory_usage' => trim($stats[1] ?? '0B'),
                        'uptime' => $this->getContainerUptime($container),
                    ];
                }
            }

            // Fallback: try to get basic container info
            $inspectResult = Process::run("docker inspect {$container->container_id} --format '{{.State.Status}}'");
            if ($inspectResult->successful()) {
                $status = trim($inspectResult->output());

                return [
                    'status' => $status,
                    'cpu_usage' => 'N/A',
                    'memory_usage' => 'N/A',
                    'uptime' => $this->getContainerUptime($container),
                ];
            }

            return ['error' => 'Failed to get container stats'];

        } catch (\Exception $e) {
            return ['error' => 'Failed to get container stats: '.$e->getMessage()];
        }
    }

    /**
     * Get container uptime
     */
    private function getContainerUptime(Container $container): string
    {
        try {
            $result = Process::run("docker inspect {$container->container_id} --format='{{.State.StartedAt}}'");

            if ($result->successful()) {
                $startedAt = trim($result->output());
                $startTime = new \DateTime($startedAt);
                $now = new \DateTime;
                $diff = $now->diff($startTime);

                if ($diff->days > 0) {
                    return "{$diff->days} days, {$diff->h} hours";
                } elseif ($diff->h > 0) {
                    return "{$diff->h} hours, {$diff->i} minutes";
                } else {
                    return "{$diff->i} minutes";
                }
            }

            return 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get all running containers
     */
    public function getAllRunningContainers(): array
    {
        try {
            // In testing environment, return mock data
            if (app()->environment('testing')) {
                return [
                    ['id' => 'test-container-1', 'name' => 'lovable-container-1', 'status' => 'running', 'port' => '8000'],
                ];
            }

            $result = Process::run("docker ps --format 'table {{.ID}}\t{{.Names}}\t{{.Status}}\t{{.Ports}}'");

            if ($result->successful()) {
                $lines = explode("\n", trim($result->output()));
                $containers = [];

                for ($i = 1; $i < count($lines); $i++) {
                    $parts = explode("\t", $lines[$i]);
                    if (count($parts) >= 4) {
                        $containers[] = [
                            'id' => trim($parts[0]),
                            'name' => trim($parts[1]),
                            'status' => trim($parts[2]),
                            'ports' => trim($parts[3]),
                        ];
                    }
                }

                return $containers;
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Failed to get running containers', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Clean up old containers and images
     */
    public function cleanupOldResources(): array
    {
        $cleaned = [
            'containers' => 0,
            'images' => 0,
            'errors' => [],
        ];

        try {
            // Remove stopped containers
            $result = Process::run('docker container prune -f');
            if ($result->successful()) {
                $cleaned['containers'] = 1;
            }

            // Remove unused images
            $result = Process::run('docker image prune -f');
            if ($result->successful()) {
                $cleaned['images'] = 1;
            }

            Log::info('Docker cleanup completed', $cleaned);

        } catch (\Exception $e) {
            $cleaned['errors'][] = $e->getMessage();
            Log::error('Docker cleanup failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return $cleaned;
    }

    /**
     * Clean up Docker resources for a project
     */
    public function cleanupProject(Project $project): bool
    {
        try {
            $containers = $project->containers;
            $imageName = "lovable-project-{$project->id}";

            foreach ($containers as $container) {
                // Stop and remove container if it exists
                if ($container->container_id) {
                    $this->cleanupExistingContainer($container->container_id);
                }
            }

            // Remove Docker image
            $this->removeDockerImage($imageName);

            // Clean up project files
            $projectDir = storage_path("app/projects/{$project->id}");
            if (is_dir($projectDir)) {
                $this->removeDirectory($projectDir);
            }

            Log::info('Project cleanup completed', [
                'project_id' => $project->id,
                'image_name' => $imageName,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Project cleanup failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Remove Docker image
     */
    private function removeDockerImage(string $imageName): void
    {
        try {
            // Check if image exists
            $checkResult = Process::run("docker images -q {$imageName}");
            if ($checkResult->successful() && trim($checkResult->output())) {
                // Remove the image
                Process::run("docker rmi {$imageName}");
                Log::info('Docker image removed', ['image_name' => $imageName]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to remove Docker image', [
                'image_name' => $imageName,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove directory recursively
     */
    private function removeDirectory(string $dir): void
    {
        try {
            if (is_dir($dir)) {
                $files = array_diff(scandir($dir), ['.', '..']);
                foreach ($files as $file) {
                    $path = $dir.'/'.$file;
                    is_dir($path) ? $this->removeDirectory($path) : unlink($path);
                }
                rmdir($dir);
                Log::info('Project directory removed', ['directory' => $dir]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to remove project directory', [
                'directory' => $dir,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if Docker is available and running
     */
    public function isDockerAvailable(): bool
    {
        try {
            // In testing environment, always return true
            if (app()->environment('testing')) {
                return true;
            }

            $result = Process::run('docker --version');
            if (! $result->successful()) {
                return false;
            }

            // Check if Docker daemon is running
            $daemonResult = Process::run('docker info');

            return $daemonResult->successful();

        } catch (\Exception $e) {
            Log::warning('Docker availability check failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get project preview URL
     */
    public function getProjectPreviewUrl(Project $project): string
    {
        $container = $project->containers()->where('status', 'running')->first();

        if ($container && $container->url) {
            return $container->url;
        }

        // Fallback to project's preview URL
        return $project->preview_url ?? '';
    }

    /**
     * Get all running containers
     */
    public function getRunningContainers(): array
    {
        return $this->getAllRunningContainers();
    }

    /**
     * Clean up Docker resources
     */
    public function cleanup(): array
    {
        return $this->cleanupOldResources();
    }

    /**
     * Get Docker system information
     */
    public function getDockerInfo(): array
    {
        try {
            if (app()->environment('testing')) {
                return [
                    'available' => true,
                    'version' => 'Docker version 24.0.0 (test)',
                    'containers' => 0,
                    'images' => 0,
                    'status' => 'running',
                ];
            }

            $versionResult = Process::run('docker --version');
            $infoResult = Process::run("docker system df --format '{{.Type}}|{{.Count}}'");
            $psResult = Process::run('docker ps -q | wc -l');

            $containers = 0;
            $images = 0;

            if ($infoResult->successful()) {
                $lines = explode("\n", trim($infoResult->output()));
                foreach ($lines as $line) {
                    $parts = explode('|', $line);
                    if (count($parts) === 2) {
                        $type = trim($parts[0]);
                        $count = (int) trim($parts[1]);

                        if ($type === 'Images') {
                            $images = $count;
                        } elseif ($type === 'Containers') {
                            $containers = $count;
                        }
                    }
                }
            }

            return [
                'available' => $versionResult->successful(),
                'version' => trim($versionResult->output()),
                'containers' => (int) trim($psResult->output()),
                'images' => $images,
                'status' => $versionResult->successful() ? 'running' : 'stopped',
            ];

        } catch (\Exception $e) {
            return [
                'available' => false,
                'version' => 'Unknown',
                'containers' => 0,
                'images' => 0,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
}
