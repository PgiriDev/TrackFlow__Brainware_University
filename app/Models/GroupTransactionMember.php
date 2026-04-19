<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupTransactionMember extends Model
{
    protected $fillable = [
        'transaction_id',
        'member_id',
        'contributed_amount',
        'final_share_amount',
        'participated'
    ];

    protected $casts = [
        'contributed_amount' => 'decimal:2',
        'final_share_amount' => 'decimal:2',
        'participated' => 'boolean'
    ];

    /**
     * Get the transaction
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(GroupTransaction::class, 'transaction_id');
    }

    /**
     * Get the member
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class, 'member_id');
    }

    /**
     * Calculate balance (what member owes or is owed)
     */
    public function getBalanceAttribute(): float
    {
        return (float) ($this->contributed_amount - $this->final_share_amount);
    }
}
