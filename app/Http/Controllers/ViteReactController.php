<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use App\Services\FilePermissionService;
use Illuminate\Support\Facades\Log;

class ViteReactController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Generate Vite + React specific system prompt
     */
    public function getSystemPrompt(): string
    {
        return 'You are a web developer. Generate a Vite + React + TypeScript project as JSON with these exact keys where each value is a STRING (not an object): index.html, src/main.tsx, src/App.tsx, src/App.css, src/index.css. Each value must be a complete file content as a string. DO NOT include configuration files like package.json, vite.config.ts, tsconfig.json, etc. - these are handled by the system. Focus only on the application code and UI components. Return only valid JSON, no other text.';
    }

    /**
     * Generate Vite + React specific user prompt
     */
    public function getUserPrompt(string $prompt): string
    {
        return "Create a Vite + React + TypeScript website for: {$prompt}";
    }

    /**
     * Get required files for Vite + React projects
     */
    public function getRequiredFiles(): array
    {
        return [
            'package.json',
            'vite.config.ts',
            'tsconfig.json',
            'index.html',
            'src/main.tsx',
            'src/App.tsx',
            'src/App.css',
            'src/index.css',
            'Dockerfile',
            'docker-compose.yml',
            '.dockerignore',
        ];
    }

    /**
     * Generate mock Vite + React project data
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
     * Generate Vite + React portfolio project
     */
    private function generatePortfolioProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + React ecommerce project
     */
    private function generateEcommerceProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + React blog project
     */
    private function generateBlogProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + React landing page project
     */
    private function generateLandingProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate Vite + React dashboard project
     */
    private function generateDashboardProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate generic Vite + React project
     */
    private function generateGenericProject(string $prompt): array
    {
        return [
            'project_type' => 'vite',
            'name' => 'Vite + React Project',
            'description' => 'AI-generated Vite + React + TypeScript application',
            'prompt' => $prompt,
            'files' => [
                [
                    'path' => 'index.html',
                    'type' => 'file',
                    'content' => $this->getIndexHtmlContent(),
                ],
                [
                    'path' => 'src/main.tsx',
                    'type' => 'file',
                    'content' => $this->getMainTsxContent(),
                ],
                [
                    'path' => 'src/App.tsx',
                    'type' => 'file',
                    'content' => $this->getAppTsxContent($prompt),
                ],
                [
                    'path' => 'src/App.css',
                    'type' => 'file',
                    'content' => $this->getAppCssContent(),
                ],
                [
                    'path' => 'src/index.css',
                    'type' => 'file',
                    'content' => $this->getIndexCssContent(),
                ],
            ],
        ];
    }

    /**
     * Get Vite index.html content
     */
    private function getIndexHtmlContent(): string
    {
        return '<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vite + React + TS</title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.tsx"></script>
  </body>
</html>';
    }

    /**
     * Get Vite main.tsx content
     */
    private function getMainTsxContent(): string
    {
        return 'import React from \'react\'
import ReactDOM from \'react-dom/client\'
import App from \'./App.tsx\'
import \'./index.css\'

ReactDOM.createRoot(document.getElementById(\'root\')!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)';
    }

    /**
     * Get Vite App.tsx content
     */
    private function getAppTsxContent(string $prompt): string
    {
        return 'import { useState } from \'react\'
import reactLogo from \'./assets/react.svg\'
import viteLogo from \'/vite.svg\'
import \'./App.css\'

function App() {
  const [count, setCount] = useState(0)

  return (
    <>
      <div>
        <a href="https://vitejs.dev" target="_blank">
          <img src={viteLogo} className="logo" alt="Vite logo" />
        </a>
        <a href="https://react.dev" target="_blank">
          <img src={reactLogo} className="logo react" alt="React logo" />
        </a>
      </div>
      <h1>Vite + React + TS</h1>
      <div className="card">
        <button onClick={() => setCount((count) => count + 1)}>
          count is {count}
        </button>
        <p>
          Edit <code>src/App.tsx</code> and save to test HMR
        </p>
      </div>
      <p className="read-the-docs">
        Click on the Vite and React logos to learn more
      </p>
    </>
  )
}

export default App';
    }

    /**
     * Get Vite App.css content
     */
    private function getAppCssContent(): string
    {
        return '#root {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem;
  text-align: center;
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
.logo.react:hover {
  filter: drop-shadow(0 0 2em #61dafbaa);
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
  a:nth-of-type(2) .logo {
    animation: logo-spin infinite 20s linear;
  }
}

.card {
  padding: 2em;
}

.read-the-docs {
  color: #888;
}';
    }

    /**
     * Get Vite index.css content
     */
    private function getIndexCssContent(): string
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
     * Check if project is Vite + React
     */
    public function isViteProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        return str_contains($stack, 'vite');
    }

    /**
     * Get Vite + React specific Docker configuration
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
     * Create Vite + React project files
     */
    public function createProjectFiles(string $projectDir, array $projectFiles): void
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

        // Create Dockerfile for Vite project
        $this->createDockerfile($projectDir);
    }

    /**
     * Create additional Vite configuration files
     */
    public function createConfigFiles(string $projectDir): void
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
     * Create Dockerfile for Vite projects (Development Mode for Live Previews)
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
     * Check if Vite files already exist in the project directory
     */
    public function hasRequiredFiles(string $projectDir): bool
    {
        $requiredFiles = [
            'index.html',
            'src/main.tsx',
            'src/App.tsx',
            'package.json',
            'vite.config.ts',
        ];

        foreach ($requiredFiles as $file) {
            if (! file_exists("{$projectDir}/{$file}")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create basic Vite fallback when no generated code is available
     */
    public function createBasicFallback(string $projectDir, Project $project): void
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
    <title>'.($project->name ?? 'AI Project').'</title>
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
        $this->createConfigFiles($projectDir);

        // Create Dockerfile for Vite project
        $this->createDockerfile($projectDir);
    }

    /**
     * Get the internal port for Vite projects
     */
    public function getInternalPort(): string
    {
        return '5173';
    }
}
