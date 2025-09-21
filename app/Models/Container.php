<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Container extends Model
{
    /** @use HasFactory<\Database\Factories\ContainerFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'container_id',
        'name',
        'status',
        'port',
        'url',
        'environment',
        'logs',
        'started_at',
        'stopped_at',
    ];

    protected $casts = [
        'environment' => 'array',
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function isStopped(): bool
    {
        return $this->status === 'stopped';
    }

    public function hasError(): bool
    {
        return $this->status === 'error';
    }
}
