<?php

use App\Models\Project;
use App\Models\User;
use App\Services\AccessibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->accessibilityService = app(AccessibilityService::class);
});

it('provides accessible color contrast ratios', function () {
    // Test high contrast combinations
    expect($this->accessibilityService->meetsWCAGAA('#000000', '#FFFFFF'))->toBeTrue();
    expect($this->accessibilityService->meetsWCAGAA('#FFFFFF', '#000000'))->toBeTrue();
    
    // Test low contrast combinations
    expect($this->accessibilityService->meetsWCAGAA('#CCCCCC', '#DDDDDD'))->toBeFalse();
    
    // Test WCAG AAA compliance
    expect($this->accessibilityService->meetsWCAGAAA('#000000', '#FFFFFF'))->toBeTrue();
    expect($this->accessibilityService->meetsWCAGAAA('#333333', '#FFFFFF'))->toBeTrue();
});

it('generates accessible color palettes', function () {
    $palette = $this->accessibilityService->generateAccessiblePalette('#3B82F6');
    
    expect($palette)->toHaveKeys(['primary', 'primary_dark', 'primary_light', 'text_on_primary', 'background_on_primary']);
    expect($palette['primary'])->toBe('#3B82F6');
    
    // Verify text color is accessible on primary background
    expect($this->accessibilityService->meetsWCAGAA($palette['text_on_primary'], $palette['primary']))->toBeTrue();
});

it('generates proper ARIA labels for dynamic content', function () {
    $label = $this->accessibilityService->generateAriaLabel('create', 'project', 'in dashboard');
    expect($label)->toBe('Create project in dashboard');
    
    $label = $this->accessibilityService->generateAriaLabel('delete', 'container');
    expect($label)->toBe('Delete container');
});

it('generates screen reader friendly status messages', function () {
    $message = $this->accessibilityService->generateStatusMessage('success', 'project created', 'with 5 files');
    expect($message)->toBe('Status: success. Action: project created. Details: with 5 files');
    
    $message = $this->accessibilityService->generateStatusMessage('error', 'deployment failed');
    expect($message)->toBe('Status: error. Action: deployment failed');
});

it('validates form accessibility requirements', function () {
    $formData = [
        'name' => [
            'label' => 'Project Name',
            'required' => true,
            'description' => 'Enter a unique name for your project',
            'error' => null,
            'aria_describedby' => null
        ],
        'description' => [
            'label' => 'Description',
            'required' => false,
            'description' => null,
            'error' => 'Description is too long',
            'aria_describedby' => 'description-error'
        ],
        'invalid_field' => [
            'label' => '', // Missing label
            'required' => true,
            'description' => null, // Missing description for required field
            'error' => 'Field is invalid',
            'aria_describedby' => null // Missing aria-describedby for error
        ]
    ];
    
    $issues = $this->accessibilityService->validateFormAccessibility($formData);
    
    expect($issues)->toHaveCount(3);
    expect($issues)->toContain("Field 'invalid_field' is missing a label");
    expect($issues)->toContain("Required field 'invalid_field' should have a description");
    expect($issues)->toContain("Field 'invalid_field' with error should have aria-describedby");
});

it('renders projects page with proper accessibility attributes', function () {
    Project::factory()->count(3)->create(['user_id' => $this->user->id]);
    
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for proper heading structure
    $response->assertSee('Projects', false); // Should have a main heading
    
    // Check for proper form labels
    $response->assertSee('name="name"', false); // Should have name input
    $response->assertSee('name="description"', false); // Should have description input
});

it('provides keyboard navigation support', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for focusable elements
    $response->assertSee('tabindex', false); // Should have tabindex attributes
    $response->assertSee('role=', false); // Should have ARIA roles
});

it('supports screen reader announcements', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    
    $response = $this->post("/projects/{$project->id}/prompts", [
        'prompt' => 'Create a simple website'
    ]);
    
    $response->assertSuccessful();
    
    // Check for ARIA live regions
    $response->assertSee('aria-live', false);
    $response->assertSee('role="status"', false);
});

it('provides proper error handling with accessibility', function () {
    $response = $this->post('/projects', [
        'name' => '', // Invalid empty name
        'description' => 'Test description'
    ]);
    
    $response->assertSessionHasErrors(['name']);
    
    // Check that error messages are properly associated with form fields
    $response->assertSee('aria-invalid="true"', false);
    $response->assertSee('aria-describedby', false);
});

it('supports mobile-responsive design', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for responsive meta tags
    $response->assertSee('viewport', false);
    $response->assertSee('width=device-width', false);
    $response->assertSee('initial-scale=1', false);
});

it('provides proper loading states with accessibility', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for loading indicators
    $response->assertSee('aria-busy', false);
    $response->assertSee('role="status"', false);
    $response->assertSee('Loading', false);
});

it('supports dark mode accessibility', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for dark mode support
    $response->assertSee('dark:', false); // Tailwind dark mode classes
    $response->assertSee('prefers-color-scheme', false); // CSS media queries
});

it('provides proper focus management', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for focus management
    $response->assertSee('focus:', false); // Focus styles
    $response->assertSee('tabindex', false); // Tab order
    $response->assertSee('aria-label', false); // Accessible labels
});

it('supports reduced motion preferences', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for reduced motion support
    $response->assertSee('prefers-reduced-motion', false);
    $response->assertSee('animation:', false); // Should have animation controls
});

it('provides proper semantic HTML structure', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for semantic HTML
    $response->assertSee('<main', false); // Main content area
    $response->assertSee('<nav', false); // Navigation
    $response->assertSee('<header', false); // Page header
    $response->assertSee('<section', false); // Content sections
});

it('supports high contrast mode', function () {
    $response = $this->get('/projects');
    
    $response->assertSuccessful();
    
    // Check for high contrast support
    $response->assertSee('prefers-contrast', false);
    $response->assertSee('border-color', false); // Should have border colors
    $response->assertSee('background-color', false); // Should have background colors
});