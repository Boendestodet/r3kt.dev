<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use App\Services\FilePermissionService;
use Illuminate\Support\Facades\Log;

class ViteVueController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Generate Vite + Vue + TypeScript specific system prompt
     */
    public function getSystemPrompt(): string
    {
        return 'You are a web developer. Generate a Vite + Vue + TypeScript project as JSON with these exact keys where each value is a STRING (not an object): index.html, src/main.ts, src/App.vue, src/App.css, src/style.css. Each value must be a complete file content as a string. DO NOT include configuration files like package.json, vite.config.ts, tsconfig.json, etc. - these are handled by the system. Focus only on the application code and UI components. Return only valid JSON, no other text.';
    }

    /**
     * Generate Vite + Vue + TypeScript specific user prompt
     */
    public function getUserPrompt(string $prompt): string
    {
        return "Create a Vite + Vue + TypeScript website for: {$prompt}";
    }

    /**
     * Get required files for Vite + Vue + TypeScript projects
     */
    public function getRequiredFiles(): array
    {
        return [
            'package.json',
            'vite.config.ts',
            'tsconfig.json',
            'index.html',
            'src/main.ts',
            'src/App.vue',
            'src/App.css',
            'src/style.css',
            'Dockerfile',
            'docker-compose.yml',
            '.dockerignore',
        ];
    }

    /**
     * Generate mock Vite + Vue + TypeScript project data
     */
    public function generateMockProject(string $prompt, string $projectType = 'portfolio'): array
    {
        switch ($projectType) {
            case 'portfolio':
                return $this->generatePortfolioProject($prompt);
            case 'ecommerce':
                return $this->generateEcommerceProject($prompt);
            case 'blog':
                return $this->generateBlogProject($prompt);
            case 'landing':
                return $this->generateLandingProject($prompt);
            case 'dashboard':
                return $this->generateDashboardProject($prompt);
            default:
                return $this->generateGenericProject($prompt);
        }
    }

    /**
     * Generate Vite + Vue + TypeScript portfolio project
     */
    private function generatePortfolioProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + Vue + TypeScript ecommerce project
     */
    private function generateEcommerceProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + Vue + TypeScript blog project
     */
    private function generateBlogProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + Vue + TypeScript landing page project
     */
    private function generateLandingProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + Vue + TypeScript dashboard project
     */
    private function generateDashboardProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate generic Vite + Vue + TypeScript project
     */
    private function generateGenericProject(string $prompt): array
    {
        return [
            'project_type' => 'vite-vue',
            'name' => 'Vite + Vue + TypeScript Project',
            'description' => 'AI-generated Vite + Vue + TypeScript application',
            'prompt' => $prompt,
            'files' => [
                [
                    'path' => 'index.html',
                    'type' => 'file',
                    'content' => $this->getIndexHtmlContent(),
                ],
                [
                    'path' => 'src/main.ts',
                    'type' => 'file',
                    'content' => $this->getMainTsContent(),
                ],
                [
                    'path' => 'src/App.vue',
                    'type' => 'file',
                    'content' => $this->getAppVueContent($prompt),
                ],
                [
                    'path' => 'src/App.css',
                    'type' => 'file',
                    'content' => $this->getAppCssContent(),
                ],
                [
                    'path' => 'src/style.css',
                    'type' => 'file',
                    'content' => $this->getStyleCssContent(),
                ],
            ],
        ];
    }

    /**
     * Get Vite index.html content for Vue
     */
    private function getIndexHtmlContent(): string
    {
        return '<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vite + Vue + TS</title>
  </head>
  <body>
    <div id="app"></div>
    <script type="module" src="/src/main.ts"></script>
  </body>
</html>';
    }

    /**
     * Get Vite main.ts content for Vue
     */
    private function getMainTsContent(): string
    {
        return 'import { createApp } from \'vue\'
import \'./style.css\'
import App from \'./App.vue\'

createApp(App).mount(\'#app\')';
    }

    /**
     * Get Vite App.vue content
     */
    private function getAppVueContent(string $prompt): string
    {
        return '<script setup lang="ts">
import { ref } from \'vue\'

const count = ref(0)
</script>

<template>
  <div class="app">
    <div class="header">
      <a href="https://vitejs.dev" target="_blank">
        <img src="/vite.svg" class="logo" alt="Vite logo" />
      </a>
      <a href="https://vuejs.org/" target="_blank">
        <img src="/vue.svg" class="logo vue" alt="Vue logo" />
      </a>
    </div>
    
    <h1>Vite + Vue + TypeScript</h1>
    
    <div class="card">
      <button @click="count++">
        Count is {{ count }}
      </button>
      <p>
        Edit <code>src/App.vue</code> and save to test HMR
      </p>
    </div>
    
    <p class="read-the-docs">
      Click on the Vite and Vue logos to learn more
    </p>
  </div>
</template>

<style scoped>
.app {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
}

.header {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-bottom: 2rem;
}

.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}

.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}

