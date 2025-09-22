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

            // Build and start Docker container
            $containerId = $this->buildAndRunContainer($container);

            if ($containerId) {
                $port = $this->getAvailablePort();
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

        if (! is_dir($projectDir)) {
            mkdir($projectDir, 0755, true);
        }

        // Check project type by looking at the project settings
        $isNextJSProject = $this->isNextJSProject($container->project);
        $isViteProject = $this->isViteProject($container->project);

        if ($isNextJSProject) {
            // Parse the project data as Next.js project
            $projectFiles = json_decode($projectData, true);

            if ($projectFiles && is_array($projectFiles)) {
                // Next.js project structure
                $this->createNextJSProject($projectDir, $projectFiles);
            } else {
                // Fallback: create basic Next.js structure if no generated code
                $this->createBasicNextJSFallback($projectDir, $container->project);
            }
        } elseif ($isViteProject) {
            // Parse the project data as Vite project
            $projectFiles = json_decode($projectData, true);

            if ($projectFiles && is_array($projectFiles)) {
                // Vite project structure
                $this->createViteProject($projectDir, $projectFiles);
            } else {
                // Fallback: create basic Vite structure if no generated code
                $this->createBasicViteFallback($projectDir, $container->project);
            }
        } else {
            // Legacy HTML project (only if not Next.js or Vite)
            file_put_contents("{$projectDir}/index.html", $projectData);
            $this->createDockerfile($projectDir);
            $this->createNginxConfig($projectDir);
        }
    }

    /**
     * Create Next.js project files
     */
    private function createNextJSProject(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = [
            'next.config.js',
            'package.json',
            'tsconfig.json',
            'tailwind.config.js',
            'postcss.config.js',
            '.eslintrc.json',
            'Dockerfile',
            '.dockerignore',
            'docker-compose.yml'
        ];

        foreach ($projectFiles as $filePath => $content) {
            // Skip protected files - we'll create them ourselves
            if (in_array($filePath, $protectedFiles)) {
                Log::info('Skipping protected file from AI generation', [
                    'file' => $filePath,
                    'reason' => 'Protected configuration file'
                ]);
                continue;
            }

            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // BULLETPROOF PROTECTION: Delete any AI-generated config files that might have been written
        foreach ($protectedFiles as $protectedFile) {
            $protectedFilePath = "{$projectDir}/{$protectedFile}";
            if (file_exists($protectedFilePath)) {
                Log::warning('Deleting AI-generated protected file', [
                    'file' => $protectedFile,
                    'reason' => 'AI ignored protection instructions'
                ]);
                unlink($protectedFilePath);
            }
        }

        // Ensure we have a complete package.json with all Next.js dependencies
        $this->ensureCompletePackageJson($projectDir, $projectFiles);

        // Create Dockerfile for Next.js project
        $this->createDockerfile($projectDir);
    }

    /**
     * Ensure package.json has all required Next.js dependencies
     */
    private function ensureCompletePackageJson(string $projectDir, array $projectFiles): void
    {
        $packageJsonPath = "{$projectDir}/package.json";

        // Parse existing package.json or create default
        $packageJson = [];
        if (file_exists($packageJsonPath)) {
            $existingContent = file_get_contents($packageJsonPath);
            $packageJson = json_decode($existingContent, true) ?: [];
        }

        // Set default values if not present
        $packageJson['name'] = $packageJson['name'] ?? 'ai-generated-project';
        $packageJson['version'] = $packageJson['version'] ?? '0.1.0';
        $packageJson['private'] = $packageJson['private'] ?? true;

        // Ensure scripts are present (prioritize our enhanced scripts)
        $packageJson['scripts'] = array_merge($packageJson['scripts'] ?? [], [
            'dev' => 'next dev --turbopack',
            'build' => 'next build --turbopack',
            'start' => 'next start',
            'lint' => 'biome check',
            'format' => 'biome format --write',
        ]);

        // Add required dependencies
        $packageJson['dependencies'] = array_merge([
            'react' => '19.1.0',
            'react-dom' => '19.1.0',
            'next' => '15.5.3',
        ], $packageJson['dependencies'] ?? []);

        // Add dev dependencies
        $packageJson['devDependencies'] = array_merge([
            'typescript' => '^5',
            '@types/node' => '^20',
            '@types/react' => '^19',
            '@types/react-dom' => '^19',
            '@tailwindcss/postcss' => '^4',
            'tailwindcss' => '^4',
            '@biomejs/biome' => '2.2.0',
        ], $packageJson['devDependencies'] ?? []);

        // Write the complete package.json
        file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Create additional required configuration files
        $this->createNextJSConfigFiles($projectDir);
    }

    /**
     * Create additional Next.js configuration files
     */
    private function createNextJSConfigFiles(string $projectDir): void
    {
        // Create tailwind.config.js for Tailwind CSS v4
        $tailwindConfigPath = "{$projectDir}/tailwind.config.js";
        if (! file_exists($tailwindConfigPath)) {
            $tailwindConfig = <<<'JS'
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './pages/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}',
    './app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
JS;
            file_put_contents($tailwindConfigPath, $tailwindConfig);
        }

        // Create postcss.config.js for Tailwind CSS v4
        $postcssConfigPath = "{$projectDir}/postcss.config.js";
        if (! file_exists($postcssConfigPath)) {
            $postcssConfig = <<<'JS'
module.exports = {
  plugins: {
    '@tailwindcss/postcss': {},
  },
}
JS;
            file_put_contents($postcssConfigPath, $postcssConfig);
        }

        // Create biome.json configuration
        $biomeConfigPath = "{$projectDir}/biome.json";
        if (! file_exists($biomeConfigPath)) {
            $biomeConfig = <<<'JSON'
{
  "$schema": "https://biomejs.dev/schemas/1.9.4/schema.json",
  "organizeImports": {
    "enabled": true
  },
  "linter": {
    "enabled": true,
    "rules": {
      "recommended": true
    }
  },
  "formatter": {
    "enabled": true,
    "indentStyle": "space",
    "indentWidth": 2
  },
  "javascript": {
    "formatter": {
      "quoteStyle": "double",
      "semicolons": "asNeeded"
    }
  }
}
JSON;
            file_put_contents($biomeConfigPath, $biomeConfig);
        }
    }

    /**
     * Create additional Vite configuration files
     */
    private function createViteConfigFiles(string $projectDir): void
    {
        // Create vite.config.ts (ALWAYS overwrite to ensure correct Docker config)
        $viteConfigPath = "{$projectDir}/vite.config.ts";
        $viteConfig = <<<'TS'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 5173,
  },
})
TS;
        
        // Ensure we can write the file by fixing permissions if needed
        if (file_exists($viteConfigPath)) {
            chmod($viteConfigPath, 0644);
        }
        
        $result = file_put_contents($viteConfigPath, $viteConfig);
        
        if ($result === false) {
            Log::error('Failed to create vite.config.ts', [
                'project_dir' => $projectDir,
                'file' => $viteConfigPath,
            ]);
        }

        // Create tsconfig.json
        $tsconfigPath = "{$projectDir}/tsconfig.json";
        if (! file_exists($tsconfigPath)) {
            $tsconfig = <<<'JSON'
{
  "compilerOptions": {
    "target": "ES2020",
    "useDefineForClassFields": true,
    "lib": ["ES2020", "DOM", "DOM.Iterable"],
    "module": "ESNext",
    "skipLibCheck": true,

    /* Bundler mode */
    "moduleResolution": "bundler",
    "allowImportingTsExtensions": true,
    "resolveJsonModule": true,
    "isolatedModules": true,
    "noEmit": true,
    "jsx": "react-jsx",

    /* Linting */
    "strict": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noFallthroughCasesInSwitch": true
  },
  "include": ["src"],
  "references": [{ "path": "./tsconfig.node.json" }]
}
JSON;
            file_put_contents($tsconfigPath, $tsconfig);
        }

        // Create tsconfig.node.json
        $tsconfigNodePath = "{$projectDir}/tsconfig.node.json";
        if (! file_exists($tsconfigNodePath)) {
            $tsconfigNode = <<<'JSON'
{
  "compilerOptions": {
    "composite": true,
    "skipLibCheck": true,
    "module": "ESNext",
    "moduleResolution": "bundler",
    "allowSyntheticDefaultImports": true
  },
  "include": ["vite.config.ts"]
}
JSON;
            file_put_contents($tsconfigNodePath, $tsconfigNode);
        }

        // Create tailwind.config.js for Tailwind CSS v3
        $tailwindConfigPath = "{$projectDir}/tailwind.config.js";
        if (! file_exists($tailwindConfigPath)) {
            $tailwindConfig = <<<'JS'
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
JS;
            file_put_contents($tailwindConfigPath, $tailwindConfig);
        }

        // Create postcss.config.js for Tailwind CSS
        $postcssConfigPath = "{$projectDir}/postcss.config.js";
        if (! file_exists($postcssConfigPath)) {
            $postcssConfig = <<<'JS'
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
JS;
            file_put_contents($postcssConfigPath, $postcssConfig);
        }

        // Create .eslintrc.cjs
        $eslintConfigPath = "{$projectDir}/.eslintrc.cjs";
        if (! file_exists($eslintConfigPath)) {
            $eslintConfig = <<<'JS'
module.exports = {
  root: true,
  env: { browser: true, es2020: true },
  extends: [
    'eslint:recommended',
    '@typescript-eslint/recommended',
    'plugin:react-hooks/recommended',
  ],
  ignorePatterns: ['dist', '.eslintrc.cjs'],
  parser: '@typescript-eslint/parser',
  plugins: ['react-refresh'],
  rules: {
    'react-refresh/only-export-components': [
      'warn',
      { allowConstantExport: true },
    ],
  },
}
JS;
            file_put_contents($eslintConfigPath, $eslintConfig);
        }

        // Create package.json
        $packageJsonPath = "{$projectDir}/package.json";
        if (! file_exists($packageJsonPath)) {
            $packageJson = <<<'JSON'
{
  "name": "vite-react-ts",
  "private": true,
  "version": "0.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "tsc && vite build",
    "lint": "eslint . --ext ts,tsx --report-unused-disable-directives --max-warnings 0",
    "preview": "vite preview"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@types/react": "^18.2.66",
    "@types/react-dom": "^18.2.22",
    "@typescript-eslint/eslint-plugin": "^7.2.0",
    "@typescript-eslint/parser": "^7.2.0",
    "@vitejs/plugin-react": "^4.2.1",
    "eslint": "^8.57.0",
    "eslint-plugin-react-hooks": "^4.6.0",
    "eslint-plugin-react-refresh": "^0.4.6",
    "typescript": "^5.2.2",
    "vite": "^5.2.0",
    "tailwindcss": "^3.4.0",
    "autoprefixer": "^10.4.17",
    "postcss": "^8.4.35"
  }
}
JSON;
            file_put_contents($packageJsonPath, $packageJson);
        }
    }

    /**
     * Create Dockerfile for the project
     */
    private function createDockerfile(string $projectDir): void
    {
        // Check project type by looking for specific files
        if (file_exists("{$projectDir}/next.config.js") || file_exists("{$projectDir}/app")) {
            $this->createNextJSDockerfile($projectDir);
        } elseif (file_exists("{$projectDir}/vite.config.ts") || file_exists("{$projectDir}/vite.config.js")) {
            $this->createViteDockerfile($projectDir);
        } else {
            // Default to HTML for projects without specific framework detection
            $this->createHTMLDockerfile($projectDir);
        }
    }

    /**
     * Create Dockerfile for HTML projects
     */
    private function createHTMLDockerfile(string $projectDir): void
    {
        $dockerfile = 'FROM nginx:alpine

# Copy project files
COPY . /usr/share/nginx/html/

# Copy nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose port 80
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]';

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Create Dockerfile for Next.js projects (Development Mode for Live Previews)
     */
    private function createNextJSDockerfile(string $projectDir): void
    {
        $dockerfile = 'FROM node:18-alpine

WORKDIR /app

# Copy package files
COPY package.json ./

# Install dependencies
RUN npm install

# Copy source code
COPY . .

# Expose port
EXPOSE 3000

# Start the development server for live previews
CMD ["npm", "run", "dev"]';

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Create Dockerfile for Vite projects (Development Mode for Live Previews)
     */
    private function createViteDockerfile(string $projectDir): void
    {
        $dockerfile = 'FROM node:18-alpine

WORKDIR /app

# Copy package files and config files first
COPY package.json ./
COPY vite.config.ts ./
COPY tsconfig.json ./
COPY tsconfig.node.json ./

# Install dependencies
RUN npm install

# Copy remaining source code
COPY . .

# Expose port
EXPOSE 5173

# Start the development server for live previews
CMD ["npm", "run", "dev"]';

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Create nginx configuration
     */
    private function createNginxConfig(string $projectDir): void
    {
        $nginxConfig = "server {
    listen 80;
    server_name _;
    root /usr/share/nginx/html;
    index index.html;

    location / {
        try_files \$uri \$uri/ /index.html;
    }

    # Enable gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Cache static assets
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control \"public, immutable\";
    }
}";

        file_put_contents("{$projectDir}/nginx.conf", $nginxConfig);
    }

    /**
     * Get the internal port for the project based on its type
     */
    private function getInternalPort(string $projectDir): string
    {
        // Check for Next.js project
        if (file_exists("{$projectDir}/next.config.js") || file_exists("{$projectDir}/app")) {
            return '3000';
        }
        
        // Check for Vite project
        if (file_exists("{$projectDir}/vite.config.ts") || file_exists("{$projectDir}/vite.config.js")) {
            return '5173';
        }
        
        // Default to HTML/nginx port
        return '80';
    }

    /**
     * Build and run Docker container for the project
     */
    private function buildAndRunContainer(Container $container): ?string
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

                return $this->runFallbackServer($container);
            }

            $projectDir = storage_path("app/projects/{$container->project->id}");
            $imageName = "lovable-project-{$container->project->id}";
            $containerName = "lovable-container-{$container->project->id}";
            $port = $this->getAvailablePort();

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

                return $this->runFallbackServer($container);
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
                Log::error('Docker run failed', [
                    'project_id' => $container->project_id,
                    'command' => $runCommand,
                    'error' => $runResult->errorOutput(),
                    'output' => $runResult->output(),
                ]);

                return $this->runFallbackServer($container);
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

            return $this->runFallbackServer($container);
        }
    }

    /**
     * Fallback server when Docker is not available
     */
    private function runFallbackServer(Container $container): string
    {
        $projectDir = storage_path("app/projects/{$container->project->id}");
        $port = $this->getAvailablePort();

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

        // Find an available port
        for ($i = 0; $i < $maxPorts; $i++) {
            $port = $basePort + $i;

            // Check if port is not used by our containers and is available on system
            if (! in_array($port, $usedPorts) && $this->isPortAvailable($port)) {
                return $port;
            }
        }

        // Fallback to random port if no port is available
        $randomPort = $basePort + rand(0, $maxPorts - 1);
        Log::warning("No available ports found, using random port: {$randomPort}");

        return $randomPort;
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

    /**
     * Check if this is a Next.js project based on project settings
     */
    private function isNextJSProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = $settings['stack'] ?? 'nextjs';
        
        return $stack === 'nextjs' || $stack === 'Next.js';
    }

    /**
     * Check if this is a Vite project based on project settings
     */
    private function isViteProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = $settings['stack'] ?? '';
        
        return $stack === 'vite' || $stack === 'Vite + React';
    }

    /**
     * Create basic Next.js fallback when no generated code is available
     */
    private function createBasicNextJSFallback(string $projectDir, Project $project): void
    {
        // Create a basic Next.js structure
        $basicNextJS = [
            'package.json' => json_encode([
                'name' => strtolower($project->slug ?? 'ai-project'),
                'version' => '0.1.0',
                'private' => true,
                'scripts' => [
                    'dev' => 'next dev --turbopack',
                    'build' => 'next build --turbopack',
                    'start' => 'next start',
                    'lint' => 'biome check',
                ],
                'dependencies' => [
                    'react' => '19.1.0',
                    'react-dom' => '19.1.0',
                    'next' => '15.5.3',
                ],
                'devDependencies' => [
                    'typescript' => '^5',
                    '@types/node' => '^20',
                    '@types/react' => '^19',
                    '@types/react-dom' => '^19',
                    '@tailwindcss/postcss' => '^4',
                    'tailwindcss' => '^4',
                    '@biomejs/biome' => '2.2.0',
                ],
            ], JSON_PRETTY_PRINT),
            'app/page.tsx' => 'export default function Home() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-center">
      <h1 className="text-4xl font-bold">Welcome to Your Next.js Project</h1>
      <p className="mt-4 text-lg text-gray-600">This project is ready for AI code generation!</p>
    </main>
  )
}',
            'app/layout.tsx' => 'import type { Metadata } from \'next\'
