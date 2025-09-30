<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'chat_id',
        'messages',
        'raw_conversation',
        'last_activity',
        'total_cost',
        'total_tokens',
        'input_tokens',
        'output_tokens',
        'cost_currency',
    ];

    protected $casts = [
        'messages' => 'array',
        'last_activity' => 'datetime',
        'total_cost' => 'decimal:6',
    ];

    /**
     * Get the project that owns the chat conversation
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Add a message to the conversation
     */
    public function addMessage(string $role, string $content, array $costInfo = null): void
    {
        $messages = $this->messages ?? [];
        
        $messageData = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toISOString(),
        ];
        
        // Add cost information if provided (for AI messages)
        if ($costInfo && $role === 'ai') {
            $messageData['cost_info'] = $costInfo;
        }
        
        $messages[] = $messageData;
        
        $this->update([
            'messages' => $messages,
            'last_activity' => now(),
        ]);
    }

    /**
     * Get the latest messages from the conversation
     */
    public function getLatestMessages(int $limit = 50): array
    {
        $messages = $this->messages ?? [];
        return array_slice($messages, -$limit);
    }

    /**
     * Update the raw conversation data
     */
    public function updateRawConversation(string $rawConversation): void
    {
        $this->update([
            'raw_conversation' => $rawConversation,
            'last_activity' => now(),
        ]);
    }

    /**
     * Add cost information to the conversation
     */
    public function addCost(float $cost, int $inputTokens, int $outputTokens, string $currency = 'USD'): void
    {
        $this->update([
            'total_cost' => $this->total_cost + $cost,
            'total_tokens' => $this->total_tokens + $inputTokens + $outputTokens,
            'input_tokens' => $this->input_tokens + $inputTokens,
            'output_tokens' => $this->output_tokens + $outputTokens,
            'cost_currency' => $currency,
            'last_activity' => now(),
        ]);
    }

    /**
     * Get formatted cost display
     */
    public function getFormattedCost(): string
    {
        return number_format($this->total_cost, 6) . ' ' . $this->cost_currency;
    }

    /**
     * Get cost per token
     */
    public function getCostPerToken(): float
    {
        if ($this->total_tokens === 0) {
            return 0;
        }
        
        return $this->total_cost / $this->total_tokens;
    }
}