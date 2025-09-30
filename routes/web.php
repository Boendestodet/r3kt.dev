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

    // Project routes (Core CRUD)
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    Route::post('projects/{project}/duplicate', [App\Http\Controllers\ProjectController::class, 'duplicate'])->name('projects.duplicate');
    Route::get('api/projects/check-name', [App\Http\Controllers\ProjectController::class, 'checkName'])->name('projects.check-name');
    Route::get('api/projects/{project}', [App\Http\Controllers\ProjectController::class, 'showApi'])->name('projects.show-api');

    // Project Sandbox routes
    Route::get('projects/{project}/sandbox', [App\Http\Controllers\ProjectSandboxController::class, 'show'])->name('projects.sandbox');

    // Project Verification routes
    Route::get('api/projects/{project}/verify-setup', [App\Http\Controllers\ProjectVerificationController::class, 'verify'])->name('projects.verify-setup');

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

    // Balance routes
    Route::get('api/balance', [App\Http\Controllers\BalanceController::class, 'index'])->name('balance.index');
    Route::get('api/balance/cost-estimates', [App\Http\Controllers\BalanceController::class, 'costEstimates'])->name('balance.cost-estimates');
    Route::get('api/balance/can-afford', [App\Http\Controllers\BalanceController::class, 'canAfford'])->name('balance.can-afford');
    Route::post('api/balance/add-credits', [App\Http\Controllers\BalanceController::class, 'addCredits'])->name('balance.add-credits');
    Route::post('comments/{comment}/toggle-resolved', [App\Http\Controllers\CommentController::class, 'toggleResolved'])->name('comments.toggle-resolved');

    // Docker System Management routes
    Route::prefix('api/docker')->name('docker.')->group(function () {
        Route::get('info', [App\Http\Controllers\DockerSystemController::class, 'info'])->name('info');
        Route::get('containers', [App\Http\Controllers\DockerSystemController::class, 'getRunningContainers'])->name('containers');
        Route::post('cleanup', [App\Http\Controllers\DockerSystemController::class, 'cleanup'])->name('cleanup');
    });

    // Project Deployment routes
    Route::prefix('api/projects/{project}')->name('projects.')->group(function () {
        Route::post('deploy', [App\Http\Controllers\ProjectDeploymentController::class, 'deploy'])->name('deploy');
        Route::get('docker/preview', [App\Http\Controllers\ProjectDeploymentController::class, 'getPreviewUrl'])->name('docker.preview');
    });

    // Container Management routes
    Route::prefix('api/projects/{project}')->name('projects.')->group(function () {
        Route::post('docker/start', [App\Http\Controllers\ContainerController::class, 'start'])->name('docker.start');
        Route::get('docker/status', [App\Http\Controllers\ContainerController::class, 'status'])->name('docker.status');
        Route::get('docker/logs', [App\Http\Controllers\ContainerController::class, 'logs'])->name('docker.logs');
        Route::post('docker/stop', [App\Http\Controllers\ContainerController::class, 'stop'])->name('docker.stop');
        Route::post('docker/restart', [App\Http\Controllers\ContainerController::class, 'restart'])->name('docker.restart');
    });

    // Direct Container Management routes (for direct container access - when you have container ID)
    Route::prefix('api/containers/{container}')->name('containers.')->group(function () {
        Route::post('stop', [App\Http\Controllers\ContainerController::class, 'stop'])->name('stop');
        Route::post('restart', [App\Http\Controllers\ContainerController::class, 'restart'])->name('restart');
        Route::get('status', [App\Http\Controllers\ContainerController::class, 'status'])->name('status');
        Route::get('logs', [App\Http\Controllers\ContainerController::class, 'logs'])->name('logs');
    });

    // Subdomain management routes
    Route::get('api/subdomain/check', [App\Http\Controllers\SubdomainController::class, 'checkAvailability'])->name('subdomain.check');
    Route::post('projects/{project}/subdomain', [App\Http\Controllers\SubdomainController::class, 'updateSubdomain'])->name('projects.subdomain');
    Route::post('projects/{project}/custom-domain', [App\Http\Controllers\SubdomainController::class, 'configureCustomDomain'])->name('projects.custom-domain');
    Route::delete('projects/{project}/custom-domain', [App\Http\Controllers\SubdomainController::class, 'removeCustomDomain'])->name('projects.remove-custom-domain');
    Route::get('api/cloudflare/test', [App\Http\Controllers\SubdomainController::class, 'testCloudflareConnection'])->name('cloudflare.test');

    // Chat routes
    Route::prefix('api/projects/{project}')->name('projects.')->group(function () {
        Route::get('chat/status', [App\Http\Controllers\ChatController::class, 'getStatus'])->name('chat.status');
        Route::get('chat/conversation', [App\Http\Controllers\ChatController::class, 'getConversation'])->name('chat.conversation');
        Route::get('chat/conversations', [App\Http\Controllers\ChatController::class, 'getAllConversations'])->name('chat.conversations');
        Route::post('chat/message', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.message');
        Route::post('chat/create-session', [App\Http\Controllers\ChatController::class, 'createSession'])->name('chat.create-session');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
