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
    Route::get('projects/{project}/sandbox', function ($projectId) {
        return Inertia::render('projects/Sandbox', [
            'project' => [
                'id' => $projectId,
                'name' => 'My Sandbox Project',
                'stack' => 'Next.js',
                'model' => 'Claude Code',
                'status' => 'ready',
                'preview_url' => 'http://localhost:3000'
            ]
        ]);
    })->name('projects.sandbox');

    // Prompt routes
    Route::post('projects/{project}/prompts', [App\Http\Controllers\PromptController::class, 'store'])->name('prompts.store');
    Route::get('prompts/{prompt}', [App\Http\Controllers\PromptController::class, 'show'])->name('prompts.show');
    Route::get('prompts/{prompt}/status', [App\Http\Controllers\PromptController::class, 'status'])->name('prompts.status');
    Route::delete('prompts/{prompt}', [App\Http\Controllers\PromptController::class, 'destroy'])->name('prompts.destroy');

    // Container management routes (legacy - for backward compatibility)
    Route::post('projects/{project}/containers', [App\Http\Controllers\ContainerController::class, 'store'])->name('containers.store');
    Route::get('containers/{container}', [App\Http\Controllers\ContainerController::class, 'show'])->name('containers.show');
    Route::delete('containers/{container}', [App\Http\Controllers\ContainerController::class, 'destroy'])->name('containers.destroy');

    // Authenticated routes
    Route::post('projects/{project}/toggle-public', [App\Http\Controllers\GalleryController::class, 'togglePublic'])->name('projects.toggle-public');

    // Comment routes
    Route::get('projects/{project}/comments', [App\Http\Controllers\CommentController::class, 'index'])->name('comments.index');
    Route::post('projects/{project}/comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::put('comments/{comment}', [App\Http\Controllers\CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('comments/{comment}/toggle-resolved', [App\Http\Controllers\CommentController::class, 'toggleResolved'])->name('comments.toggle-resolved');

    // Docker management routes (consolidated)
    Route::prefix('api/docker')->name('docker.')->group(function () {
        Route::get('info', [App\Http\Controllers\DockerController::class, 'info'])->name('info');
        Route::get('containers', [App\Http\Controllers\DockerController::class, 'getRunningContainers'])->name('containers');
        Route::post('cleanup', [App\Http\Controllers\DockerController::class, 'cleanup'])->name('cleanup');
    });

    // Project Docker operations
    Route::prefix('api/projects/{project}')->name('projects.')->group(function () {
        Route::post('deploy', [App\Http\Controllers\DockerController::class, 'deploy'])->name('deploy');
        Route::post('docker/start', [App\Http\Controllers\DockerController::class, 'startContainer'])->name('docker.start');
        Route::get('docker/preview', [App\Http\Controllers\DockerController::class, 'getPreviewUrl'])->name('docker.preview');
        Route::get('docker/status', [App\Http\Controllers\DockerController::class, 'getContainerStatus'])->name('docker.status');
        Route::get('docker/logs', [App\Http\Controllers\DockerController::class, 'getContainerLogs'])->name('docker.logs');
        Route::post('docker/stop', [App\Http\Controllers\DockerController::class, 'stopContainer'])->name('docker.stop');
        Route::post('docker/restart', [App\Http\Controllers\DockerController::class, 'restartContainer'])->name('docker.restart');
    });

    // Container Docker operations (for direct container access - when you have container ID)
    Route::prefix('api/containers/{container}')->name('containers.')->group(function () {
        Route::post('stop', [App\Http\Controllers\DockerController::class, 'stopContainer'])->name('stop');
        Route::post('restart', [App\Http\Controllers\DockerController::class, 'restartContainer'])->name('restart');
        Route::get('status', [App\Http\Controllers\DockerController::class, 'getContainerStatus'])->name('status');
        Route::get('logs', [App\Http\Controllers\DockerController::class, 'getContainerLogs'])->name('logs');
    });

    // Subdomain management routes
    Route::get('api/subdomain/check', [App\Http\Controllers\SubdomainController::class, 'checkAvailability'])->name('subdomain.check');
    Route::post('projects/{project}/subdomain', [App\Http\Controllers\SubdomainController::class, 'updateSubdomain'])->name('projects.subdomain');
    Route::post('projects/{project}/custom-domain', [App\Http\Controllers\SubdomainController::class, 'configureCustomDomain'])->name('projects.custom-domain');
    Route::delete('projects/{project}/custom-domain', [App\Http\Controllers\SubdomainController::class, 'removeCustomDomain'])->name('projects.remove-custom-domain');
    Route::get('api/cloudflare/test', [App\Http\Controllers\SubdomainController::class, 'testCloudflareConnection'])->name('cloudflare.test');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
