<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a demo user if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'demo@lovable.dev'],
            [
                'name' => 'Demo User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create sample projects
        $projects = [
            [
                'name' => 'Modern Portfolio Website',
                'description' => 'A sleek portfolio website with dark mode and smooth animations',
                'status' => 'ready',
                'is_public' => true,
                'generated_code' => $this->getPortfolioCode(),
                'preview_url' => 'http://localhost:8001',
                'last_built_at' => now()->subHours(2),
            ],
            [
                'name' => 'E-commerce Landing Page',
                'description' => 'A conversion-focused landing page for an online store',
                'status' => 'ready',
                'is_public' => true,
                'generated_code' => $this->getEcommerceCode(),
                'preview_url' => 'http://localhost:8002',
                'last_built_at' => now()->subHours(5),
            ],
            [
                'name' => 'SaaS Dashboard',
                'description' => 'A modern dashboard interface for a SaaS application',
                'status' => 'building',
                'is_public' => false,
                'generated_code' => null,
                'preview_url' => null,
                'last_built_at' => null,
            ],
            [
                'name' => 'Restaurant Website',
                'description' => 'A beautiful website for a local restaurant with menu and reservations',
                'status' => 'draft',
                'is_public' => false,
                'generated_code' => null,
                'preview_url' => null,
                'last_built_at' => null,
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create(array_merge($projectData, [
                'user_id' => $user->id,
                'slug' => \Illuminate\Support\Str::slug($projectData['name']),
                'settings' => [
                    'theme' => 'modern',
                    'framework' => 'react',
                    'styling' => 'tailwind',
                ],
            ]));
        }
    }

    private function getPortfolioCode(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>John Doe - Portfolio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; font-weight: 700; }
        .hero p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
        .btn { display: inline-block; background: #ff6b6b; color: white; padding: 12px 30px; text-decoration: none; border-radius: 50px; font-weight: 600; transition: transform 0.3s ease; }
        .btn:hover { transform: translateY(-2px); }
        .projects { padding: 80px 0; background: #f8f9fa; }
        .projects h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .projects-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .project { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .project h3 { font-size: 1.5rem; margin-bottom: 1rem; color: #333; }
        .project p { color: #666; line-height: 1.6; }
        .footer { background: #333; color: white; text-align: center; padding: 2rem 0; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>John Doe</h1>
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
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 John Doe. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>';
    }

    private function getEcommerceCode(): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Premium Electronics</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .hero { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; font-weight: 700; }
        .hero p { font-size: 1.3rem; margin-bottom: 2rem; opacity: 0.9; }
        .btn { display: inline-block; background: #ff6b6b; color: white; padding: 15px 40px; text-decoration: none; border-radius: 50px; font-weight: 600; transition: transform 0.3s ease; font-size: 1.1rem; }
        .btn:hover { transform: translateY(-2px); }
        .features { padding: 80px 0; background: #f8f9fa; }
        .features h2 { text-align: center; font-size: 2.5rem; margin-bottom: 3rem; color: #333; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; }
        .feature { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; }
        .feature h3 { font-size: 1.5rem; margin-bottom: 1rem; color: #333; }
        .feature p { color: #666; line-height: 1.6; }
        .cta { background: #333; color: white; text-align: center; padding: 80px 0; }
        .cta h2 { font-size: 2.5rem; margin-bottom: 1rem; }
        .cta p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
    </style>
</head>
<body>
    <section class="hero">
        <div class="container">
            <h1>Premium Electronics</h1>
            <p>Discover the latest in technology with our curated collection of premium electronics</p>
            <a href="#features" class="btn">Shop Now</a>
        </div>
    </section>
    
    <section class="features" id="features">
        <div class="container">
            <h2>Why Choose TechStore?</h2>
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
    
    <section class="cta">
        <div class="container">
            <h2>Ready to Upgrade?</h2>
            <p>Join thousands of satisfied customers who trust TechStore for their electronics needs.</p>
            <a href="#" class="btn">Start Shopping</a>
        </div>
    </section>
</body>
</html>';
    }
}