.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}

@keyframes logo-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@media (prefers-reduced-motion: no-preference) {
  .logo.vue {
    animation: logo-spin infinite 20s linear;
  }
}

.card {
  padding: 2em;
  margin: 2rem 0;
}

.read-the-docs {
  color: #888;
}

code {
  background: #f0f0f0;
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  font-family: monospace;
}
</style>';
    }

    /**
     * Get Vite App.css content for Vue
     */
    private function getAppCssContent(): string
    {
        return '/* App-specific styles */
.app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

h1 {
  color: #42b883;
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

h2 {
  color: #2c3e50;
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
}

p {
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 1rem;
}

button {
  background-color: #42b883;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #369870;
}

button:active {
  transform: translateY(1px);
}

.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin: 2rem auto;
  max-width: 600px;
}

.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.feature {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  border-left: 4px solid #42b883;
}

.feature h3 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.feature p {
  color: #666;
  font-size: 0.95rem;
  margin: 0;
}';
    }

    /**
     * Get Vite style.css content for Vue
     */
    private function getStyleCssContent(): string
    {
        return ':root {
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
}';
    }

    /**
     * Check if project is Vite + Vue + TypeScript
     */
    public function isViteVueProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        return str_contains($stack, 'vite') && str_contains($stack, 'vue');
    }

    /**
     * Get Vite + Vue + TypeScript specific Docker configuration
     */
    public function getDockerConfig(): array
    {
        return [
            'port' => 5173,
            'build_command' => 'npm run build',
            'start_command' => 'npm run preview',
            'dev_command' => 'npm run dev',
        ];
    }

    /**
     * Create Vite + Vue project files
     */
    public function createProjectFiles(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = ['package.json', 'vite.config.ts', 'tsconfig.json', 'Dockerfile', 'docker-compose.yml', '.dockerignore'];

        foreach ($projectFiles as $filePath => $content) {
            // Skip protected files
            if (in_array($filePath, $protectedFiles)) {
                continue;
            }

            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            try {
                // Create directory with proper permissions
                FilePermissionService::createDirectory($dir, 0755);

                // Create file with proper permissions
                FilePermissionService::createFile($fullPath, $content);
            } catch (\Exception $e) {
                Log::warning('Failed to create file, continuing with deployment', [
                    'file' => $filePath,
                    'error' => $e->getMessage(),
                    'reason' => 'Permission or file system error - container may still work',
                ]);
                
                // Continue with other files instead of failing the entire deployment
                continue;
            }
        }

        // Create configuration files
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Vite Vue project
        $this->createDockerfile($projectDir);
    }

    /**
     * Create Vite Vue configuration files
     */
    public function createConfigFiles(string $projectDir): void
    {
        // Create a lock file to prevent race conditions
        $lockFile = "{$projectDir}/.config-lock";
        
        try {
            // Try to create lock file
            if (file_exists($lockFile)) {
                Log::info('Config files already being created, skipping', [
                    'project_dir' => $projectDir,
                ]);
                return;
            }
            
            file_put_contents($lockFile, 'locked');
            
            // Create vite.config.ts for Vue
            $viteConfig = 'import { defineConfig } from \'vite\'
import vue from \'@vitejs/plugin-vue\'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    host: \'0.0.0.0\',
    port: 5173,
    strictPort: true,
    watch: {
      usePolling: true
    }
  },
  preview: {
    host: \'0.0.0.0\',
    port: 5173,
    strictPort: true
  }
})';

            FilePermissionService::createFile("{$projectDir}/vite.config.ts", $viteConfig);

            // Create package.json for Vue
            $packageJson = [
                'name' => 'vite-vue-project',
                'private' => true,
                'version' => '0.0.0',
                'type' => 'module',
                'scripts' => [
                    'dev' => 'vite',
                    'build' => 'vue-tsc && vite build',
                    'preview' => 'vite preview',
                ],
                'dependencies' => [
                    'vue' => '^3.4.0',
                ],
                'devDependencies' => [
                    '@vitejs/plugin-vue' => '^5.0.0',
                    'typescript' => '^5.2.0',
                    'vite' => '^5.2.0',
                    'vue-tsc' => '^1.8.0',
                ],
            ];

            FilePermissionService::createFile("{$projectDir}/package.json", json_encode($packageJson, JSON_PRETTY_PRINT));

            // Create tsconfig.json for Vue
            $tsconfig = [
                'compilerOptions' => [
                    'target' => 'ES2020',
                    'useDefineForClassFields' => true,
                    'lib' => ['ES2020', 'DOM', 'DOM.Iterable'],
                    'module' => 'ESNext',
                    'skipLibCheck' => true,
                    'moduleResolution' => 'bundler',
                    'allowImportingTsExtensions' => true,
                    'resolveJsonModule' => true,
                    'isolatedModules' => true,
                    'noEmit' => true,
                    'jsx' => 'preserve',
                    'strict' => true,
                    'noUnusedLocals' => true,
                    'noUnusedParameters' => true,
                    'noFallthroughCasesInSwitch' => true,
                ],
                'include' => ['src/**/*.ts', 'src/**/*.d.ts', 'src/**/*.tsx', 'src/**/*.vue'],
                'references' => [['path' => './tsconfig.node.json']],
            ];

            FilePermissionService::createFile("{$projectDir}/tsconfig.json", json_encode($tsconfig, JSON_PRETTY_PRINT));

            // Create tsconfig.node.json
            $tsconfigNode = [
                'compilerOptions' => [
                    'composite' => true,
                    'skipLibCheck' => true,
                    'module' => 'ESNext',
                    'moduleResolution' => 'bundler',
                    'allowSyntheticDefaultImports' => true,
                ],
                'include' => ['vite.config.ts'],
            ];

            FilePermissionService::createFile("{$projectDir}/tsconfig.node.json", json_encode($tsconfigNode, JSON_PRETTY_PRINT));
            
            Log::info('Vue config files created successfully', [
                'project_dir' => $projectDir,
            ]);
            
        } catch (\Exception $e) {
            Log::warning('Failed to create some config files, continuing with deployment', [
                'project_dir' => $projectDir,
                'error' => $e->getMessage(),
                'reason' => 'Permission or file system error - container may still work',
            ]);
        } finally {
            // Remove lock file
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }
        }
    }


    /**
     * Create Vite Vue Dockerfile
     */
    public function createDockerfile(string $projectDir): void
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
     * Check if Vite Vue files already exist in the project directory
     */
    public function hasRequiredFiles(string $projectDir): bool
    {
        $requiredFiles = [
            'index.html',
            'src/main.ts',
            'src/App.vue',
            'src/App.css',
            'src/style.css',
            'package.json',
            'vite.config.ts',
            'tsconfig.json',
        ];

        foreach ($requiredFiles as $file) {
            if (! file_exists("{$projectDir}/{$file}")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create basic Vite Vue fallback structure
     */
    public function createBasicFallback(string $projectDir, Project $project): void
    {
        // Create a basic Vite + Vue + TypeScript structure
        $basicViteVue = [
            'index.html' => '<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>'.($project->name ?? 'AI Project').'</title>
  </head>
  <body>
    <div id="app"></div>
    <script type="module" src="/src/main.ts"></script>
  </body>
</html>',
            'src/main.ts' => 'import { createApp } from \'vue\'
import \'./style.css\'
import App from \'./App.vue\'

createApp(App).mount(\'#app\')',
            'src/App.vue' => '<script setup lang="ts">
import { ref } from \'vue\'

const count = ref(0)
</script>

<template>
  <div class="app">
    <div class="header">
      <a href="https://vitejs.dev" target="_blank">
        <img src="/vite.svg" class="logo" alt="Vite logo" />
      </a>
      <a href="https://vuejs.org/" target="_blank">
        <img src="/vue.svg" class="logo vue" alt="Vue logo" />
      </a>
    </div>
    
    <h1>Vite + Vue + TypeScript</h1>
    
    <div class="card">
      <button @click="count++">
        Count is {{ count }}
      </button>
      <p>
        Edit <code>src/App.vue</code> and save to test HMR
      </p>
    </div>
    
    <p class="read-the-docs">
      Click on the Vite and Vue logos to learn more
    </p>
  </div>
</template>

<style scoped>
.app {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
}

.header {
  display: flex;
  justify-content: center;
  gap: 2rem;
  margin-bottom: 2rem;
}

.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}

.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}

.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}

@keyframes logo-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@media (prefers-reduced-motion: no-preference) {
  .logo.vue {
    animation: logo-spin infinite 20s linear;
  }
}

.card {
  padding: 2em;
  margin: 2rem 0;
}

.read-the-docs {
  color: #888;
}

code {
  background: #f0f0f0;
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  font-family: monospace;
}
</style>',
            'src/App.css' => '/* App-specific styles */
.app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}

h1 {
  color: #42b883;
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

h2 {
  color: #2c3e50;
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
}

p {
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 1rem;
}

button {
  background-color: #42b883;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #369870;
}

button:active {
  transform: translateY(1px);
}

.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin: 2rem auto;
  max-width: 600px;
}

.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.feature {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  border-left: 4px solid #42b883;
}

.feature h3 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.feature p {
  color: #666;
  font-size: 0.95rem;
  margin: 0;
}',
            'src/style.css' => ':root {
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
        foreach ($basicViteVue as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create additional configuration files
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Vite Vue project
        $this->createDockerfile($projectDir);
    }

    /**
     * Get the internal port for Vite Vue projects
     */
    public function getInternalPort(): string
    {
        return '5173';
    }
}
