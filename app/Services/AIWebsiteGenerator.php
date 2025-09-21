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
            // Try AI providers in order of preference
            $providers = $this->getAvailableProviders();

            if (empty($providers)) {
                Log::warning('No AI providers configured, falling back to mock generation');
                $this->generateWithFallback($prompt);

                return;
            }

            $lastException = null;

            foreach ($providers as $provider) {
                try {
                    Log::info("Attempting to generate website with {$provider['name']}");
                    $result = $provider['service']->generateWebsite($prompt->prompt);
                    $nextjsProject = $result['project'];

                    // Update the prompt with the response
                    $prompt->update([
                        'response' => json_encode($nextjsProject),
                        'status' => 'completed',
                        'processed_at' => now(),
                        'tokens_used' => $result['tokens_used'],
                        'metadata' => [
                            'model' => $result['model'],
                            'temperature' => $provider['temperature'],
                            'max_tokens' => $provider['max_tokens'],
                            'project_type' => 'nextjs',
                            'ai_provider' => $provider['name'],
                        ],
                    ]);

                    // Update the project with the generated Next.js project
                    $prompt->project->update([
                        'generated_code' => json_encode($nextjsProject),
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
     * Get available AI providers in order of preference
     */
    private function getAvailableProviders(): array
    {
        $providers = [];

        // Add Claude AI as first preference (if configured)
        if ($this->claudeAIService->isConfigured()) {
            $providers[] = [
                'name' => 'claude',
                'service' => $this->claudeAIService,
                'temperature' => config('services.claude.temperature', 0.7),
                'max_tokens' => config('services.claude.max_tokens', 4000),
            ];
        }

        // Add OpenAI as second preference (if configured)
        if ($this->openAIService->isConfigured()) {
            $providers[] = [
                'name' => 'openai',
                'service' => $this->openAIService,
                'temperature' => config('services.openai.temperature', 0.7),
                'max_tokens' => config('services.openai.max_tokens', 4000),
            ];
        }

        return $providers;
    }

    /**
     * Fallback to mock generation when all AI providers fail
     */
    private function generateWithFallback(Prompt $prompt): void
    {
        try {
            Log::info('Using fallback mock generation for prompt: '.$prompt->id);

            // Generate mock Next.js project based on the prompt
            $nextjsProject = $this->generateMockWebsite($prompt->prompt);

            // Update the prompt with the response
            $prompt->update([
                'response' => json_encode($nextjsProject),
                'status' => 'completed',
                'processed_at' => now(),
                'tokens_used' => rand(100, 500),
                'metadata' => [
                    'model' => 'mock-fallback',
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                    'project_type' => 'nextjs',
                    'ai_provider' => 'mock',
                ],
            ]);

            // Update the project with the generated Next.js project
            $prompt->project->update([
                'generated_code' => json_encode($nextjsProject),
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
     * Generate mock Next.js project based on prompt
     */
    private function generateMockWebsite(string $prompt): array
    {
        // Analyze the prompt to determine the type of website
        $websiteType = $this->analyzePrompt($prompt);

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
}
