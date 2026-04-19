<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'scheduled_date',
        'description',
        'merchant',
        'amount',
        'currency',
        'type',
        'notes',
        'status',
        'confirmation_sent',
        'reminder_sent',
        'confirmation_sent_at',
        'reminder_sent_at',
        'transaction_id',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'amount' => 'decimal:2',
        'confirmation_sent' => 'boolean',
        'reminder_sent' => 'boolean',
        'confirmation_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the scheduled transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the scheduled transaction.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the actual transaction if executed.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Scope for pending scheduled transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for transactions due today.
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('scheduled_date', today());
    }

    /**
     * Scope for transactions that need reminders.
     */
    public function scopeNeedsReminder($query)
    {
        return $query->where('status', 'pending')
            ->where('reminder_sent', false)
            ->whereDate('scheduled_date', today());
    }

    /**
     * Check if the scheduled transaction is due today.
     */
    public function isDueToday(): bool
    {
        return $this->scheduled_date->isToday();
    }

    /**
     * Check if the scheduled transaction is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->scheduled_date->isPast() && $this->status === 'pending';
    }

    /**
     * Mark as completed and link to actual transaction.
     */
    public function markCompleted(int $transactionId): void
    {
        $this->update([
            'status' => 'completed',
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Mark as cancelled.
     */
    public function markCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
