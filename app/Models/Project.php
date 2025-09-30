<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'slug',
        'status',
        'settings',
        'generated_code',
        'preview_url',
        'is_public',
        'last_built_at',
        'subdomain',
        'custom_domain',
        'dns_configured',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_built_at' => 'datetime',
        'dns_configured' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
    }

    public function prompts(): HasMany
    {
        return $this->hasMany(Prompt::class);
    }

    public function chatConversations(): HasMany
    {
        return $this->hasMany(ChatConversation::class);
    }

    public function getActiveChatConversation(): ?ChatConversation
    {
        return $this->chatConversations()->latest('last_activity')->first();
    }

    public function getActiveContainer(): ?Container
    {
        return $this->containers()->where('status', 'running')->first();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->name);
            }
            
            if (empty($project->subdomain)) {
                $project->subdomain = $project->generateUniqueSubdomain();
            }
        });

        static::deleting(function (Project $project) {
            // Clean up Docker resources when project is deleted
            try {
                $dockerService = app(\App\Services\DockerService::class);
                $dockerService->cleanupProject($project);
            } catch (\Exception $e) {
                // Log error but don't prevent deletion
                \Log::warning("Failed to cleanup Docker resources during project deletion", [
                    'project_id' => $project->id,
                    'error' => $e->getMessage()
                ]);
            }

            // Clean up chat conversations when project is deleted
            try {
                $project->chatConversations()->delete();
                \Log::info("Cleaned up chat conversations for project", [
                    'project_id' => $project->id,
                ]);
            } catch (\Exception $e) {
                \Log::warning("Failed to cleanup chat conversations during project deletion", [
                    'project_id' => $project->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    /**
     * Generate a unique subdomain for the project
     */
    public function generateUniqueSubdomain(): string
    {
        $baseSubdomain = Str::slug($this->name);
        $subdomain = $baseSubdomain;
        $counter = 1;

        // Ensure subdomain is unique
        while (static::where('subdomain', $subdomain)->exists()) {
            $subdomain = $baseSubdomain . '-' . $counter;
            $counter++;
        }

        return $subdomain;
    }

    /**
     * Get the full subdomain URL
     */
    public function getSubdomainUrl(): string
    {
        $baseDomain = config('app.domain', 'r3kt.dev');
        return "https://{$this->subdomain}.{$baseDomain}";
    }

    /**
     * Get the project URL (subdomain or custom domain)
     */
    public function getProjectUrl(): string
    {
        if ($this->custom_domain) {
            return "https://{$this->custom_domain}";
        }

        return $this->getSubdomainUrl();
    }

    /**
     * Check if subdomain is available
     */
    public static function isSubdomainAvailable(string $subdomain): bool
    {
        return !static::where('subdomain', $subdomain)->exists();
    }

    /**
     * Validate subdomain format
     */
    public static function isValidSubdomain(string $subdomain): bool
    {
        // Subdomain must be 3-63 characters, alphanumeric and hyphens only
        return preg_match('/^[a-z0-9]([a-z0-9-]{1,61}[a-z0-9])?$/', $subdomain) === 1;
    }
}
