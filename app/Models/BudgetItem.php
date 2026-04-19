<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'category_id',
        'limit_amount',
        'spent_amount',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getRemainingAttribute(): float
    {
        return $this->limit_amount - $this->spent_amount;
    }

    public function getPercentageUsedAttribute(): float
    {
        if ($this->limit_amount == 0) {
            return 0;
        }
        return ($this->spent_amount / $this->limit_amount) * 100;
    }

    public function isOverBudget(): bool
    {
        return $this->spent_amount > $this->limit_amount;
    }

    public function updateSpentAmount(): void
    {
        $budget = $this->budget;

        $spent = Transaction::where('user_id', $budget->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'debit')
            ->whereYear('date', $budget->year)
            ->whereMonth('date', $budget->month)
            ->excludeDuplicates()
            ->sum('amount');

        $this->update(['spent_amount' => $spent]);
    }
}
