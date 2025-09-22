<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\CollaborationService;
use App\Services\DockerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CollaborationService $collaborationService,
        private DockerService $dockerService
    ) {
        //
    }
    public function index(): Response
    {
        $projects = auth()->user()->projects()
            ->with(['containers', 'prompts'])
            ->latest()
            ->paginate(12);

        return Inertia::render('projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function show(Project $project): Response
    {
        $this->authorize('view', $project);

        $project->load(['containers', 'prompts' => function ($query) {
            $query->latest()->limit(10);
        }]);

        // Track user joining the project
        $this->collaborationService->userJoined($project, auth()->user());

        // Get collaboration data
        $activeCollaborators = $this->collaborationService->getActiveCollaborators($project);
        $collaborationHistory = $this->collaborationService->getCollaborationHistory($project);

        return Inertia::render('projects/Sandbox', [
            'project' => $project,
            'activeCollaborators' => $activeCollaborators,
            'collaborationHistory' => $collaborationHistory,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('projects/Create');
    }

    public function store(StoreProjectRequest $request)
    {
        $project = auth()->user()->projects()->create([
            'name' => $request->name,
            'description' => $request->description,
            'slug' => Str::slug($request->name),
            'settings' => $request->settings ?? [],
        ]);

        // Set up project directory and basic Docker files
        $this->setupProjectFiles($project);

        // For Inertia requests, return back with the project data in props
        if (request()->header('X-Inertia')) {
            return Inertia::render('projects/Index', [
                'projects' => auth()->user()->projects()
                    ->with(['containers', 'prompts'])
                    ->latest()
                    ->paginate(12),
                'createdProject' => $project,
            ]);
        }

        // For AJAX requests, return JSON
        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully!',
                'project' => $project,
            ]);
        }

        // Fallback redirect
        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully!');
    }

    /**
     * Set up project files and Docker configuration
     */
    private function setupProjectFiles(Project $project): void
    {
        try {
            $projectDir = storage_path("app/projects/{$project->id}");

            // Create project directory
            if (!is_dir($projectDir)) {
                mkdir($projectDir, 0755, true);
            }

            // Create project structure based on stack type
            $this->createProjectStructure($projectDir, $project);

            // Create Dockerfile
            $this->createDockerfile($projectDir);

            // Create .dockerignore
            $this->createDockerignore($projectDir);

            // Create docker-compose.yml for development
            $this->createDockerCompose($projectDir, $project);

            Log::info('Project files created successfully', [
                'project_id' => $project->id,
                'project_dir' => $projectDir,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create project files', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create project structure based on stack type
     */
    private function createProjectStructure(string $projectDir, Project $project): void
    {
        $settings = $project->settings ?? [];
        $stack = $settings['stack'] ?? '';
        
        if ($stack === 'vite' || $stack === 'Vite + React') {
            $this->createBasicViteProject($projectDir, $project);
        } else {
            // Default to Next.js for backward compatibility
            $this->createBasicNextJSProject($projectDir, $project);
        }
    }

    /**
     * Create basic Next.js project structure
     */
    private function createBasicNextJSProject(string $projectDir, Project $project): void
    {
        // Create package.json
        $packageJson = [
            'name' => strtolower($project->slug),
            'version' => '0.1.0',
            'private' => true,
            'scripts' => [
                'dev' => 'next dev',
                'build' => 'next build',
                'start' => 'next start',
                'lint' => 'next lint',
            ],
            'dependencies' => [
                'next' => '^14.0.0',
                'react' => '^18.0.0',
                'react-dom' => '^18.0.0',
            ],
            'devDependencies' => [
                '@types/node' => '^20.0.0',
                '@types/react' => '^18.0.0',
                '@types/react-dom' => '^18.0.0',
                'eslint' => '^8.0.0',
                'eslint-config-next' => '^14.0.0',
                'typescript' => '^5.0.0',
            ],
        ];

        file_put_contents("{$projectDir}/package.json", json_encode($packageJson, JSON_PRETTY_PRINT));

        // Create next.config.js
        $nextConfig = <<<'EOT'
/** @type {import('next').NextConfig} */
const nextConfig = {
  experimental: {
    appDir: true,
  },
}

module.exports = nextConfig
EOT;
        file_put_contents("{$projectDir}/next.config.js", $nextConfig);

        // Create tsconfig.json
        $tsConfig = [
            'compilerOptions' => [
                'target' => 'es5',
                'lib' => ['dom', 'dom.iterable', 'es6'],
                'allowJs' => true,
                'skipLibCheck' => true,
                'strict' => true,
                'noEmit' => true,
                'esModuleInterop' => true,
                'module' => 'esnext',
                'moduleResolution' => 'bundler',
                'resolveJsonModule' => true,
                'isolatedModules' => true,
                'jsx' => 'preserve',
                'incremental' => true,
                'plugins' => [
                    [
                        'next/babel',
                        [
                            'preset-env' => [],
                            'preset-react' => [],
                        ],
                    ],
                ],
            ],
            'include' => ['next-env.d.ts', '**/*.ts', '**/*.tsx', '.next/types/**/*.ts'],
            'exclude' => ['node_modules'],
        ];
        file_put_contents("{$projectDir}/tsconfig.json", json_encode($tsConfig, JSON_PRETTY_PRINT));

        // Create app directory structure
        $appDir = "{$projectDir}/app";
        if (!is_dir($appDir)) {
            mkdir($appDir, 0755, true);
        }

        // Create layout.tsx
        $layout = <<<'EOT'
import './globals.css'

export const metadata = {
  title: 'Generated Project',
  description: 'AI Generated Project',
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
}
EOT;
        file_put_contents("{$appDir}/layout.tsx", $layout);

        // Create page.tsx
        $page = <<<'EOT'
export default function Home() {
  return (
    <main className="flex min-h-screen flex-col items-center justify-between p-24">
      <div className="z-10 max-w-5xl w-full items-center justify-between font-mono text-sm lg:flex">
        <h1 className="text-4xl font-bold">Welcome to Your Generated Project</h1>
      </div>
      <div className="relative flex place-items-center">
        <p className="text-lg">This project is ready for AI code generation!</p>
      </div>
    </main>
  )
}
EOT;
        file_put_contents("{$appDir}/page.tsx", $page);

        // Create globals.css
        $globalsCss = <<<'EOT'
@tailwind base;
@tailwind components;
@tailwind utilities;

:root {
  --foreground-rgb: 0, 0, 0;
  --background-start-rgb: 214, 219, 220;
  --background-end-rgb: 255, 255, 255;
}

@media (prefers-color-scheme: dark) {
  :root {
    --foreground-rgb: 255, 255, 255;
    --background-start-rgb: 0, 0, 0;
    --background-end-rgb: 0, 0, 0;
  }
}

body {
  color: rgb(var(--foreground-rgb));
  background: linear-gradient(
      to bottom,
      transparent,
      rgb(var(--background-end-rgb))
    )
    rgb(var(--background-start-rgb));
}
EOT;
        file_put_contents("{$appDir}/globals.css", $globalsCss);
    }

    /**
     * Create basic Vite + React + TypeScript project structure
     */
    private function createBasicViteProject(string $projectDir, Project $project): void
    {
        // Create package.json
        $packageJson = [
            'name' => strtolower($project->slug),
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
        ];

        file_put_contents("{$projectDir}/package.json", json_encode($packageJson, JSON_PRETTY_PRINT));

        // Create index.html
        $indexHtml = <<<'EOT'
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Generated Project</title>
  </head>
  <body>
    <div id="root"></div>
    <script type="module" src="/src/main.tsx"></script>
  </body>
</html>
EOT;
        file_put_contents("{$projectDir}/index.html", $indexHtml);

        // Create src directory
        $srcDir = "{$projectDir}/src";
        if (!is_dir($srcDir)) {
            mkdir($srcDir, 0755, true);
        }

        // Create main.tsx
        $mainTsx = <<<'EOT'
import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.tsx'
import './index.css'

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
)
EOT;
        file_put_contents("{$srcDir}/main.tsx", $mainTsx);

        // Create App.tsx
        $appTsx = <<<'EOT'
import { useState } from 'react'
import './App.css'

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

export default App
EOT;
        file_put_contents("{$srcDir}/App.tsx", $appTsx);

        // Create App.css
        $appCss = <<<'EOT'
.App {
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
}
EOT;
        file_put_contents("{$srcDir}/App.css", $appCss);

        // Create index.css
        $indexCss = <<<'EOT'
@import "tailwindcss/base";
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
}
EOT;
        file_put_contents("{$srcDir}/index.css", $indexCss);
    }

    /**
     * Create Dockerfile based on project type
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
     * Create Dockerfile for Vite project
     */
    private function createViteDockerfile(string $projectDir): void
    {
        $dockerfile = <<<'EOT'
FROM node:18-alpine

WORKDIR /app

# Copy package files
COPY package.json ./

# Install dependencies
RUN npm install

# Copy source code
COPY . .

# Expose port
EXPOSE 5173

# Start the development server for live previews
CMD ["npm", "run", "dev"]
EOT;
        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Create Dockerfile for HTML projects
     */
    private function createHTMLDockerfile(string $projectDir): void
    {
        $dockerfile = <<<'EOT'
FROM nginx:alpine

# Copy project files
COPY . /usr/share/nginx/html/

# Copy nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Expose port 80
EXPOSE 80

# Start nginx
CMD ["nginx", "-g", "daemon off;"]
EOT;
        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Create Dockerfile for Next.js project
     */
    private function createNextJSDockerfile(string $projectDir): void
    {
        $dockerfile = <<<'EOT'
FROM node:18-alpine AS base

# Install dependencies only when needed
FROM base AS deps
RUN apk add --no-cache libc6-compat
WORKDIR /app

# Install dependencies based on the preferred package manager
COPY package.json package-lock.json* ./
RUN npm ci

# Rebuild the source code only when needed
FROM base AS builder
WORKDIR /app
COPY --from=deps /app/node_modules ./node_modules
COPY . .

# Next.js collects completely anonymous telemetry data about general usage.
# Learn more here: https://nextjs.org/telemetry
# Uncomment the following line in case you want to disable telemetry during the build.
ENV NEXT_TELEMETRY_DISABLED 1

RUN npm run build

# Production image, copy all the files and run next
FROM base AS runner
WORKDIR /app

ENV NODE_ENV production
ENV NEXT_TELEMETRY_DISABLED 1

RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

COPY --from=builder /app/public ./public

# Set the correct permission for prerender cache
RUN mkdir .next
RUN chown nextjs:nodejs .next

# Automatically leverage output traces to reduce image size
COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

USER nextjs

EXPOSE 3000

ENV PORT 3000
ENV HOSTNAME "0.0.0.0"

CMD ["node", "server.js"]
EOT;
        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Create .dockerignore file
     */
    private function createDockerignore(string $projectDir): void
    {
        $dockerignore = <<<'EOT'
Dockerfile
.dockerignore
node_modules
npm-debug.log
README.md
.env
.env.local
.env.production.local
.env.local
.next
.git
.gitignore
EOT;
        file_put_contents("{$projectDir}/.dockerignore", $dockerignore);
    }

    /**
     * Create docker-compose.yml for development
     */
    private function createDockerCompose(string $projectDir, Project $project): void
    {
        $dockerCompose = <<<EOT
version: '3.8'

services:
  app:
    build: .
    ports:
      - "3000:3000"
    environment:
      - NODE_ENV=production
    volumes:
      - .:/app
      - /app/node_modules
    restart: unless-stopped
    container_name: "lovable-container-{$project->id}"
EOT;
        file_put_contents("{$projectDir}/docker-compose.yml", $dockerCompose);
    }

    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        return Inertia::render('projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $oldData = $project->toArray();
        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'settings' => $request->settings ?? $project->settings,
        ]);
        
        // Track project changes
        $newData = $project->fresh()->toArray();
        $changes = [];
        foreach ($newData as $key => $value) {
            if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                $changes[$key] = $value;
            }
        }
        $this->collaborationService->projectUpdated($project, auth()->user(), $changes);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully!');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        // Clean up Docker resources before deleting the project
        $this->dockerService->cleanupProject($project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }

    public function duplicate(Project $project): RedirectResponse
    {
        $this->authorize('view', $project);

        $newProject = $project->replicate();
        $newProject->name = $project->name . ' (Copy)';
        $newProject->slug = Str::slug($newProject->name);
        $newProject->status = 'draft';
        $newProject->preview_url = null;
        $newProject->last_built_at = null;
        $newProject->subdomain = null; // Clear subdomain to generate a new one
        $newProject->custom_domain = null; // Clear custom domain
        $newProject->dns_configured = false; // Reset DNS status
        $newProject->save();

        return redirect()->route('projects.show', $newProject)
            ->with('success', 'Project duplicated successfully!');
    }

    public function checkName(Request $request)
    {
        $name = $request->query('name');
        
        if (!$name) {
            return response()->json(['exists' => false]);
        }

        $exists = auth()->user()->projects()
            ->where('name', $name)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function showApi(Project $project)
    {
        $this->authorize('view', $project);

        return response()->json([
            'success' => true,
            'project' => $project->load(['containers', 'prompts' => function ($query) {
                $query->latest()->limit(5);
            }])
        ]);
    }

    public function verifySetup(Project $project)
    {
        // Check if user owns the project
        if ($project->user_id !== auth()->id()) {
            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Unauthorized access to project']);
            }
            return response()->json(['success' => false, 'message' => 'Unauthorized access to project'], 403);
        }

        $verification = [
            'database_exists' => false,
            'folder_exists' => false,
            'required_files' => [],
            'all_files_present' => false,
            'overall_status' => 'failed'
        ];

        try {
            // 1. Check if project exists in database
            $verification['database_exists'] = $project->exists;

            // 2. Check if project folder exists
            $projectPath = storage_path("app/projects/{$project->id}");
            $verification['folder_exists'] = is_dir($projectPath);

            // 3. Check for required files
            $requiredFiles = [
                'package.json',
                'next.config.js',
                'tsconfig.json',
                'Dockerfile',
                'docker-compose.yml',
                '.dockerignore'
            ];

            $filesPresent = [];
            foreach ($requiredFiles as $file) {
                $filePath = $projectPath . '/' . $file;
                $filesPresent[$file] = file_exists($filePath);
            }

            $verification['required_files'] = $filesPresent;
            $verification['all_files_present'] = !in_array(false, $filesPresent);

            // 4. Overall status
            if ($verification['database_exists'] && $verification['folder_exists'] && $verification['all_files_present']) {
                $verification['overall_status'] = 'success';
            } elseif ($verification['database_exists'] && $verification['folder_exists']) {
                $verification['overall_status'] = 'partial';
            } else {
                $verification['overall_status'] = 'failed';
            }

            // For Inertia requests, return back with verification data
            if (request()->header('X-Inertia')) {
                return redirect()->back()->with(['verification' => $verification]);
            }

            // For AJAX requests, return JSON
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'verification' => $verification
                ]);
            }

            return response()->json(['verification' => $verification]);

        } catch (\Exception $e) {
            Log::error('Failed to verify project setup', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            $verification['error'] = $e->getMessage();

            // For Inertia requests, return back with error
            if (request()->header('X-Inertia')) {
                return redirect()->back()->withErrors(['error' => 'Verification failed: ' . $e->getMessage()]);
            }

            // For AJAX requests, return JSON
            if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Verification failed: ' . $e->getMessage(),
                    'verification' => $verification
                ], 500);
            }

            return response()->json(['verification' => $verification], 500);
        }
    }
}
