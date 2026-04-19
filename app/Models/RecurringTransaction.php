<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'merchant',
        'amount',
        'currency',
        'type',
        'frequency',
        'start_date',
        'end_date',
        'next_occurrence',
        'is_active',
        'auto_create',
        'last_created_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_occurrence' => 'date',
        'is_active' => 'boolean',
        'auto_create' => 'boolean',
        'last_created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if this recurring transaction is overdue
     */
    public function isOverdue(): bool
    {
        return $this->next_occurrence < now()->startOfDay();
    }

    /**
     * Get days until next occurrence
     */
    public function daysUntilDue(): int
    {
        return now()->startOfDay()->diffInDays($this->next_occurrence, false);
    }
}
