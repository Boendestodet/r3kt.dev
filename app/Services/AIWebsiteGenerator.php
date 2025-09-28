<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Prompt;
use Illuminate\Support\Facades\Log;

class AIWebsiteGenerator
{
    public function __construct(
        private CollaborationService $collaborationService,
        private OpenAIService $openAIService,
        private ClaudeAIService $claudeAIService,
        private GeminiAIService $geminiAIService,
        private CursorAIService $cursorAIService,
        private DockerService $dockerService
    ) {
        //
    }

    /**
     * Process a prompt and generate website code
     */
    public function processPrompt(Prompt $prompt): void
    {
        $prompt->update(['status' => 'processing']);

        try {
            // Get user's preferred model from project settings
            $preferredModel = $this->getPreferredModel($prompt->project);
            $providers = $this->getAvailableProviders($preferredModel);

            if (empty($providers)) {
                Log::warning('No AI providers configured, falling back to mock generation');
                $this->generateWithFallback($prompt);

                return;
            }

            $lastException = null;
            $modelMapping = $this->getModelServiceMapping();

            foreach ($providers as $provider) {
                try {
                    Log::info("Attempting to generate website with {$provider['name']}");

                    // Determine project type from project settings
                    $projectType = $this->getProjectType($prompt->project);
                    $result = $provider['service']->generateWebsite($prompt->prompt, $projectType);
                    $generatedProject = $result['project'];

                    // Update the prompt with the response
                    $prompt->update([
                        'response' => json_encode($generatedProject),
                        'status' => 'completed',
                        'processed_at' => now(),
                        'tokens_used' => $result['tokens_used'],
                        'metadata' => [
                            'model' => $result['model'],
                            'temperature' => $provider['temperature'],
                            'max_tokens' => $provider['max_tokens'],
                            'project_type' => $projectType,
                            'ai_provider' => $provider['name'],
                        ],
                    ]);

                    // Update the project with the generated project
                    $prompt->project->update([
                        'generated_code' => json_encode($generatedProject),
                        'status' => 'ready',
                        'last_built_at' => now(),
                    ]);

                    // Track AI generation completion
                    $this->collaborationService->aiGenerationCompleted($prompt->project, $prompt->project->user, 'completed');

                    // Auto-start container if requested
                    if ($prompt->auto_start_container) {
                        $this->autoStartContainer($prompt->project);
                    }

                    Log::info("Successfully generated website with {$provider['name']}");

                    return;

                } catch (\Exception $e) {
                    Log::warning("Failed to generate with {$provider['name']}: ".$e->getMessage());
                    $lastException = $e;

                    // If this was the user's preferred model, don't try others
                    if ($preferredModel && $provider['name'] === $modelMapping[$preferredModel]) {
                        Log::error("User's preferred model '{$preferredModel}' failed, stopping generation");
                        $prompt->update([
                            'status' => 'failed',
                            'response' => json_encode(['error' => "Your selected AI model '{$preferredModel}' failed to generate the website. Please try again or select a different model."]),
                            'processed_at' => now(),
                        ]);
                        return;
                    }

                    continue;
                }
            }

            // If all providers failed, throw the last exception
            throw $lastException ?? new \Exception('All AI providers failed');
        } catch (\Exception $e) {
            Log::error('All AI generation attempts failed: '.$e->getMessage(), [
                'prompt_id' => $prompt->id,
                'project_id' => $prompt->project_id,
                'error' => $e->getMessage(),
            ]);

            // Fallback to mock generation
            $this->generateWithFallback($prompt);
        }
    }

