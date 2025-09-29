<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance',
        'total_spent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:4',
            'total_spent' => 'decimal:4',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Check if user has sufficient balance for a given cost
     */
    public function hasSufficientBalance(float $cost): bool
    {
        return $this->balance >= $cost;
    }

    /**
     * Deduct amount from user's balance
     */
    public function deductBalance(float $amount): bool
    {
        if (! $this->hasSufficientBalance($amount)) {
            return false;
        }

        $this->balance -= $amount;
        $this->total_spent += $amount;
        $this->save();

        return true;
    }

    /**
     * Add amount to user's balance
     */
    public function addBalance(float $amount): void
    {
        $this->balance += $amount;
        $this->save();
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalance(): string
    {
        return '$'.number_format($this->balance, 4);
    }

    /**
     * Get formatted total spent
     */
    public function getFormattedTotalSpent(): string
    {
        return '$'.number_format($this->total_spent, 4);
    }
}
