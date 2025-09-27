<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DockerService;
use App\Services\FilePermissionService;
use Illuminate\Support\Facades\Log;

class SvelteKitController extends Controller
{
    public function __construct(
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Generate SvelteKit specific system prompt
     */
    public function getSystemPrompt(): string
    {
        return 'You are an expert SvelteKit developer. Generate ONLY SvelteKit files - NO Next.js, React, or other framework files.

CRITICAL REQUIREMENTS:
• Generate ONLY these 4 files: "src/app.html", "src/app.css", "src/routes/+layout.svelte", "src/routes/+page.svelte"
• DO NOT generate app/layout.tsx, app/page.tsx, or any Next.js files
• DO NOT generate src/App.tsx, src/main.tsx, or any React files
• Use .svelte file extensions, NOT .tsx or .jsx
• Use SvelteKit routing structure (src/routes/), NOT Next.js app/ directory

Technical preferences:
• Always use kebab-case for component names (e.g. my-component.svelte)
• Favor using SvelteKit SSR features where possible
• Use semantic HTML elements where possible
• Utilize Svelte stores for global state management
• Use TypeScript for enhanced type safety

Return ONLY valid JSON with these exact 4 keys. No other text or files.';
    }

    /**
     * Generate SvelteKit specific user prompt
     */
    public function getUserPrompt(string $prompt): string
    {
        return "Create a SvelteKit website for: {$prompt}";
    }

    /**
     * Get required files for SvelteKit projects
     */
    public function getRequiredFiles(): array
    {
        return [
            'package.json',
            'svelte.config.js',
            'tsconfig.json',
            'src/app.html',
            'src/app.css',
            'src/routes/+layout.svelte',
            'src/routes/+page.svelte',
            'Dockerfile',
            'docker-compose.yml',
            '.dockerignore',
        ];
    }

    /**
     * Generate mock SvelteKit project data
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
     * Generate SvelteKit portfolio project
     */
    private function generatePortfolioProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate SvelteKit ecommerce project
     */
    private function generateEcommerceProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate SvelteKit blog project
     */
    private function generateBlogProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate SvelteKit landing page project
     */
    private function generateLandingProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate SvelteKit dashboard project
     */
    private function generateDashboardProject(string $prompt): array
    {
        return $this->generateGenericProject($prompt);
    }

    /**
     * Generate generic SvelteKit project
     */
    private function generateGenericProject(string $prompt): array
    {
        return [
            'project_type' => 'sveltekit',
            'name' => 'SvelteKit Project',
            'description' => 'AI-generated SvelteKit application',
            'prompt' => $prompt,
            'files' => [
                [
                    'path' => 'src/app.html',
                    'type' => 'file',
                    'content' => $this->getAppHtmlContent(),
                ],
                [
                    'path' => 'src/app.css',
                    'type' => 'file',
                    'content' => $this->getAppCssContent(),
                ],
                [
                    'path' => 'src/routes/+layout.svelte',
                    'type' => 'file',
                    'content' => $this->getLayoutSvelteContent(),
                ],
                [
                    'path' => 'src/routes/+page.svelte',
                    'type' => 'file',
                    'content' => $this->getPageSvelteContent($prompt),
                ],
            ],
        ];
    }

    /**
     * Get SvelteKit app.html content
     */
    private function getAppHtmlContent(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<link rel="icon" href="%sveltekit.assets%/favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		%sveltekit.head%
	</head>
	<body data-sveltekit-preload-data="hover">
		<div style="display: contents">%sveltekit.body%</div>
	</body>
</html>';
    }

    /**
     * Get SvelteKit app.css content
     */
    private function getAppCssContent(): string
    {
        return '/* app.css */
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
     * Get SvelteKit layout.svelte content
     */
    private function getLayoutSvelteContent(): string
    {
        return '<script>
	import \'../app.css\';
</script>

<main>
	<slot />
</main>

<style>
	main {
		padding: 1rem;
		max-width: 1200px;
		margin: 0 auto;
	}
</style>';
    }

    /**
     * Get SvelteKit page.svelte content
     */
    private function getPageSvelteContent(string $prompt): string
    {
        return '<script>
	let count = 0;
</script>

<svelte:head>
	<title>AI Generated SvelteKit Project</title>
</svelte:head>

<div class="container">
	<h1 class="text-4xl font-bold mb-4">Welcome to Your SvelteKit Project</h1>
	<p class="text-lg mb-8">This is a SvelteKit application generated by AI.</p>
	
	<div class="card">
		<button on:click={() => count++}>
			Count is {count}
		</button>
		<p>
			Edit <code>src/routes/+page.svelte</code> to get started.
		</p>
	</div>
	
	<div class="features">
		<div class="feature">
			<h2>Fast</h2>
			<p>Built with SvelteKit for optimal performance</p>
		</div>
		<div class="feature">
			<h2>Modern</h2>
			<p>Uses the latest web technologies</p>
		</div>
		<div class="feature">
			<h2>TypeScript</h2>
			<p>Full TypeScript support out of the box</p>
		</div>
	</div>
</div>

<style>
	.container {
		text-align: center;
		padding: 2rem;
	}
	
	.card {
		padding: 2rem;
		margin: 2rem 0;
		border: 1px solid #e0e0e0;
		border-radius: 8px;
		background: #f9f9f9;
	}
	
	.features {
		display: grid;
		grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
		gap: 1rem;
		margin-top: 2rem;
	}
	
	.feature {
		padding: 1rem;
		border: 1px solid #e0e0e0;
		border-radius: 8px;
		background: white;
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
     * Check if project is SvelteKit
     */
    public function isSvelteKitProject(Project $project): bool
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        return str_contains($stack, 'svelte');
    }

    /**
     * Get SvelteKit specific Docker configuration
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
     * Create SvelteKit project files
     */
    public function createProjectFiles(string $projectDir, array $projectFiles): void
    {
        // Define protected files that should not be overwritten by AI
        $protectedFiles = [
            'svelte.config.js',
            'vite.config.js',
            'package.json',
            'tsconfig.json',
            'tailwind.config.js',
            'postcss.config.js',
            '.eslintrc.cjs',
            '.prettierrc',
            'Dockerfile',
        ];

        // Define allowed SvelteKit files only
        $allowedFiles = [
            'src/app.html',
            'src/app.css',
            'src/routes/+layout.svelte',
            'src/routes/+page.svelte',
        ];

        foreach ($projectFiles as $filePath => $content) {
            // Skip protected files
            if (in_array(basename($filePath), $protectedFiles)) {
                continue;
            }

            // Only create allowed SvelteKit files - reject Next.js and other framework files
            if (! in_array($filePath, $allowedFiles)) {
                Log::warning("Skipping non-SvelteKit file: {$filePath}", [
                    'project_dir' => $projectDir,
                    'file_path' => $filePath,
                    'allowed_files' => $allowedFiles,
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

        // Create Dockerfile for SvelteKit
        $this->createDockerfile($projectDir);
    }

    /**
     * Create Dockerfile for SvelteKit project
     */
    public function createDockerfile(string $projectDir): void
    {
        $dockerfile = <<<'EOT'
FROM node:18-alpine

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy source code
COPY . .

# Expose port
EXPOSE 5173

# Start development server
CMD ["npm", "run", "dev"]
EOT;

        file_put_contents("{$projectDir}/Dockerfile", $dockerfile);
    }

    /**
     * Check if SvelteKit files already exist in the project directory
     */
    public function hasRequiredFiles(string $projectDir): bool
    {
        $requiredFiles = [
            'src/app.html',
            'src/app.css',
            'src/routes/+layout.svelte',
            'src/routes/+page.svelte',
            'svelte.config.js',
            'package.json',
        ];

        foreach ($requiredFiles as $file) {
            if (! file_exists("{$projectDir}/{$file}")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Create basic SvelteKit fallback when no generated code is available
     */
    public function createBasicFallback(string $projectDir, Project $project): void
    {
        // Create a basic SvelteKit structure
        $basicSvelteKit = [
            'package.json' => json_encode([
                'name' => strtolower($project->slug ?? 'ai-project'),
                'version' => '0.0.1',
                'private' => true,
                'type' => 'module',
                'scripts' => [
                    'build' => 'vite build',
                    'dev' => 'vite dev --host 0.0.0.0',
                    'preview' => 'vite preview',
                    'check' => 'svelte-kit sync && svelte-check --tsconfig ./tsconfig.json',
                    'check:watch' => 'svelte-kit sync && svelte-check --tsconfig ./tsconfig.json --watch',
                    'lint' => 'prettier --check . && eslint .',
                    'format' => 'prettier --write .',
                ],
                'devDependencies' => [
                    '@sveltejs/adapter-auto' => '^2.0.0',
                    '@sveltejs/kit' => '^1.20.4',
                    '@sveltejs/vite-plugin-svelte' => '^2.4.2',
                    '@typescript-eslint/eslint-plugin' => '^6.0.0',
                    '@typescript-eslint/parser' => '^6.0.0',
                    'eslint' => '^8.28.0',
                    'eslint-config-prettier' => '^8.5.0',
                    'eslint-plugin-svelte' => '^2.30.0',
                    'prettier' => '^2.8.0',
                    'prettier-plugin-svelte' => '^2.10.1',
                    'svelte' => '^4.0.5',
                    'svelte-check' => '^3.4.3',
                    'tslib' => '^2.4.1',
                    'typescript' => '^5.0.0',
                    'vite' => '^4.4.2',
                    'tailwindcss' => '^3.4.0',
                    'autoprefixer' => '^10.4.17',
                    'postcss' => '^8.4.35',
                ],
            ], JSON_PRETTY_PRINT),
            'svelte.config.js' => <<<'EOT'
import adapter from '@sveltejs/adapter-auto';
import { vitePreprocess } from '@sveltejs/vite-plugin-svelte';

/** @type {import('@sveltejs/kit').Config} */
const config = {
	preprocess: vitePreprocess(),
	kit: {
		adapter: adapter()
	}
};

export default config;
EOT,
            'vite.config.js' => <<<'EOT'
import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [sveltekit()],
	server: {
		host: '0.0.0.0',
		port: 5173
	}
});
EOT,
            'tsconfig.json' => json_encode([
                'extends' => './.svelte-kit/tsconfig.json',
                'compilerOptions' => [
                    'allowJs' => true,
                    'checkJs' => true,
                    'esModuleInterop' => true,
                    'forceConsistentCasingInFileNames' => true,
                    'resolveJsonModule' => true,
                    'skipLibCheck' => true,
                    'sourceMap' => true,
                    'strict' => true,
                ],
            ], JSON_PRETTY_PRINT),
            'src/app.html' => <<<'EOT'
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<link rel="icon" href="%sveltekit.assets%/favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		%sveltekit.head%
	</head>
	<body data-sveltekit-preload-data="hover">
		<div style="display: contents">%sveltekit.body%</div>
	</body>
</html>
EOT,
            'src/app.css' => <<<'EOT'
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
EOT,
            'src/routes/+layout.svelte' => <<<'EOT'
<script>
	import '../app.css';
</script>

<slot />
EOT,
            'src/routes/+page.svelte' => <<<'EOT'
<script lang="ts">
	let count = 0;
</script>

<svelte:head>
	<title>AI Generated SvelteKit Project</title>
</svelte:head>

<main class="max-w-4xl mx-auto p-8 text-center">
	<div class="card p-8">
		<h1 class="text-4xl font-bold mb-4">Welcome to Your SvelteKit Project</h1>
		<p class="mb-6">This project is ready for AI code generation!</p>
		<button 
			class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors"
			on:click={() => count++}
		>
			count is {count}
		</button>
		<p class="mt-4">
			Edit <code class="bg-gray-800 px-2 py-1 rounded">src/routes/+page.svelte</code> and save to test HMR
		</p>
	</div>
</main>

<style>
	.card {
		padding: 2em;
	}
</style>
EOT,
            'static/favicon.png' => '',
            '.gitignore' => <<<'EOT'
.DS_Store
node_modules
/build
/.svelte-kit
/package
.env
.env.*
!.env.example
vite.config.js.timestamp-*
vite.config.ts.timestamp-*
EOT,
            '.eslintrc.cjs' => <<<'EOT'
module.exports = {
	root: true,
	extends: [
		'eslint:recommended',
		'@typescript-eslint/recommended',
		'prettier'
	],
	parser: '@typescript-eslint/parser',
	plugins: ['svelte3', '@typescript-eslint'],
	overrides: [
		{
			files: ['*.svelte'],
			processor: 'svelte3/svelte3'
		}
	],
	settings: {
		'svelte3/typescript': () => require('typescript')
	},
	parserOptions: {
		sourceType: 'module',
		ecmaVersion: 2020
	},
	env: {
		browser: true,
		es2017: true,
		node: true
	}
};
EOT,
            '.prettierrc' => json_encode([
                'useTabs' => true,
                'singleQuote' => true,
                'trailingComma' => 'none',
                'printWidth' => 100,
                'plugins' => ['prettier-plugin-svelte'],
                'overrides' => [['files' => '*.svelte', 'options' => ['parser' => 'svelte']]],
            ], JSON_PRETTY_PRINT),
            'postcss.config.js' => <<<'EOT'
export default {
	plugins: {
		tailwindcss: {},
		autoprefixer: {},
	},
};
EOT,
            'tailwind.config.js' => <<<'EOT'
/** @type {import('tailwindcss').Config} */
export default {
	content: ['./src/**/*.{html,js,svelte,ts}'],
	theme: {
		extend: {},
	},
	plugins: [],
};
EOT,
        ];

        foreach ($basicSvelteKit as $filePath => $content) {
            $fullPath = "{$projectDir}/{$filePath}";
            $dir = dirname($fullPath);

            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            file_put_contents($fullPath, $content);
        }

        // Create Dockerfile for SvelteKit
        $this->createDockerfile($projectDir);
    }

    /**
     * Get the internal port for SvelteKit projects
     */
    public function getInternalPort(): string
    {
        return '5173';
    }
}