import \'./globals.css\'

export const metadata: Metadata = {
  title: \'' . ($project->name ?? 'AI Project') . '\',
  description: \'AI Generated Next.js Project\',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  )
}',
            'app/globals.css' => '@import "tailwindcss";',
        ];

        // Create the files
        foreach ($basicNextJS as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create additional configuration files
        $this->createNextJSConfigFiles($projectDir);
        
        // Create Dockerfile for Next.js project
        $this->createDockerfile($projectDir);
    }

    /**
     * Create Vite project files
     */
    private function createViteProject(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = [
            'vite.config.ts',
            'package.json',
            'tsconfig.json',
            'tsconfig.node.json',
            'tailwind.config.js',
            'postcss.config.js',
            '.eslintrc.cjs',
            'Dockerfile',
            '.dockerignore',
            'docker-compose.yml'
        ];

        foreach ($projectFiles as $filePath => $content) {
            // Skip protected files - we'll create them ourselves
            if (in_array($filePath, $protectedFiles)) {
                Log::info('Skipping protected file from AI generation', [
                    'file' => $filePath,
                    'reason' => 'Protected configuration file'
                ]);
                continue;
            }

            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // BULLETPROOF PROTECTION: Delete any AI-generated config files that might have been written
        foreach ($protectedFiles as $protectedFile) {
            $protectedFilePath = "{$projectDir}/{$protectedFile}";
            if (file_exists($protectedFilePath)) {
                Log::warning('Deleting AI-generated protected file', [
                    'file' => $protectedFile,
                    'reason' => 'AI ignored protection instructions'
                ]);
                unlink($protectedFilePath);
            }
        }

        // Create additional configuration files (these will overwrite any AI attempts)
        $this->createViteConfigFiles($projectDir);
        
        // Create Dockerfile for Vite project
        $this->createViteDockerfile($projectDir);
    }

    /**
     * Create basic Vite fallback when no generated code is available
     */
    private function createBasicViteFallback(string $projectDir, Project $project): void
    {
        // Create a basic Vite + React + TypeScript structure
        $basicVite = [
            'package.json' => json_encode([
                'name' => strtolower($project->slug ?? 'ai-project'),
                'private' => true,
                'version' => '0.0.0',
                'type' => 'module',
                'scripts' => [
                    'dev' => 'vite',
                    'build' => 'tsc && vite build',
                    'lint' => 'eslint . --ext ts,tsx --report-unused-disable-directives --max-warnings 0',
                    'preview' => 'vite preview',
                ],
                'dependencies' => [
                    'react' => '^18.2.0',
                    'react-dom' => '^18.2.0',
                ],
                'devDependencies' => [
                    '@types/react' => '^18.2.66',
                    '@types/react-dom' => '^18.2.22',
                    '@typescript-eslint/eslint-plugin' => '^7.2.0',
                    '@typescript-eslint/parser' => '^7.2.0',
                    '@vitejs/plugin-react' => '^4.2.1',
                    'eslint' => '^8.57.0',
                    'eslint-plugin-react-hooks' => '^4.6.0',
                    'eslint-plugin-react-refresh' => '^0.4.6',
                    'typescript' => '^5.2.2',
                    'vite' => '^5.2.0',
                    'tailwindcss' => '^3.4.0',
                    'autoprefixer' => '^10.4.17',
                    'postcss' => '^8.4.35',
                ],
            ], JSON_PRETTY_PRINT),
            'index.html' => '<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>' . ($project->name ?? 'AI Project') . '</title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.tsx"></script>
  </body>
</html>',
            'src/main.tsx' => 'import React from \'react\'
import ReactDOM from \'react-dom/client\'
import App from \'./App.tsx\'
import \'./index.css\'

ReactDOM.createRoot(document.getElementById(\'root\')!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)',
            'src/App.tsx' => 'import { useState } from \'react\'
import \'./App.css\'

function App() {
  const [count, setCount] = useState(0)

  return (
    <div className="App">
      <div className="card">
        <h1>Welcome to Your Vite + React Project</h1>
        <p>This project is ready for AI code generation!</p>
        <button onClick={() => setCount((count) => count + 1)}>
          count is {count}
        </button>
        <p>
          Edit <code>src/App.tsx</code> and save to test HMR
        </p>
      </div>
    </div>
  )
}

export default App',
            'src/App.css' => '.App {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
}

.card {
  padding: 2em;
}

button {
  border-radius: 8px;
  border: 1px solid transparent;
  padding: 0.6em 1.2em;
  font-size: 1em;
  font-weight: 500;
  font-family: inherit;
  background-color: #1a1a1a;
  color: white;
  cursor: pointer;
  transition: border-color 0.25s;
}

button:hover {
  border-color: #646cff;
}

button:focus,
button:focus-visible {
  outline: 4px auto -webkit-focus-ring-color;
}

code {
  background-color: #1a1a1a;
  padding: 0.2em 0.4em;
  border-radius: 4px;
  font-family: monospace;
}',
            'src/index.css' => '@import "tailwindcss/base";
@import "tailwindcss/components";
@import "tailwindcss/utilities";

:root {
  font-family: Inter, system-ui, Avenir, Helvetica, Arial, sans-serif;
  line-height: 1.5;
  font-weight: 400;

  color-scheme: light dark;
  color: rgba(255, 255, 255, 0.87);
  background-color: #242424;

  font-synthesis: none;
  text-rendering: optimizeLegibility;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  -webkit-text-size-adjust: 100%;
}

a {
  font-weight: 500;
  color: #646cff;
  text-decoration: inherit;
}
a:hover {
  color: #535bf2;
}

body {
  margin: 0;
  display: flex;
  place-items: center;
  min-width: 320px;
  min-height: 100vh;
}

h1 {
  font-size: 3.2em;
  line-height: 1.1;
}

#root {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
}

@media (prefers-color-scheme: light) {
  :root {
    color: #213547;
    background-color: #ffffff;
  }
  a:hover {
    color: #747bff;
  }
  button {
    background-color: #f9f9f9;
  }
}',
        ];

        // Create the files
        foreach ($basicVite as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create additional configuration files
        $this->createViteConfigFiles($projectDir);
        
        // Create Dockerfile for Vite project
        $this->createViteDockerfile($projectDir);
    }

    /**
     * Get default HTML content
     */
    private function getDefaultHtml(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Preview</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 2rem; text-align: center; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 1rem; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Project Preview</h1>
        <p>This is a preview of your project. Generate some content using AI prompts to see your website here!</p>
    </div>
</body>
</html>';
    }

    /**
     * Fix vite.config.ts to ensure it has correct server configuration for Docker
     */
    private function fixViteConfigForDocker(string $projectDir): void
    {
        $viteConfigPath = "{$projectDir}/vite.config.ts";
        
        if (file_exists($viteConfigPath)) {
            // Read the current config
            $currentConfig = file_get_contents($viteConfigPath);
            
            // Check if it already has the correct server configuration AND correct plugin
            $hasCorrectServer = strpos($currentConfig, "host: '0.0.0.0'") !== false && 
                               strpos($currentConfig, "port: 5173") !== false;
            
            // Check package.json to see what plugin should be used
            $packageJsonPath = "{$projectDir}/package.json";
            $packageJson = [];
            if (file_exists($packageJsonPath)) {
                $packageJson = json_decode(file_get_contents($packageJsonPath), true);
            }
            $devDeps = $packageJson['devDependencies'] ?? [];
            $hasCorrectPlugin = false;
            
            if (isset($devDeps['@vitejs/plugin-react']) && strpos($currentConfig, '@vitejs/plugin-react') !== false) {
                $hasCorrectPlugin = true;
            } elseif (isset($devDeps['@vitejs/plugin-react-refresh']) && strpos($currentConfig, '@vitejs/plugin-react-refresh') !== false) {
                $hasCorrectPlugin = true;
            }
            
            if ($hasCorrectServer && $hasCorrectPlugin) {
                // Already has correct configuration, no need to fix
                return;
            }
            
            // Determine which plugin to use based on what's installed
            $useReactPlugin = isset($devDeps['@vitejs/plugin-react']);
            $useReactRefreshPlugin = isset($devDeps['@vitejs/plugin-react-refresh']);
            
            if ($useReactPlugin) {
                // Use @vitejs/plugin-react (newer Vite versions)
                $correctConfig = <<<'TS'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 5173,
  },
})
TS;
            } elseif ($useReactRefreshPlugin) {
                // Use @vitejs/plugin-react-refresh (older Vite versions)
                $correctConfig = <<<'TS'
import { defineConfig } from 'vite'
import reactRefresh from '@vitejs/plugin-react-refresh'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [reactRefresh()],
  server: {
    host: '0.0.0.0',
    port: 5173,
  },
})
TS;
            } else {
                // Fallback to basic config without plugins
                $correctConfig = <<<'TS'
import { defineConfig } from 'vite'

// https://vitejs.dev/config/
export default defineConfig({
  server: {
    host: '0.0.0.0',
    port: 5173,
  },
})
TS;
            }
            
            // Write the correct configuration
            $result = file_put_contents($viteConfigPath, $correctConfig);
            
            if ($result === false) {
                // If we can't write due to permissions, try to fix ownership
                $projectDir = dirname($viteConfigPath);
                chmod($projectDir, 0755);
                chmod($viteConfigPath, 0644);
                
                // Try again
                $result = file_put_contents($viteConfigPath, $correctConfig);
                
                if ($result === false) {
                    Log::error('Failed to fix vite.config.ts due to permission issues', [
                        'project_dir' => $projectDir,
                        'file' => $viteConfigPath,
                    ]);
                    return;
                }
            }
            
            Log::info('Fixed vite.config.ts for Docker compatibility', [
                'project_dir' => $projectDir,
            ]);
        }
    }
}
