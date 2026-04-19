<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'target_amount',
        'current_amount',
        'currency',
        'target_date',
        'description',
        'icon',
        'color',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    /**
     * Get the user that owns the goal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the progress percentage.
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) {
            return 0;
        }

        return min(($this->current_amount / $this->target_amount) * 100, 100);
    }

    /**
     * Get the remaining amount.
     */
    public function getRemainingAmountAttribute()
    {
        return max($this->target_amount - $this->current_amount, 0);
    }

    /**
     * Check if the goal is completed.
     */
    public function isCompleted()
    {
        return $this->current_amount >= $this->target_amount;
    }
}
