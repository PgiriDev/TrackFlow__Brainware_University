<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Eager load relationships by default for common queries
     */
    protected $with = [];

    /**
     * Relationships that should be touched when this model is updated
     */
    protected $touches = [];

    protected $fillable = [
        'user_id',
        'category_id',
        'budget_id',
        'budget_item_id',
        'date',
        'description',
        'merchant',
        'amount',
        'entered_amount',
        'entered_currency',
        'currency',
        'type',
        'status',
        'payment_method',
        'notes',
        'is_recurring',
        'is_duplicate',
        'duplicate_of_id',
        'provider_tx_id',
        'transaction_hash',
        'raw_data',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
        'is_duplicate' => 'boolean',
        'raw_data' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            // Auto-generate transaction hash if not set
            if (empty($transaction->transaction_hash)) {
                $transaction->transaction_hash = md5(
                    $transaction->user_id .
                    $transaction->date .
                    $transaction->amount .
                    $transaction->description .
                    time()
                );
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function duplicateOf(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'duplicate_of_id');
    }

    public function duplicates()
    {
        return $this->hasMany(Transaction::class, 'duplicate_of_id');
    }

    public function generateHash(): string
    {
        $normalizedMerchant = strtolower(preg_replace('/[^a-z0-9]/', '', $this->merchant ?? ''));
        $normalizedDesc = strtolower(preg_replace('/[^a-z0-9]/', '', $this->description ?? ''));

        return hash('sha256', implode('|', [
            $this->user_id,
            $this->date->format('Y-m-d'),
            abs((float) $this->amount),
            $normalizedMerchant,
            substr($normalizedDesc, 0, 50),
        ]));
    }

    public function scopeDebits($query)
    {
        return $query->where('type', 'debit');
    }

    public function scopeCredits($query)
    {
        return $query->where('type', 'credit');
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeUncategorized($query)
    {
        return $query->whereNull('category_id');
    }

    public function scopeExcludeDuplicates($query)
    {
        return $query->where('is_duplicate', false);
    }

    /**
     * Scope for common report queries with eager loading
     */
    public function scopeWithReportData($query)
    {
        return $query->with(['category:id,name,icon,color,type']);
    }

    /**
     * Scope for dashboard queries with minimal data
     */
    public function scopeForDashboard($query)
    {
        return $query->select(['id', 'user_id', 'category_id', 'date', 'description', 'amount', 'type', 'status'])
            ->with(['category:id,name,icon,color']);
    }

    /**
     * Get category name efficiently using cached categories
     */
    public function getCategoryNameAttribute(): string
    {
        if ($this->category_id && app()->bound('user.categories')) {
            $categories = app('user.categories');
            return $categories[$this->category_id]->name ?? 'Uncategorized';
        }
        return $this->category?->name ?? 'Uncategorized';
    }

    public function isExpense(): bool
    {
        return $this->type === 'debit';
    }

    public function isIncome(): bool
    {
        return $this->type === 'credit';
    }
}