    /**
     * Get project type from project settings
     */
    private function getProjectType(Project $project): string
    {
        $settings = $project->settings ?? [];
        $stack = strtolower(trim($settings['stack'] ?? ''));

        Log::info('Determining project type', [
            'project_id' => $project->id,
            'stack' => $stack,
            'stack_type' => gettype($stack),
            'stack_length' => strlen($stack),
            'settings' => $settings,
        ]);

        // Normalize and map stack names with flexible matching
        // Order matters - more specific patterns first
        $stackMappings = [
            'astro' => ['astro', 'astro + typescript', 'astro typescript'],
            'nodejs-express' => ['nodejs-express', 'node.js + express', 'nodejs + express', 'express', 'express.js'],
            'python-fastapi' => ['python-fastapi', 'python + fastapi', 'python + fastapi + async', 'fastapi', 'fastapi + async'],
            'nuxt3' => ['nuxt3', 'nuxt 3', 'nuxt + typescript', 'nuxt typescript'],
            'vite-vue' => ['vite-vue', 'vite + vue', 'vite + vue + typescript', 'vite vue'],
            'vite-react' => ['vite-react', 'vite + react', 'vite + react + typescript'],
            'nextjs' => ['next.js', 'nextjs + react', 'nextjs', 'next'],
            'sveltekit' => ['svelte + kit', 'sveltekit', 'svelte'],
            'vite' => ['vite'], // Generic vite fallback
        ];

        // Check for partial matches
        foreach ($stackMappings as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($stack, $pattern)) {
                    Log::info("Detected {$type} project type from stack: {$stack}");

                    // If it's just 'vite', default to 'vite-react'
                    if ($type === 'vite') {
                        return 'vite-react';
                    }

                    return $type;
                }
            }
        }

        // Log unknown stack and default to Next.js for backward compatibility
        Log::warning("Unknown stack type: '{$stack}', defaulting to nextjs", [
            'project_id' => $project->id,
            'stack' => $stack,
            'settings' => $settings,
        ]);

        return 'nextjs';
    }

    /**
     * Get user's preferred model from project settings
     */
    private function getPreferredModel(Project $project): ?string
    {
        $settings = $project->settings ?? [];
        $preferredModel = $settings['ai_model'] ?? null;
        
        Log::info('User preferred model', [
            'project_id' => $project->id,
            'preferred_model' => $preferredModel,
            'settings' => $settings,
        ]);
        
        return $preferredModel;
    }

    /**
     * Map UI model names to service names
     */
    private function getModelServiceMapping(): array
    {
        return [
            'Claude Code' => 'claude',
            'Codex (GPT-5)' => 'openai',
            'Gemini CLI' => 'gemini',
            'Cursor CLI' => 'cursor-cli',
        ];
    }

    /**
     * Get available AI providers, prioritizing user's preferred model
     */
    private function getAvailableProviders(?string $preferredModel = null): array
    {
        $providers = [];
        $modelMapping = $this->getModelServiceMapping();
        
        // If user has a preferred model, ONLY use that model (no fallback)
        if ($preferredModel && isset($modelMapping[$preferredModel])) {
            $preferredService = $modelMapping[$preferredModel];
            $preferredProvider = $this->getProviderByName($preferredService);
            
            if ($preferredProvider) {
                $providers[] = $preferredProvider;
                Log::info("Using ONLY user's preferred model: {$preferredModel} -> {$preferredService} (no fallback)");
                return $providers; // Return only the preferred provider
            } else {
                Log::warning("User's preferred model '{$preferredModel}' is not configured, will use fallback");
            }
        }

        // Fallback to all available providers only if no preferred model is set
        $allProviders = [
            'claude' => [
                'name' => 'claude',
                'service' => $this->claudeAIService,
                'temperature' => config('services.claude.temperature', 0.7),
                'max_tokens' => config('services.claude.max_tokens', 4000),
            ],
            'openai' => [
                'name' => 'openai',
                'service' => $this->openAIService,
                'temperature' => config('services.openai.temperature', 0.7),
                'max_tokens' => config('services.openai.max_tokens', 4000),
            ],
            'gemini' => [
                'name' => 'gemini',
                'service' => $this->geminiAIService,
                'temperature' => config('services.gemini.temperature', 0.7),
                'max_tokens' => config('services.gemini.max_tokens', 4000),
            ],
            'cursor-cli' => [
                'name' => 'cursor-cli',
                'service' => $this->cursorAIService,
                'temperature' => 0.7,
                'max_tokens' => 4000,
            ],
        ];

        // Add all configured providers for fallback
        foreach ($allProviders as $serviceName => $provider) {
            if ($provider['service']->isConfigured()) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    /**
     * Get provider by service name
     */
    private function getProviderByName(string $serviceName): ?array
    {
        switch ($serviceName) {
            case 'claude':
                if ($this->claudeAIService->isConfigured()) {
                    return [
                        'name' => 'claude',
                        'service' => $this->claudeAIService,
                        'temperature' => config('services.claude.temperature', 0.7),
                        'max_tokens' => config('services.claude.max_tokens', 4000),
                    ];
                }
                break;
            case 'openai':
                if ($this->openAIService->isConfigured()) {
                    return [
                        'name' => 'openai',
                        'service' => $this->openAIService,
                        'temperature' => config('services.openai.temperature', 0.7),
                        'max_tokens' => config('services.openai.max_tokens', 4000),
                    ];
                }
                break;
            case 'gemini':
                if ($this->geminiAIService->isConfigured()) {
                    return [
                        'name' => 'gemini',
                        'service' => $this->geminiAIService,
                        'temperature' => config('services.gemini.temperature', 0.7),
                        'max_tokens' => config('services.gemini.max_tokens', 4000),
                    ];
                }
                break;
            case 'cursor-cli':
                if ($this->cursorAIService->isConfigured()) {
                    return [
                        'name' => 'cursor-cli',
                        'service' => $this->cursorAIService,
                        'temperature' => 0.7,
                        'max_tokens' => 4000,
                    ];
                }
                break;
        }
        
        return null;
    }

    /**
     * Fallback to mock generation when all AI providers fail
     */
    private function generateWithFallback(Prompt $prompt): void
    {
        try {
            Log::info('Using fallback mock generation for prompt: '.$prompt->id);

            // Determine project type and generate appropriate mock project
            $projectType = $this->getProjectType($prompt->project);
            $mockProject = $this->generateMockWebsite($prompt->prompt, $projectType);

            // Update the prompt with the response
            $prompt->update([
                'response' => json_encode($mockProject),
                'status' => 'completed',
                'processed_at' => now(),
                'tokens_used' => rand(100, 500),
                'metadata' => [
                    'model' => 'mock-fallback',
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'project_type' => $projectType,
                    'ai_provider' => 'mock',
                ],
            ]);

            // Update the project with the generated project
            $prompt->project->update([
                'generated_code' => json_encode($mockProject),
                'status' => 'ready',
                'last_built_at' => now(),
            ]);

            // Track AI generation completion
            $this->collaborationService->aiGenerationCompleted($prompt->project, $prompt->project->user, 'completed');

        } catch (\Exception $e) {
            Log::error('Fallback generation also failed: '.$e->getMessage());

            $prompt->update([
                'status' => 'failed',
                'processed_at' => now(),
            ]);

            $prompt->project->update([
                'status' => 'error',
            ]);

            // Track AI generation failure
            $this->collaborationService->aiGenerationCompleted($prompt->project, $prompt->project->user, 'failed');
        }
    }

    /**
     * Generate mock project based on prompt and project type
     */
    private function generateMockWebsite(string $prompt, string $projectType = 'nextjs'): array
    {
        // Analyze the prompt to determine the type of website
        $websiteType = $this->analyzePrompt($prompt);

        Log::info('Generating mock website', [
            'project_type' => $projectType,
            'website_type' => $websiteType,
            'prompt' => $prompt,
        ]);

        if ($projectType === 'vite-react') {
            switch ($websiteType) {
                case 'portfolio':
                    return $this->generateVitePortfolio($prompt);
                case 'ecommerce':
                    return $this->generateViteEcommerce($prompt);
                case 'blog':
                    return $this->generateViteBlog($prompt);
                case 'landing':
                    return $this->generateViteLanding($prompt);
                case 'dashboard':
                    return $this->generateViteDashboard($prompt);
                default:
                    return $this->generateViteGeneric($prompt);
            }
        } elseif ($projectType === 'sveltekit') {
            switch ($websiteType) {
                case 'portfolio':
                    return $this->generateSvelteKitPortfolio($prompt);
                case 'ecommerce':
                    return $this->generateSvelteKitEcommerce($prompt);
                case 'blog':
                    return $this->generateSvelteKitBlog($prompt);
                case 'landing':
                    return $this->generateSvelteKitLanding($prompt);
                case 'dashboard':
                    return $this->generateSvelteKitDashboard($prompt);
                default:
                    return $this->generateSvelteKitGeneric($prompt);
            }
        } elseif ($projectType === 'nextjs') {
            switch ($websiteType) {
                case 'portfolio':
                    return $this->generateNextJSPortfolio($prompt);
                case 'ecommerce':
                    return $this->generateNextJSEcommerce($prompt);
                case 'blog':
                    return $this->generateNextJSBlog($prompt);
                case 'landing':
                    return $this->generateNextJSLanding($prompt);
                case 'dashboard':
                    return $this->generateNextJSDashboard($prompt);
                default:
                    return $this->generateNextJSGeneric($prompt);
            }
        } else {
            // Unknown project type - log error and default to Next.js
            Log::error("Unknown project type in mock generation: {$projectType}, defaulting to Next.js");

            return $this->generateNextJSGeneric($prompt);
        }
    }

    private function analyzePrompt(string $prompt): string
    {
        $prompt = strtolower($prompt);

        if (str_contains($prompt, 'portfolio') || str_contains($prompt, 'personal') || str_contains($prompt, 'about me')) {
            return 'portfolio';
        }

        if (str_contains($prompt, 'shop') || str_contains($prompt, 'store') || str_contains($prompt, 'ecommerce') || str_contains($prompt, 'buy') || str_contains($prompt, 'sell')) {
            return 'ecommerce';
        }

        if (str_contains($prompt, 'blog') || str_contains($prompt, 'article') || str_contains($prompt, 'news')) {
            return 'blog';
        }

        if (str_contains($prompt, 'landing') || str_contains($prompt, 'marketing') || str_contains($prompt, 'conversion')) {
            return 'landing';
        }

        if (str_contains($prompt, 'dashboard') || str_contains($prompt, 'admin') || str_contains($prompt, 'analytics')) {
            return 'dashboard';
        }

        return 'generic';
    }

    private function generateNextJSGeneric(string $prompt): array
    {
        return [
            'package.json' => json_encode([
                'name' => 'ai-generated-website',
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
            ], JSON_PRETTY_PRINT),
            'next.config.js' => "/** @type {import('next').NextConfig} */
const nextConfig = {
  experimental: {
    appDir: true,
  },
}

module.exports = nextConfig",
            'tsconfig.json' => json_encode([
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
            ], JSON_PRETTY_PRINT),
            'app/layout.tsx' => 'import type { Metadata } from \'next\'
import { Inter } from \'next/font/google\'
import \'./globals.css\'

const inter = Inter({ subsets: [\'latin\'] })

export const metadata: Metadata = {
  title: \'AI Generated Website\',
  description: \'Welcome to your custom website built with AI\',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body className={inter.className}>{children}</body>
    </html>
  )
}',
            'app/page.tsx' => 'export default function Home() {
  return (
    <main className="min-h-screen">
      <section className="hero">
        <div className="container">
          <h1>Welcome to Your AI-Generated Website</h1>
          <p>This website was created based on your prompt: "'.htmlspecialchars($prompt).'"</p>
          <a href="#features" className="btn">Learn More</a>
        </div>
      </section>
      
      <section className="features" id="features">
        <div className="container">
          <h2>Features</h2>
          <div className="features-grid">
            <div className="feature">
              <h3>Responsive Design</h3>
              <p>This website is fully responsive and looks great on all devices.</p>
            </div>
            <div className="feature">
              <h3>Modern UI</h3>
              <p>Clean, modern design with beautiful gradients and animations.</p>
            </div>
            <div className="feature">
              <h3>Fast Loading</h3>
              <p>Optimized for speed and performance across all platforms.</p>
            </div>
          </div>
        </div>
      </section>
      
      <footer className="footer">
        <div className="container">
          <p>&copy; 2024 AI Generated Website. Created with love by AI.</p>
        </div>
      </footer>
    </main>
  )
}',
            'app/globals.css' => '* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
  line-height: 1.6;
  color: #333;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 100px 0;
  text-align: center;
}

.hero h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  font-weight: 700;
}

.hero p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.btn {
  display: inline-block;
  background: #ff6b6b;
  color: white;
  padding: 12px 30px;
  text-decoration: none;
  border-radius: 50px;
  font-weight: 600;
  transition: transform 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
}

.features {
  padding: 80px 0;
  background: #f8f9fa;
}

.features h2 {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 3rem;
  color: #333;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
}

.feature {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  text-align: center;
}

.feature h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #333;
}

.feature p {
  color: #666;
  line-height: 1.6;
}

.footer {
  background: #333;
  color: white;
  text-align: center;
  padding: 2rem 0;
}

@media (max-width: 768px) {
  .hero h1 {
    font-size: 2rem;
  }
  
  .hero p {
    font-size: 1rem;
  }
  
  .features-grid {
    grid-template-columns: 1fr;
  }
}',
        ];
    }

    private function generateNextJSPortfolio(string $prompt): array
    {
        return $this->generateNextJSGeneric($prompt);
    }

    private function generateNextJSEcommerce(string $prompt): array
    {
        return $this->generateNextJSGeneric($prompt);
    }

    private function generateNextJSBlog(string $prompt): array
    {
        return $this->generateNextJSGeneric($prompt);
    }

    private function generateNextJSLanding(string $prompt): array
    {
        return $this->generateNextJSGeneric($prompt);
    }

    private function generateNextJSDashboard(string $prompt): array
    {
        return $this->generateNextJSGeneric($prompt);
    }

    // Legacy methods for backward compatibility
    private function generatePortfolioWebsite(string $prompt): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Creative Professional</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; font-weight: 700; }
        .hero p { font-size: 1.3rem; margin-bottom: 2rem; opacity: 0.9; }
        .btn { display: inline-block; background: #ff6b6b; color: white; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: 600; transition: transform 0.3s ease; }
        .btn:hover { transform: translateY(-2px); }
        .projects { padding: 80px 0; background: #f8f9fa; }
        .projects h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .projects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .project { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .project h3 { font-size: 1.5rem; margin-bottom: 1rem; color: #333; }
        .project p { color: #666; line-height: 1.6; }
        .skills { padding: 80px 0; background: white; }
        .skills h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .skills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .skill { background: #f8f9fa; padding: 1.5rem; border-radius: 10px; text-align: center; }
        .footer { background: #333; color: white; text-align: center; padding: 2rem 0; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>Creative Professional</h1>
            <p>Full-Stack Developer & UI/UX Designer</p>
            <a href="#projects" class="btn">View My Work</a>
        </div>
    </section>
    
    <section class="projects" id="projects">
        <div class="container">
            <h2>Featured Projects</h2>
            <div class="projects-grid">
                <div class="project">
                    <h3>E-commerce Platform</h3>
                    <p>Built with React and Node.js, featuring real-time inventory management and payment processing.</p>
                </div>
                <div class="project">
                    <h3>Mobile Banking App</h3>
                    <p>Secure mobile application with biometric authentication and real-time transaction monitoring.</p>
                </div>
                <div class="project">
                    <h3>AI-Powered Dashboard</h3>
                    <p>Data visualization dashboard with machine learning insights and predictive analytics.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="skills">
        <div class="container">
            <h2>Skills & Technologies</h2>
            <div class="skills-grid">
                <div class="skill">React</div>
                <div class="skill">Node.js</div>
                <div class="skill">Python</div>
                <div class="skill">TypeScript</div>
                <div class="skill">AWS</div>
                <div class="skill">Docker</div>
            </div>
        </div>
    </section>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Creative Professional. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>';
    }

    private function generateEcommerceWebsite(string $prompt): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Store - Premium Products</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; font-weight: 700; }
        .hero p { font-size: 1.3rem; margin-bottom: 2rem; opacity: 0.9; }
        .btn { display: inline-block; background: #ff6b6b; color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: 600; transition: transform 0.3s ease; font-size: 1.1rem; }
        .btn:hover { transform: translateY(-2px); }
        .products { padding: 80px 0; background: #f8f9fa; }
        .products h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
        .product { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; }
        .product h3 { font-size: 1.3rem; margin-bottom: 1rem; color: #333; }
        .product .price { font-size: 1.5rem; font-weight: 700; color: #ff6b6b; margin-bottom: 1rem; }
        .features { padding: 80px 0; background: white; }
        .features h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .feature { background: #f8f9fa; padding: 2rem; border-radius: 10px; text-align: center; }
        .feature h3 { font-size: 1.5rem; margin-bottom: 1rem; color: #333; }
        .feature p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>Premium Products</h1>
            <p>Discover the latest in technology with our curated collection of premium products</p>
            <a href="#products" class="btn">Shop Now</a>
        </div>
    </section>
    
    <section class="products" id="products">
        <div class="container">
            <h2>Featured Products</h2>
            <div class="products-grid">
                <div class="product">
                    <h3>Wireless Headphones</h3>
                    <div class="price">$199.99</div>
                    <p>Premium sound quality with noise cancellation</p>
                </div>
                <div class="product">
                    <h3>Smart Watch</h3>
                    <div class="price">$299.99</div>
                    <p>Track your fitness and stay connected</p>
                </div>
                <div class="product">
                    <h3>Laptop Stand</h3>
                    <div class="price">$79.99</div>
                    <p>Ergonomic design for better productivity</p>
                </div>
                <div class="product">
                    <h3>Phone Case</h3>
                    <div class="price">$29.99</div>
                    <p>Protect your device in style</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="features">
        <div class="container">
            <h2>Why Choose Us?</h2>
            <div class="features-grid">
                <div class="feature">
                    <h3>Free Shipping</h3>
                    <p>Free shipping on all orders over $50. Fast and reliable delivery worldwide.</p>
                </div>
                <div class="feature">
                    <h3>2-Year Warranty</h3>
                    <p>All products come with our comprehensive 2-year warranty for peace of mind.</p>
                </div>
                <div class="feature">
                    <h3>24/7 Support</h3>
                    <p>Our expert support team is available around the clock to help you.</p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>';
    }

    private function generateBlogWebsite(string $prompt): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Blog - Latest Insights</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; font-weight: 700; }
        .hero p { font-size: 1.3rem; margin-bottom: 2rem; opacity: 0.9; }
        .articles { padding: 80px 0; background: #f8f9fa; }
        .articles h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .articles-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; }
        .article { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .article h3 { font-size: 1.5rem; margin-bottom: 1rem; color: #333; }
        .article p { color: #666; line-height: 1.6; margin-bottom: 1rem; }
        .article .date { color: #999; font-size: 0.9rem; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>Tech Blog</h1>
            <p>Latest insights and trends in technology</p>
        </div>
    </section>
    
    <section class="articles">
        <div class="container">
            <h2>Latest Articles</h2>
            <div class="articles-grid">
                <div class="article">
                    <h3>The Future of Web Development</h3>
                    <p>Exploring the latest trends and technologies shaping the future of web development...</p>
                    <div class="date">Published on '.date('M d, Y').'</div>
                </div>
                <div class="article">
                    <h3>AI in Modern Applications</h3>
                    <p>How artificial intelligence is revolutionizing the way we build and use applications...</p>
                    <div class="date">Published on '.date('M d, Y', strtotime('-2 days')).'</div>
                </div>
                <div class="article">
                    <h3>Responsive Design Best Practices</h3>
                    <p>Essential tips and techniques for creating beautiful, responsive websites...</p>
                    <div class="date">Published on '.date('M d, Y', strtotime('-5 days')).'</div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>';
    }

    private function generateLandingPage(string $prompt): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page - Convert Visitors</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 4rem; margin-bottom: 1rem; font-weight: 700; }
        .hero p { font-size: 1.4rem; margin-bottom: 2rem; opacity: 0.9; }
        .btn { display: inline-block; background: #fff; color: #ff6b6b; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: 600; transition: transform 0.3s ease; font-size: 1.1rem; }
        .btn:hover { transform: translateY(-2px); }
        .benefits { padding: 80px 0; background: white; }
        .benefits h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .benefits-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .benefit { text-align: center; padding: 2rem; }
        .benefit h3 { font-size: 1.5rem; margin-bottom: 1rem; color: #333; }
        .benefit p { color: #666; line-height: 1.6; }
        .cta { background: #333; color: white; text-align: center; padding: 80px 0; }
        .cta h2 { font-size: 2.5rem; margin-bottom: 1rem; }
        .cta p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>Transform Your Business</h1>
            <p>Join thousands of satisfied customers who have revolutionized their workflow</p>
            <a href="#benefits" class="btn">Get Started Today</a>
        </div>
    </section>
    
    <section class="benefits" id="benefits">
        <div class="container">
            <h2>Why Choose Us?</h2>
            <div class="benefits-grid">
                <div class="benefit">
                    <h3>Save Time</h3>
                    <p>Automate your processes and focus on what matters most to your business.</p>
                </div>
                <div class="benefit">
                    <h3>Increase Revenue</h3>
                    <p>Our proven strategies help you convert more visitors into paying customers.</p>
                </div>
                <div class="benefit">
                    <h3>Expert Support</h3>
                    <p>Get dedicated support from our team of experts whenever you need help.</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="cta">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <p>Don\'t wait - start transforming your business today!</p>
            <a href="#" class="btn">Start Your Free Trial</a>
        </div>
    </section>
</body>
</html>';
    }

    private function generateDashboardWebsite(string $prompt): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Analytics & Insights</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .header { background: white; padding: 1rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header h1 { font-size: 2rem; color: #333; }
        .dashboard { padding: 2rem 0; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat h3 { font-size: 1.2rem; margin-bottom: 0.5rem; color: #666; }
        .stat .value { font-size: 2rem; font-weight: 700; color: #333; }
        .charts { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; }
        .chart { background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .chart h3 { font-size: 1.2rem; margin-bottom: 1rem; color: #333; }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Analytics Dashboard</h1>
        </div>
    </div>
    
    <div class="dashboard">
        <div class="container">
            <div class="stats">
                <div class="stat">
                    <h3>Total Users</h3>
                    <div class="value">12,543</div>
                </div>
                <div class="stat">
                    <h3>Revenue</h3>
                    <div class="value">$45,231</div>
                </div>
                <div class="stat">
                    <h3>Conversion Rate</h3>
                    <div class="value">3.2%</div>
                </div>
                <div class="stat">
                    <h3>Active Sessions</h3>
                    <div class="value">1,234</div>
                </div>
            </div>
            
            <div class="charts">
                <div class="chart">
                    <h3>User Growth</h3>
                    <p>Chart showing user growth over time...</p>
                </div>
                <div class="chart">
                    <h3>Revenue Trends</h3>
                    <p>Chart showing revenue trends and patterns...</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Auto-start a Docker container for the project
     */
    private function autoStartContainer(Project $project): void
    {
        try {
            // Check if Docker is available
            if (! $this->dockerService->isDockerAvailable()) {
                Log::warning('Docker not available for auto-start container', [
                    'project_id' => $project->id,
                ]);

                return;
            }

            // Create a new container
            $container = $project->containers()->create([
                'status' => 'starting',
            ]);

            // Start the container
            $success = $this->dockerService->startContainer($container);

            if ($success) {
                Log::info('Auto-started container successfully', [
                    'project_id' => $project->id,
                    'container_id' => $container->id,
                    'url' => $container->url,
                ]);
            } else {
                Log::error('Failed to auto-start container', [
                    'project_id' => $project->id,
                    'container_id' => $container->id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Auto-start container failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate Vite + React + TypeScript portfolio project
     */
    private function generateVitePortfolio(string $prompt): array
    {
        return [
            'package.json' => json_encode([
                'name' => 'portfolio-website',
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
            'src/App.tsx' => 'import { useState } from \'react\'
import \'./App.css\'

function App() {
  const [activeSection, setActiveSection] = useState(\'home\')

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Navigation */}
      <nav className="bg-white shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-16">
            <div className="flex items-center">
              <h1 className="text-xl font-bold text-gray-900">Portfolio</h1>
            </div>
            <div className="flex space-x-8">
              <button 
                onClick={() => setActiveSection(\'home\')}
                className="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              >
                Home
              </button>
              <button 
                onClick={() => setActiveSection(\'about\')}
                className="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              >
                About
              </button>
              <button 
                onClick={() => setActiveSection(\'projects\')}
                className="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              >
                Projects
              </button>
              <button 
                onClick={() => setActiveSection(\'contact\')}
                className="text-gray-500 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
              >
                Contact
              </button>
            </div>
          </div>
        </div>
      </nav>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        {activeSection === \'home\' && (
          <div className="text-center">
            <h1 className="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
              Welcome to My Portfolio
            </h1>
            <p className="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
              A showcase of my work and skills in web development
            </p>
            <div className="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
              <button 
                onClick={() => setActiveSection(\'projects\')}
                className="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 md:py-4 md:text-lg md:px-10"
              >
                View My Work
              </button>
            </div>
          </div>
        )}

        {activeSection === \'about\' && (
          <div className="max-w-3xl mx-auto">
            <h2 className="text-3xl font-bold text-gray-900">About Me</h2>
            <p className="mt-4 text-lg text-gray-600">
              I am a passionate web developer with expertise in React, TypeScript, and modern web technologies. 
              I love creating beautiful, functional, and user-friendly applications.
            </p>
            <div className="mt-8">
              <h3 className="text-xl font-semibold text-gray-900">Skills</h3>
              <div className="mt-4 flex flex-wrap gap-2">
                {[\'React\', \'TypeScript\', \'Vite\', \'Tailwind CSS\', \'Node.js\', \'Git\'].map((skill) => (
                  <span key={skill} className="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                    {skill}
                  </span>
                ))}
              </div>
            </div>
          </div>
        )}

        {activeSection === \'projects\' && (
          <div>
            <h2 className="text-3xl font-bold text-gray-900">My Projects</h2>
            <div className="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
              {[
                { title: \'E-commerce Platform\', description: \'Full-stack e-commerce solution with React and Node.js\' },
                { title: \'Task Management App\', description: \'Collaborative task management with real-time updates\' },
                { title: \'Portfolio Website\', description: \'Responsive portfolio built with React and Tailwind CSS\' },
              ].map((project, index) => (
                <div key={index} className="bg-white overflow-hidden shadow rounded-lg">
                  <div className="px-4 py-5 sm:p-6">
                    <h3 className="text-lg font-medium text-gray-900">{project.title}</h3>
                    <p className="mt-2 text-sm text-gray-600">{project.description}</p>
                    <div className="mt-4">
                      <button className="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                        View Project →
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {activeSection === \'contact\' && (
          <div className="max-w-2xl mx-auto">
            <h2 className="text-3xl font-bold text-gray-900">Get In Touch</h2>
            <p className="mt-4 text-lg text-gray-600">
              I would love to hear from you. Send me a message and I will respond as soon as possible.
            </p>
            <form className="mt-8 space-y-6">
              <div>
                <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                  Name
                </label>
                <input
                  type="text"
                  id="name"
                  className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-gray-700">
                  Email
                </label>
                <input
                  type="email"
                  id="email"
                  className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <div>
                <label htmlFor="message" className="block text-sm font-medium text-gray-700">
                  Message
                </label>
                <textarea
                  id="message"
                  rows={4}
                  className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                />
              </div>
              <div>
                <button
                  type="submit"
                  className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Send Message
                </button>
              </div>
            </form>
          </div>
        )}
      </main>
    </div>
  )
}

export default App',
            'src/App.css' => '.App {
  text-align: center;
}',
            'src/index.css' => '@import "tailwindcss/base";
@import "tailwindcss/components";
@import "tailwindcss/utilities";

body {
  margin: 0;
  font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\',
    \'Ubuntu\', \'Cantarell\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\',
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

code {
  font-family: source-code-pro, Menlo, Monaco, Consolas, \'Courier New\',
    monospace;
}',
        ];
    }

    /**
     * Generate Vite + React + TypeScript generic project
     */
    private function generateViteGeneric(string $prompt): array
    {
        return [
            'package.json' => json_encode([
                'name' => 'ai-generated-project',
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
            'src/App.tsx' => 'import { useState } from \'react\'
import \'./App.css\'

function App() {
  const [count, setCount] = useState(0)

  return (
    <div className="min-h-screen bg-gray-100 flex items-center justify-center">
      <div className="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <div className="p-8">
          <h1 className="text-2xl font-bold text-gray-900 mb-4">
            Welcome to Your Vite + React Project
          </h1>
          <p className="text-gray-600 mb-6">
            This project was generated based on your prompt: "{prompt}"
          </p>
          <div className="text-center">
            <button
              onClick={() => setCount((count) => count + 1)}
              className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
              Count is {count}
            </button>
            <p className="mt-4 text-sm text-gray-500">
              Edit <code className="bg-gray-100 px-2 py-1 rounded">src/App.tsx</code> and save to test HMR
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}

export default App',
            'src/App.css' => '.App {
  text-align: center;
}',
            'src/index.css' => '@import "tailwindcss/base";
@import "tailwindcss/components";
@import "tailwindcss/utilities";

body {
  margin: 0;
  font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\',
    \'Ubuntu\', \'Cantarell\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\',
    sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

code {
  font-family: source-code-pro, Menlo, Monaco, Consolas, \'Courier New\',
    monospace;
}',
        ];
    }

    // Add placeholder methods for other Vite project types
    private function generateViteEcommerce(string $prompt): array
    {
        return $this->generateViteGeneric($prompt);
    }

    private function generateViteBlog(string $prompt): array
    {
        return $this->generateViteGeneric($prompt);
    }

    private function generateViteLanding(string $prompt): array
    {
        return $this->generateViteGeneric($prompt);
    }

    private function generateViteDashboard(string $prompt): array
    {
        return $this->generateViteGeneric($prompt);
    }

    // SvelteKit Mock Generation Methods
    private function generateSvelteKitPortfolio(string $prompt): array
    {
        return $this->generateSvelteKitGeneric($prompt);
    }

    private function generateSvelteKitEcommerce(string $prompt): array
    {
        return $this->generateSvelteKitGeneric($prompt);
    }

    private function generateSvelteKitBlog(string $prompt): array
    {
        return $this->generateSvelteKitGeneric($prompt);
    }

    private function generateSvelteKitLanding(string $prompt): array
    {
        return $this->generateSvelteKitGeneric($prompt);
    }

    private function generateSvelteKitDashboard(string $prompt): array
    {
        return $this->generateSvelteKitGeneric($prompt);
    }

    private function generateSvelteKitGeneric(string $prompt): array
    {
        return [
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
        ];
    }
}
