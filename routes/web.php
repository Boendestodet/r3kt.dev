<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Gallery routes (public access)
Route::get('gallery', [App\Http\Controllers\GalleryController::class, 'index'])->name('gallery.index');
Route::get('gallery/{project}', [App\Http\Controllers\GalleryController::class, 'show'])->name('gallery.show');
Route::get('api/gallery/stats', [App\Http\Controllers\GalleryController::class, 'stats'])->name('gallery.stats');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return redirect()->route('projects.index');
    })->name('dashboard');

    // Project routes
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    Route::post('projects/{project}/duplicate', [App\Http\Controllers\ProjectController::class, 'duplicate'])->name('projects.duplicate');
    
    // Prompt routes
    Route::post('projects/{project}/prompts', [App\Http\Controllers\PromptController::class, 'store'])->name('prompts.store');
    Route::get('prompts/{prompt}', [App\Http\Controllers\PromptController::class, 'show'])->name('prompts.show');
    Route::get('prompts/{prompt}/status', [App\Http\Controllers\PromptController::class, 'status'])->name('prompts.status');
    Route::delete('prompts/{prompt}', [App\Http\Controllers\PromptController::class, 'destroy'])->name('prompts.destroy');
    
    // Container routes
    Route::post('projects/{project}/containers', [App\Http\Controllers\ContainerController::class, 'store'])->name('containers.store');
    Route::get('containers/{container}', [App\Http\Controllers\ContainerController::class, 'show'])->name('containers.show');
    Route::post('containers/{container}/start', [App\Http\Controllers\ContainerController::class, 'start'])->name('containers.start');
    Route::post('containers/{container}/stop', [App\Http\Controllers\ContainerController::class, 'stop'])->name('containers.stop');
    Route::delete('containers/{container}', [App\Http\Controllers\ContainerController::class, 'destroy'])->name('containers.destroy');
    
    // Deployment routes
    Route::post('projects/{project}/deploy', [App\Http\Controllers\DeploymentController::class, 'deploy'])->name('deployments.deploy');
    Route::get('projects/{project}/deployment/status', [App\Http\Controllers\DeploymentController::class, 'status'])->name('deployments.status');
    Route::get('projects/{project}/deployment/logs', [App\Http\Controllers\DeploymentController::class, 'logs'])->name('deployments.logs');
    Route::post('projects/{project}/deployment/stop', [App\Http\Controllers\DeploymentController::class, 'stop'])->name('deployments.stop');
    Route::post('projects/{project}/deployment/restart', [App\Http\Controllers\DeploymentController::class, 'restart'])->name('deployments.restart');
    
    // Authenticated routes
    Route::post('projects/{project}/toggle-public', [App\Http\Controllers\GalleryController::class, 'togglePublic'])->name('projects.toggle-public');
    
    // Comment routes
    Route::get('projects/{project}/comments', [App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');
    Route::post('projects/{project}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::put('comments/{comment}', [App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('comments/{comment}/toggle-resolved', [App\Http\Controllers\CommentController::class, 'toggleResolved'])->name('comments.toggle-resolved');
    
    // Docker management routes
    Route::get('api/containers', [App\Http\Controllers\DeploymentController::class, 'containers'])->name('containers.index');
    Route::post('api/docker/cleanup', [App\Http\Controllers\DeploymentController::class, 'cleanup'])->name('docker.cleanup');
    
    // Subdomain management routes
    Route::get('api/subdomain/check', [App\Http\Controllers\SubdomainController::class, 'checkAvailability'])->name('subdomain.check');
    Route::post('projects/{project}/subdomain', [App\Http\Controllers\SubdomainController::class, 'updateSubdomain'])->name('projects.subdomain');
    Route::post('projects/{project}/custom-domain', [App\Http\Controllers\SubdomainController::class, 'configureCustomDomain'])->name('projects.custom-domain');
    Route::delete('projects/{project}/custom-domain', [App\Http\Controllers\SubdomainController::class, 'removeCustomDomain'])->name('projects.remove-custom-domain');
    Route::get('api/cloudflare/test', [App\Http\Controllers\SubdomainController::class, 'testCloudflareConnection'])->name('cloudflare.test');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
