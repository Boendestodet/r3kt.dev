<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use App\Services\FilePermissionService;
use Illuminate\Support\Facades\Log;

class AstroController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Generate Astro specific system prompt
     */
    public function getSystemPrompt(): string
    {
        return 'You are a web developer. Generate an Astro project as JSON with these exact keys where each value is a STRING (not an object): src/pages/index.astro, src/layouts/Layout.astro, src/components/Header.astro, src/components/Footer.astro, src/styles/global.css. Each value must be a complete file content as a string. DO NOT include configuration files like package.json, astro.config.mjs, tsconfig.json, etc. - these are handled by the system. Focus only on the application code and UI components. Return only valid JSON, no other text.';
    }

    /**
     * Generate Astro specific user prompt
     */
    public function getUserPrompt(string $prompt): string
    {
        return "Create an Astro website for: {$prompt}";
    }

    /**
     * Get required files for Astro projects
     */
    public function getRequiredFiles(): array
    {
        return [
            'package.json',
            'astro.config.mjs',
            'tsconfig.json',
            'src/pages/index.astro',
            'src/layouts/Layout.astro',
            'src/components/Header.astro',
            'src/components/Footer.astro',
            'src/styles/global.css',
            'Dockerfile',
            'docker-compose.yml',
            '.dockerignore',
        ];
    }

    /**
     * Generate mock Astro project data
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
     * Generate Astro portfolio project
     */
    private function generatePortfolioProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Astro ecommerce project
     */
    private function generateEcommerceProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Astro blog project
     */
    private function generateBlogProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Astro landing page project
     */
    private function generateLandingProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Astro dashboard project
     */
    private function generateDashboardProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate generic Astro project
     */
    private function generateGenericProject(string $prompt): array
    {
        return [
            'project_type' => 'astro',
            'name' => 'Astro Project',
            'description' => 'AI-generated Astro application',
            'prompt' => $prompt,
            'files' => [
                [
                    'path' => 'src/pages/index.astro',
                    'type' => 'file',
                    'content' => $this->getIndexAstroContent($prompt),
                ],
                [
                    'path' => 'src/layouts/Layout.astro',
                    'type' => 'file',
                    'content' => $this->getLayoutAstroContent(),
                ],
                [
                    'path' => 'src/components/Header.astro',
                    'type' => 'file',
                    'content' => $this->getHeaderAstroContent(),
                ],
                [
                    'path' => 'src/components/Footer.astro',
                    'type' => 'file',
                    'content' => $this->getFooterAstroContent(),
                ],
                [
                    'path' => 'src/styles/global.css',
                    'type' => 'file',
                    'content' => $this->getGlobalCssContent(),
                ],
            ],
        ];
    }

    /**
     * Get Astro index.astro content
     */
    private function getIndexAstroContent(string $prompt): string
    {
        return '---
import Layout from "../layouts/Layout.astro";
import Header from "../components/Header.astro";
import Footer from "../components/Footer.astro";
---

<Layout title="Welcome to Astro">
  <Header />
  
  <main class="container mx-auto px-4 py-8">
    <div class="text-center">
      <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">
        Welcome to Your Astro Project
      </h1>
      <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
        This is an AI-generated Astro application ready for development.
      </p>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
          <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Fast</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Built with Astro for optimal performance and minimal JavaScript.
          </p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
          <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">Modern</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Uses the latest web technologies and best practices.
          </p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
          <h2 class="text-2xl font-semibold mb-4 text-gray-900 dark:text-white">TypeScript</h2>
          <p class="text-gray-600 dark:text-gray-300">
            Full TypeScript support for enhanced development experience.
          </p>
        </div>
      </div>
    </div>
  </main>
  
  <Footer />
</Layout>

<style>
  .container {
    max-width: 1200px;
  }
</style>';
    }

    /**
     * Get Astro Layout.astro content
     */
    private function getLayoutAstroContent(): string
    {
        return '---
export interface Props {
  title: string;
}

const { title } = Astro.props;
---

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="description" content="AI Generated Astro Project" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <meta name="generator" content={Astro.generator} />
    <title>{title}</title>
  </head>
  <body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    <slot />
  </body>
</html>

<style is:global>
  @import "../styles/global.css";
</style>';
    }

    /**
     * Get Astro Header.astro content
     */
    private function getHeaderAstroContent(): string
    {
        return '---
// Header component
---

<header class="bg-white dark:bg-gray-800 shadow-sm">
  <nav class="container mx-auto px-4 py-4">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-4">
        <a href="/" class="text-2xl font-bold text-gray-900 dark:text-white">
          Astro App
        </a>
      </div>
      
      <div class="hidden md:flex items-center space-x-6">
        <a href="/" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
          Home
        </a>
        <a href="/about" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
          About
        </a>
        <a href="/contact" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
          Contact
        </a>
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

<style>
  .container {
    max-width: 1200px;
  }
</style>';
    }

    /**
     * Get Astro Footer.astro content
     */
    private function getFooterAstroContent(): string
    {
        return '---
// Footer component
---

<footer class="bg-gray-800 text-white py-8 mt-16">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div>
        <h3 class="text-lg font-semibold mb-4">About</h3>
        <p class="text-gray-300">
          This is an AI-generated Astro project built with modern web technologies.
        </p>
      </div>
      
      <div>
        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="/" class="text-gray-300 hover:text-white">Home</a></li>
          <li><a href="/about" class="text-gray-300 hover:text-white">About</a></li>
          <li><a href="/contact" class="text-gray-300 hover:text-white">Contact</a></li>
        </ul>
      </div>
      
      <div>
        <h3 class="text-lg font-semibold mb-4">Tech Stack</h3>
        <ul class="space-y-2 text-gray-300">
          <li>Astro</li>
          <li>TypeScript</li>
          <li>Tailwind CSS</li>
        </ul>
      </div>
    </div>
    
    <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
      <p>&copy; 2024 AI Generated Astro Project. All rights reserved.</p>
    </div>
  </div>
</footer>

<style>
  .container {
    max-width: 1200px;
  }
</style>';
    }

    /**
     * Get Astro global CSS content
     */
    private function getGlobalCssContent(): string
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
     * Check if project is Astro
     */
    public function isAstroProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        return str_contains($stack, 'astro');
    }

    /**
     * Get Astro specific Docker configuration
     */
    public function getDockerConfig(): array
    {
        return [
            'port' => 4321,
            'build_command' => 'npm run build',
            'start_command' => 'npm run preview',
            'dev_command' => 'npm run dev',
        ];
    }

    /**
     * Create Astro project files
     */
    public function createProjectFiles(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = [
            'astro.config.mjs',
            'package.json',
            'tsconfig.json',
            'tailwind.config.mjs',
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

        // Create Dockerfile for Astro project
        $this->createDockerfile($projectDir);
    }

    /**
     * Create additional Astro configuration files
     */
    public function createConfigFiles(string $projectDir): void
    {
        // Create astro.config.mjs
        $astroConfigPath = "{$projectDir}/astro.config.mjs";
        $astroConfig = <<<'JS'
import { defineConfig } from 'astro/config';
import tailwind from '@astrojs/tailwind';

// https://astro.build/config
export default defineConfig({
  integrations: [tailwind()],
  server: {
    host: '0.0.0.0',
    port: 4321,
  },
});
JS;

        // Ensure we can write the file by fixing permissions if needed
        if (file_exists($astroConfigPath)) {
            chmod($astroConfigPath, 0644);
        }

        $result = file_put_contents($astroConfigPath, $astroConfig);

        if ($result === false) {
            Log::error('Failed to create astro.config.mjs', [
                'project_dir' => $projectDir,
                'file' => $astroConfigPath,
            ]);
        }

        // Create tsconfig.json
        $tsconfigPath = "{$projectDir}/tsconfig.json";
        if (! file_exists($tsconfigPath)) {
            $tsconfig = <<<'JSON'
{
  "extends": "astro/tsconfigs/strict",
  "compilerOptions": {
    "baseUrl": ".",
    "paths": {
      "@/*": ["./src/*"]
    }
  }
}
JSON;
            file_put_contents($tsconfigPath, $tsconfig);
        }

        // Create tailwind.config.mjs
        $tailwindConfigPath = "{$projectDir}/tailwind.config.mjs";
        if (! file_exists($tailwindConfigPath)) {
            $tailwindConfig = <<<'JS'
/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}'],
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
  extends: ['@astrojs/eslint-config'],
  rules: {
    // Add any custom rules here
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
  "name": "astro-project",
  "type": "module",
  "version": "0.0.1",
  "scripts": {
    "dev": "astro dev",
    "start": "astro dev",
    "build": "astro check && astro build",
    "preview": "astro preview",
    "astro": "astro"
  },
  "dependencies": {
    "astro": "^4.0.0",
    "@astrojs/tailwind": "^5.0.0"
  },
  "devDependencies": {
    "@astrojs/eslint-config": "^0.4.0",
    "@types/node": "^20.0.0",
    "eslint": "^8.0.0",
    "tailwindcss": "^3.0.0",
    "typescript": "^5.0.0"
  }
}
JSON;
            file_put_contents($packageJsonPath, $packageJson);
        }
    }

    /**
     * Create Dockerfile for Astro projects (Development Mode for Live Previews)
     */
    public function createDockerfile(string $projectDir): void
    {
        $dockerfile = 'FROM node:18-alpine

WORKDIR /app

# Copy package files and config files first
COPY package.json ./
COPY astro.config.mjs ./
COPY tsconfig.json ./

# Install dependencies
RUN npm install

# Copy remaining source code
COPY . .

# Expose port
EXPOSE 4321

# Start the development server for live previews
CMD ["npm", "run", "dev"]';

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Check if Astro files already exist in the project directory
     */
    public function hasRequiredFiles(string $projectDir): bool
    {
        $requiredFiles = [
            'src/pages/index.astro',
            'src/layouts/Layout.astro',
            'package.json',
            'astro.config.mjs',
        ];

        foreach ($requiredFiles as $file) {
            if (! file_exists("{$projectDir}/{$file}")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create basic Astro fallback when no generated code is available
     */
    public function createBasicFallback(string $projectDir, Project $project): void
    {
        // Create a basic Astro structure
        $basicAstro = [
            'package.json' => json_encode([
                'name' => strtolower($project->slug ?? 'ai-project'),
                'type' => 'module',
                'version' => '0.0.1',
                'scripts' => [
                    'dev' => 'astro dev',
                    'start' => 'astro dev',
                    'build' => 'astro check && astro build',
                    'preview' => 'astro preview',
                    'astro' => 'astro',
                ],
                'dependencies' => [
                    'astro' => '^4.0.0',
                    '@astrojs/tailwind' => '^5.0.0',
                ],
                'devDependencies' => [
                    '@astrojs/eslint-config' => '^0.4.0',
                    '@types/node' => '^20.0.0',
                    'eslint' => '^8.0.0',
                    'tailwindcss' => '^3.0.0',
                    'typescript' => '^5.0.0',
                ],
            ], JSON_PRETTY_PRINT),
            'src/pages/index.astro' => '---
import Layout from "../layouts/Layout.astro";
---

<Layout title="Welcome to Astro">
  <main class="container mx-auto px-4 py-8 text-center">
    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">
      Welcome to Your Astro Project
    </h1>
    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
      This project is ready for AI code generation!
    </p>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-md mx-auto">
      <p class="text-gray-600 dark:text-gray-300">
        Edit <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">src/pages/index.astro</code> to get started.
      </p>
    </div>
  </main>
</Layout>

<style>
  .container {
    max-width: 1200px;
  }
</style>',
            'src/layouts/Layout.astro' => '---
export interface Props {
  title: string;
}

const { title } = Astro.props;
---

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="description" content="AI Generated Astro Project" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <meta name="generator" content={Astro.generator} />
    <title>{title}</title>
  </head>
  <body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white">
    <slot />
  </body>
</html>

<style is:global>
  @import "../styles/global.css";
</style>',
            'src/styles/global.css' => '@import "tailwindcss/base";
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
        foreach ($basicAstro as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create additional configuration files
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Astro project
        $this->createDockerfile($projectDir);
    }

    /**
     * Get the internal port for Astro projects
     */
    public function getInternalPort(): string
    {
        return '4321';
    }
}
