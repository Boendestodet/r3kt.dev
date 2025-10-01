<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('applies security headers to all responses', function () {
    $response = $this->get('/projects');

    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-XSS-Protection', '1; mode=block');
    $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    $response->assertHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
    $response->assertHeader('Content-Security-Policy');
});

it('rate limits project creation requests', function () {
    // Create 30 projects (rate limit)
    for ($i = 0; $i < 30; $i++) {
        $response = $this->post('/projects', [
            'name' => "Test Project {$i}",
            'description' => 'Test description',
        ]);
        $response->assertSuccessful();
    }

    // 31st request should be rate limited
    $response = $this->post('/projects', [
        'name' => 'Rate Limited Project',
        'description' => 'This should be rate limited',
    ]);

    $response->assertStatus(429);
    $response->assertJson([
        'message' => 'Too many requests. Please try again later.',
    ]);
});

it('rate limits AI prompt generation', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    // Create 10 prompts (rate limit)
    for ($i = 0; $i < 10; $i++) {
        $response = $this->post("/projects/{$project->id}/prompts", [
            'prompt' => "Generate a website for test {$i}",
        ]);
        $response->assertSuccessful();
    }

    // 11th request should be rate limited
    $response = $this->post("/projects/{$project->id}/prompts", [
        'prompt' => 'This should be rate limited',
    ]);

    $response->assertStatus(429);
});

it('sanitizes project names to prevent XSS', function () {
    $maliciousName = '<script>alert("XSS")</script>Malicious Project';
    
    $response = $this->post('/projects', [
        'name' => $maliciousName,
        'description' => 'Test description',
    ]);

    $response->assertSuccessful();
    
    $project = Project::where('user_id', $this->user->id)->latest()->first();
    expect($project->name)->not->toContain('<script>');
    expect($project->name)->toBe('Malicious Project');
});

it('sanitizes project descriptions to prevent XSS', function () {
    $maliciousDescription = '<img src="x" onerror="alert(\'XSS\')">Malicious Description';
    
    $response = $this->post('/projects', [
        'name' => 'Test Project',
        'description' => $maliciousDescription,
    ]);

    $response->assertSuccessful();
    
    $project = Project::where('user_id', $this->user->id)->latest()->first();
    expect($project->description)->not->toContain('<img');
    expect($project->description)->not->toContain('onerror');
    expect($project->description)->toBe('Malicious Description');
});

it('sanitizes AI prompts to prevent injection attacks', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    
    $maliciousPrompt = 'Generate a website with <script>alert("XSS")</script> and SQL injection: \'; DROP TABLE users; --';
    
    $response = $this->post("/projects/{$project->id}/prompts", [
        'prompt' => $maliciousPrompt,
    ]);

    $response->assertSuccessful();
    
    $prompt = $project->prompts()->latest()->first();
    expect($prompt->prompt)->not->toContain('<script>');
    expect($prompt->prompt)->not->toContain('DROP TABLE');
});

it('validates file names to prevent path traversal', function () {
    $sanitizer = app(\App\Services\InputSanitizationService::class);
    
    $maliciousNames = [
        '../../../etc/passwd',
        '..\\..\\windows\\system32\\config\\sam',
        'file/with/slashes',
        'file\\with\\backslashes',
        'file<with>dangerous:chars|?*',
    ];

    foreach ($maliciousNames as $maliciousName) {
        $sanitized = $sanitizer->sanitizeFileName($maliciousName);
        expect($sanitized)->not->toContain('../');
        expect($sanitized)->not->toContain('..\\');
        expect($sanitized)->not->toContain('/');
        expect($sanitized)->not->toContain('\\');
        expect($sanitized)->not->toContain('<');
        expect($sanitized)->not->toContain('>');
        expect($sanitized)->not->toContain(':');
        expect($sanitized)->not->toContain('|');
        expect($sanitized)->not->toContain('?');
        expect($sanitized)->not->toContain('*');
    }
});

it('validates URLs to prevent malicious redirects', function () {
    $sanitizer = app(\App\Services\InputSanitizationService::class);
    
    $maliciousUrls = [
        'javascript:alert("XSS")',
        'data:text/html,<script>alert("XSS")</script>',
        'ftp://malicious.com',
        'file:///etc/passwd',
    ];

    foreach ($maliciousUrls as $maliciousUrl) {
        $sanitized = $sanitizer->sanitizeUrl($maliciousUrl);
        expect($sanitized)->toBeNull();
    }

    // Valid URLs should be sanitized properly
    $validUrls = [
        'https://example.com',
        'http://example.com',
        'example.com', // Should get https:// prefix
    ];

    foreach ($validUrls as $validUrl) {
        $sanitized = $sanitizer->sanitizeUrl($validUrl);
        expect($sanitized)->toStartWith('https://');
    }
});

it('prevents SQL injection in project names', function () {
    $sqlInjectionName = "'; DROP TABLE projects; --";
    
    $response = $this->post('/projects', [
        'name' => $sqlInjectionName,
        'description' => 'Test description',
    ]);

    $response->assertSuccessful();
    
    // Verify the project was created with sanitized name
    $project = Project::where('user_id', $this->user->id)->latest()->first();
    expect($project->name)->not->toContain('DROP TABLE');
    expect($project->name)->not->toContain(';');
    expect($project->name)->not->toContain('--');
});

it('rate limits are user-specific', function () {
    $user2 = User::factory()->create();
    
    // User 1 creates 30 projects
    for ($i = 0; $i < 30; $i++) {
        $response = $this->post('/projects', [
            'name' => "User1 Project {$i}",
            'description' => 'Test description',
        ]);
        $response->assertSuccessful();
    }

    // User 1 should be rate limited
    $response = $this->post('/projects', [
        'name' => 'User1 Rate Limited',
        'description' => 'Test description',
    ]);
    $response->assertStatus(429);

    // Switch to user 2
    $this->actingAs($user2);
    
    // User 2 should not be rate limited
    $response = $this->post('/projects', [
        'name' => 'User2 Project',
        'description' => 'Test description',
    ]);
    $response->assertSuccessful();
});

it('rate limits reset after time window', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    // Create 10 prompts to hit rate limit
    for ($i = 0; $i < 10; $i++) {
        $response = $this->post("/projects/{$project->id}/prompts", [
            'prompt' => "Generate a website for test {$i}",
        ]);
        $response->assertSuccessful();
    }

    // Should be rate limited
    $response = $this->post("/projects/{$project->id}/prompts", [
        'prompt' => 'This should be rate limited',
    ]);
    $response->assertStatus(429);

    // Clear the rate limiter
    RateLimiter::clear("rate_limit:prompts:user:{$this->user->id}");

    // Should work again
    $response = $this->post("/projects/{$project->id}/prompts", [
        'prompt' => 'This should work now',
    ]);
    $response->assertSuccessful();
});