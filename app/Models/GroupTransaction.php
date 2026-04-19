<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupTransaction extends Model
{
    protected $fillable = [
        'group_id',
        'paid_by_member_id',
        'type',
        'category_id',
        'total_amount',
        'description',
        'date',
        'note',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    /**
     * Get the group this transaction belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the member who paid
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class, 'paid_by_member_id');
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get member contributions for this transaction
     */
    public function memberContributions(): HasMany
    {
        return $this->hasMany(GroupTransactionMember::class, 'transaction_id');
    }

    /**
     * Get members (alias for memberContributions)
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupTransactionMember::class, 'transaction_id');
    }

    /**
     * Check if transaction is expense
     */
    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    /**
     * Check if transaction is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
