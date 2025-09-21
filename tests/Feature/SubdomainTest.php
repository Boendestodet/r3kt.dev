<?php

use App\Models\User;
use App\Models\Project;
use App\Services\CloudflareService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
});

test('project automatically generates subdomain on creation', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'My Awesome Project'
    ]);

    expect($project->subdomain)->not->toBeNull();
    expect($project->subdomain)->toBe('my-awesome-project');
});

test('project generates unique subdomain when duplicate exists', function () {
    // Create first project
    Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Project',
        'subdomain' => 'test-project'
    ]);

    // Create second project with same name
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'name' => 'Test Project'
    ]);

    expect($project->subdomain)->toBe('test-project-1');
});

test('subdomain validation works correctly', function () {
    // Valid subdomains
    expect(Project::isValidSubdomain('valid-subdomain'))->toBeTrue();
    expect(Project::isValidSubdomain('test123'))->toBeTrue();
    expect(Project::isValidSubdomain('a'))->toBeTrue();
    expect(Project::isValidSubdomain('a' . str_repeat('b', 60) . 'c'))->toBeTrue();

    // Invalid subdomains
    expect(Project::isValidSubdomain(''))->toBeFalse();
    expect(Project::isValidSubdomain('ab'))->toBeFalse(); // Too short
    expect(Project::isValidSubdomain('a' . str_repeat('b', 62) . 'c'))->toBeFalse(); // Too long
    expect(Project::isValidSubdomain('-invalid'))->toBeFalse(); // Starts with hyphen
    expect(Project::isValidSubdomain('invalid-'))->toBeFalse(); // Ends with hyphen
    expect(Project::isValidSubdomain('invalid.subdomain'))->toBeFalse(); // Contains dot
    expect(Project::isValidSubdomain('INVALID'))->toBeFalse(); // Uppercase
});

test('subdomain availability check works', function () {
    // Create a project with subdomain
    Project::factory()->create([
        'user_id' => $this->user->id,
        'subdomain' => 'taken-subdomain'
    ]);

    expect(Project::isSubdomainAvailable('taken-subdomain'))->toBeFalse();
    expect(Project::isSubdomainAvailable('available-subdomain'))->toBeTrue();
});

test('project generates correct subdomain URL', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'subdomain' => 'my-project'
    ]);

    $url = $project->getSubdomainUrl();
    expect($url)->toBe('https://my-project.r3kt.dev');
});

test('project uses custom domain when available', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'subdomain' => 'my-project',
        'custom_domain' => 'myproject.com'
    ]);

    $url = $project->getProjectUrl();
    expect($url)->toBe('https://myproject.com');
});

test('project falls back to subdomain when no custom domain', function () {
    $project = Project::factory()->create([
        'user_id' => $this->user->id,
        'subdomain' => 'my-project',
        'custom_domain' => null
    ]);

    $url = $project->getProjectUrl();
    expect($url)->toBe('https://my-project.r3kt.dev');
});

test('user can check subdomain availability via API', function () {
    // Test available subdomain
    $response = $this->getJson('/api/subdomain/check?subdomain=available-test');
    
    $response->assertSuccessful();
    $response->assertJson([
        'available' => true,
        'message' => 'Subdomain is available'
    ]);

    // Test taken subdomain
    Project::factory()->create([
        'user_id' => $this->user->id,
        'subdomain' => 'taken-test'
    ]);

    $response = $this->getJson('/api/subdomain/check?subdomain=taken-test');
    
    $response->assertSuccessful();
    $response->assertJson([
        'available' => false,
        'message' => 'Subdomain is already taken'
    ]);
});

test('user can update project subdomain', function () {
    $response = $this->postJson("/projects/{$this->project->id}/subdomain", [
        'subdomain' => 'new-subdomain'
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Subdomain updated successfully',
        'subdomain' => 'new-subdomain'
    ]);

    $this->project->refresh();
    expect($this->project->subdomain)->toBe('new-subdomain');
});

test('user cannot use invalid subdomain format', function () {
    $response = $this->postJson("/projects/{$this->project->id}/subdomain", [
        'subdomain' => 'invalid.subdomain'
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'Invalid subdomain format. Use only lowercase letters, numbers, and hyphens.'
    ]);
});

test('user cannot use taken subdomain', function () {
    // Create another project with subdomain
    Project::factory()->create([
        'user_id' => $this->user->id,
        'subdomain' => 'taken-subdomain'
    ]);

    $response = $this->postJson("/projects/{$this->project->id}/subdomain", [
        'subdomain' => 'taken-subdomain'
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'message' => 'Subdomain is already taken'
    ]);
});

test('user can configure custom domain', function () {
    $response = $this->postJson("/projects/{$this->project->id}/custom-domain", [
        'custom_domain' => 'myproject.com'
    ]);

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Custom domain configured successfully',
        'custom_domain' => 'myproject.com'
    ]);

    $this->project->refresh();
    expect($this->project->custom_domain)->toBe('myproject.com');
});

test('user can remove custom domain', function () {
    // First set a custom domain
    $this->project->update(['custom_domain' => 'myproject.com']);

    $response = $this->deleteJson("/projects/{$this->project->id}/custom-domain");

    $response->assertSuccessful();
    $response->assertJson([
        'success' => true,
        'message' => 'Custom domain removed successfully'
    ]);

    $this->project->refresh();
    expect($this->project->custom_domain)->toBeNull();
});

test('user cannot update other users project subdomain', function () {
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->postJson("/projects/{$otherProject->id}/subdomain", [
        'subdomain' => 'hacked-subdomain'
    ]);

    $response->assertForbidden();
});

test('cloudflare service can test connection', function () {
    $cloudflareService = app(CloudflareService::class);
    $result = $cloudflareService->testConnection();

    expect($result)->toHaveKey('success');
    expect($result)->toHaveKey('message');
});

test('cloudflare service handles missing configuration', function () {
    config(['services.cloudflare.api_token' => null]);
    config(['services.cloudflare.zone_id' => null]);

    $cloudflareService = app(CloudflareService::class);
    $result = $cloudflareService->createDnsRecord('test-subdomain');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toBe('Cloudflare not configured');
});

test('subdomain controller requires authentication', function () {
    auth()->logout();

    $response = $this->getJson('/api/subdomain/check?subdomain=test');
    $response->assertUnauthorized();

    $response = $this->postJson("/projects/{$this->project->id}/subdomain", [
        'subdomain' => 'test'
    ]);
    $response->assertUnauthorized();
});
