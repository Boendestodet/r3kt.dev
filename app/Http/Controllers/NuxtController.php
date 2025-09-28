<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use App\Services\FilePermissionService;
use Illuminate\Support\Facades\Log;

class NuxtController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Generate Nuxt 3 specific system prompt
     */
    public function getSystemPrompt(): string
    {
        return 'You are a web developer. Generate a Nuxt 3 project as JSON with these exact keys where each value is a STRING (not an object): app.vue, pages/index.vue, components/Header.vue, components/Footer.vue, assets/css/main.css. Each value must be a complete file content as a string. DO NOT include configuration files like package.json, nuxt.config.ts, tsconfig.json, etc. - these are handled by the system. Focus only on the application code and UI components. Return only valid JSON, no other text.';
    }

    /**
     * Generate Nuxt 3 specific user prompt
     */
    public function getUserPrompt(string $prompt): string
    {
        return "Create a Nuxt 3 website for: {$prompt}";
    }

    /**
     * Get required files for Nuxt 3 projects
     */
    public function getRequiredFiles(): array
    {
        return [
            'package.json',
            'nuxt.config.ts',
            'tsconfig.json',
            'app.vue',
            'pages/index.vue',
            'components/Header.vue',
            'components/Footer.vue',
            'assets/css/main.css',
            'Dockerfile',
            'docker-compose.yml',
            '.dockerignore',
        ];
    }

    /**
     * Generate mock Nuxt 3 project data
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
     * Generate Nuxt 3 portfolio project
     */
    private function generatePortfolioProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Nuxt 3 ecommerce project
     */
    private function generateEcommerceProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Nuxt 3 blog project
     */
    private function generateBlogProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Nuxt 3 landing page project
     */
    private function generateLandingProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Nuxt 3 dashboard project
     */
    private function generateDashboardProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate generic Nuxt 3 project
     */
    private function generateGenericProject(string $prompt): array
    {
        return [
            'project_type' => 'nuxt3',
            'name' => 'Nuxt 3 Project',
            'description' => 'AI-generated Nuxt 3 application',
            'prompt' => $prompt,
            'files' => [
                [
                    'path' => 'app.vue',
                    'type' => 'file',
                    'content' => $this->getAppVueContent(),
                ],
                [
                    'path' => 'pages/index.vue',
                    'type' => 'file',
                    'content' => $this->getIndexVueContent($prompt),
                ],
                [
                    'path' => 'components/Header.vue',
                    'type' => 'file',
                    'content' => $this->getHeaderVueContent(),
                ],
                [
                    'path' => 'components/Footer.vue',
                    'type' => 'file',
                    'content' => $this->getFooterVueContent(),
                ],
                [
                    'path' => 'assets/css/main.css',
                    'type' => 'file',
                    'content' => $this->getMainCssContent(),
                ],
            ],
        ];
    }

    /**
     * Get Nuxt 3 app.vue content
     */
    private function getAppVueContent(): string
    {
        return '<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<style>
  @import "~/assets/css/main.css";
</style>';
    }

    /**
     * Get Nuxt 3 index.vue content
     */
    private function getIndexVueContent(string $prompt): string
    {
        return '<template>
  <div class="container mx-auto px-4 py-8">
    <Header />
    
    <main class="text-center">
      <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">
        Welcome to Your Nuxt 3 Project
      </h1>
      <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
        This is an AI-generated Nuxt 3 application ready for development.
      </p>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
          <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Fast</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Built with Nuxt 3 for optimal performance and SEO.
          </p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
          <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Modern</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Uses the latest Vue 3 and Nuxt 3 technologies.
          </p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
          <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">TypeScript</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Full TypeScript support for enhanced development experience.
          </p>
        </div>
      </div>
    </main>
    
    <Footer />
  </div>
</template>

<script setup lang="ts">
// Page meta
useHead({
  title: "Welcome to Nuxt 3",
  meta: [
    { name: "description", content: "AI Generated Nuxt 3 Project" }
  ]
})
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>';
    }

    /**
     * Get Nuxt 3 Header.vue content
     */
    private function getHeaderVueContent(): string
    {
        return '<template>
  <header class="bg-white dark:bg-gray-800 shadow-sm">
    <nav class="container mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <NuxtLink to="/" class="text-2xl font-bold text-gray-900 dark:text-white">
            Nuxt App
          </NuxtLink>
        </div>
        
        <div class="hidden md:flex items-center space-x-6">
          <NuxtLink to="/" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
            Home
          </NuxtLink>
          <NuxtLink to="/about" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
            About
          </NuxtLink>
          <NuxtLink to="/contact" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
            Contact
          </NuxtLink>
        </div>
        
        <div class="md:hidden">
          <button class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup lang="ts">
// Header component logic
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>';
    }

    /**
     * Get Nuxt 3 Footer.vue content
     */
    private function getFooterVueContent(): string
    {
        return '<template>
  <footer class="bg-gray-800 text-white py-8 mt-16">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-lg font-semibold mb-4">About</h3>
          <p class="text-gray-300">
            This is an AI-generated Nuxt 3 project built with modern web technologies.
          </p>
        </div>
        
        <div>
          <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-2">
            <li><NuxtLink to="/" class="text-gray-300 hover:text-white">Home</NuxtLink></li>
            <li><NuxtLink to="/about" class="text-gray-300 hover:text-white">About</NuxtLink></li>
            <li><NuxtLink to="/contact" class="text-gray-300 hover:text-white">Contact</NuxtLink></li>
          </ul>
        </div>
        
        <div>
          <h3 class="text-lg font-semibold mb-4">Tech Stack</h3>
          <ul class="space-y-2 text-gray-300">
            <li>Nuxt 3</li>
            <li>Vue 3</li>
            <li>TypeScript</li>
            <li>Tailwind CSS</li>
          </ul>
        </div>
      </div>
      
      <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
        <p>&copy; 2024 AI Generated Nuxt 3 Project. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
// Footer component logic
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>';
    }

    /**
     * Get Nuxt 3 main CSS content
     */
    private function getMainCssContent(): string
    {
        return '@import "tailwindcss/base";
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
     * Check if project is Nuxt 3
     */
    public function isNuxtProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        return str_contains($stack, 'nuxt');
    }

    /**
     * Get Nuxt 3 specific Docker configuration
     */
    public function getDockerConfig(): array
    {
        return [
            'port' => 3000,
            'build_command' => 'npm run build',
            'start_command' => 'npm run preview',
            'dev_command' => 'npm run dev',
        ];
    }

    /**
     * Create Nuxt 3 project files
     */
    public function createProjectFiles(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = [
            'nuxt.config.ts',
            'package.json',
            'tsconfig.json',
            'tailwind.config.js',
            'postcss.config.js',
            '.eslintrc.cjs',
            'Dockerfile',
            '.dockerignore',
            'docker-compose.yml',
        ];

        foreach ($projectFiles as $filePath => $content) {
            // Skip protected files - we'll create them ourselves
            if (in_array($filePath, $protectedFiles)) {
                Log::info('Skipping protected file from AI generation', [
                    'file' => $filePath,
                    'reason' => 'Protected configuration file',
                ]);

                continue;
            }

            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            // Create directory with proper permissions
            FilePermissionService::createDirectory($dir, 0755);

            // Create file with proper permissions
            FilePermissionService::createFile($fullPath, $content);
        }

        // BULLETPROOF PROTECTION: Delete any AI-generated config files that might have been written
        foreach ($protectedFiles as $protectedFile) {
            $protectedFilePath = "{$projectDir}/{$protectedFile}";
            if (file_exists($protectedFilePath)) {
                Log::warning('Deleting AI-generated protected file', [
                    'file' => $protectedFile,
                    'reason' => 'AI ignored protection instructions',
                ]);
                unlink($protectedFilePath);
            }
        }

        // Create additional configuration files (these will overwrite any AI attempts)
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Nuxt 3 project
        $this->createDockerfile($projectDir);
    }

    /**
     * Create additional Nuxt 3 configuration files
     */
    public function createConfigFiles(string $projectDir): void
    {
        // Create nuxt.config.ts
        $nuxtConfigPath = "{$projectDir}/nuxt.config.ts";
        $nuxtConfig = <<<'TS'
// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
  modules: ['@nuxtjs/tailwindcss'],
  server: {
    host: '0.0.0.0',
    port: 3000,
  },
})
TS;

        // Ensure we can write the file by fixing permissions if needed
        if (file_exists($nuxtConfigPath)) {
            chmod($nuxtConfigPath, 0644);
        }

        $result = file_put_contents($nuxtConfigPath, $nuxtConfig);

        if ($result === false) {
            Log::error('Failed to create nuxt.config.ts', [
                'project_dir' => $projectDir,
                'file' => $nuxtConfigPath,
            ]);
        }

        // Create tsconfig.json
        $tsconfigPath = "{$projectDir}/tsconfig.json";
        if (! file_exists($tsconfigPath)) {
            $tsconfig = <<<'JSON'
{
  "extends": "./.nuxt/tsconfig.json"
}
JSON;
            file_put_contents($tsconfigPath, $tsconfig);
        }

        // Create tailwind.config.js
        $tailwindConfigPath = "{$projectDir}/tailwind.config.js";
        if (! file_exists($tailwindConfigPath)) {
            $tailwindConfig = <<<'JS'
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./app.vue",
    "./error.vue"
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
JS;
            file_put_contents($tailwindConfigPath, $tailwindConfig);
        }

        // Create postcss.config.js
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
  env: {
    browser: true,
    node: true
  },
  parserOptions: {
    parser: '@typescript-eslint/parser'
  },
  extends: [
    '@nuxtjs',
    '@nuxtjs/eslint-config-typescript'
  ],
  plugins: [
    '@typescript-eslint'
  ],
  // add your custom rules here
  rules: {}
}
JS;
            file_put_contents($eslintConfigPath, $eslintConfig);
        }

        // Create package.json
        $packageJsonPath = "{$projectDir}/package.json";
        if (! file_exists($packageJsonPath)) {
            $packageJson = <<<'JSON'
{
  "name": "nuxt3-project",
  "private": true,
  "scripts": {
    "build": "nuxt build",
    "dev": "nuxt dev",
    "generate": "nuxt generate",
    "preview": "nuxt preview",
    "postinstall": "nuxt prepare"
  },
  "devDependencies": {
    "@nuxt/devtools": "latest",
    "@nuxtjs/eslint-config-typescript": "^12.0.0",
    "@nuxtjs/tailwindcss": "^6.8.4",
    "@types/node": "^20.0.0",
    "eslint": "^8.0.0",
    "nuxt": "^3.8.0",
    "typescript": "^5.0.0"
  }
}
JSON;
            file_put_contents($packageJsonPath, $packageJson);
        }
    }

    /**
     * Create Dockerfile for Nuxt 3 projects (Development Mode for Live Previews)
     */
    public function createDockerfile(string $projectDir): void
    {
        $dockerfile = 'FROM node:18-alpine

WORKDIR /app

# Copy package files and config files first
COPY package.json ./
COPY nuxt.config.ts ./
COPY tsconfig.json ./

# Install dependencies
RUN npm install

# Copy remaining source code
COPY . .

# Expose port
EXPOSE 3000

# Start the development server for live previews
CMD ["npm", "run", "dev"]';

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Check if Nuxt 3 files already exist in the project directory
     */
    public function hasRequiredFiles(string $projectDir): bool
    {
        $requiredFiles = [
            'app.vue',
            'pages/index.vue',
            'package.json',
            'nuxt.config.ts',
        ];

        foreach ($requiredFiles as $file) {
            if (! file_exists("{$projectDir}/{$file}")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create basic Nuxt 3 fallback when no generated code is available
     */
    public function createBasicFallback(string $projectDir, Project $project): void
    {
        // Create a basic Nuxt 3 structure
        $basicNuxt = [
            'package.json' => json_encode([
                'name' => strtolower($project->slug ?? 'ai-project'),
                'private' => true,
                'scripts' => [
                    'build' => 'nuxt build',
                    'dev' => 'nuxt dev',
                    'generate' => 'nuxt generate',
                    'preview' => 'nuxt preview',
                    'postinstall' => 'nuxt prepare',
                ],
                'devDependencies' => [
                    '@nuxt/devtools' => 'latest',
                    '@nuxtjs/eslint-config-typescript' => '^12.0.0',
                    '@nuxtjs/tailwindcss' => '^6.8.4',
                    '@types/node' => '^20.0.0',
                    'eslint' => '^8.0.0',
                    'nuxt' => '^3.8.0',
                    'typescript' => '^5.0.0',
                ],
            ], JSON_PRETTY_PRINT),
            'app.vue' => '<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<style>
  @import "~/assets/css/main.css";
</style>',
            'pages/index.vue' => '<template>
  <div class="container mx-auto px-4 py-8 text-center">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">
      Welcome to Your Nuxt 3 Project
    </h1>
    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
      This project is ready for AI code generation!
    </p>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md mx-auto">
      <p class="text-gray-600 dark:text-gray-300">
        Edit <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">pages/index.vue</code> to get started.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
// Page meta
useHead({
  title: "Welcome to Nuxt 3",
  meta: [
    { name: "description", content: "AI Generated Nuxt 3 Project" }
  ]
})
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>',
            'assets/css/main.css' => '@import "tailwindcss/base";
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
  min-width: 320px;
  min-height: 100vh;
}

h1 {
  font-size: 3.2em;
  line-height: 1.1;
}

@media (prefers-color-scheme: light) {
  :root {
    color: #213547;
    background-color: #ffffff;
  }
  
  a:hover {
    color: #747bff;
  }
}',
        ];

        // Create the files
        foreach ($basicNuxt as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create additional configuration files
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Nuxt 3 project
        $this->createDockerfile($projectDir);
    }

    /**
     * Get the internal port for Nuxt 3 projects
     */
    public function getInternalPort(): string
    {
        return '3000';
    }
}
