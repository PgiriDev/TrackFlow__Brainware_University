<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'month',
        'year',
        'total_limit',
        'carry_forward',
    ];

    protected $casts = [
        'total_limit' => 'decimal:2',
        'carry_forward' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->items->sum('spent_amount');
    }

    public function getRemainingAttribute(): float
    {
        return $this->total_limit - $this->getTotalSpentAttribute();
    }

    public function getPercentageUsedAttribute(): float
    {
        if ($this->total_limit == 0) {
            return 0;
        }
        return ($this->getTotalSpentAttribute() / $this->total_limit) * 100;
    }

    public function isOverBudget(): bool
    {
        return $this->getTotalSpentAttribute() > $this->total_limit;
    }

    public function getAlertLevel(): ?string
    {
        $percentage = $this->getPercentageUsedAttribute();

        if ($percentage >= 100) {
            return 'exceeded';
        } elseif ($percentage >= 75) {
            return 'warning';
        } elseif ($percentage >= 50) {
            return 'info';
        }

        return null;
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    public function scopeCurrent($query)
    {
        return $query->where('year', now()->year)
            ->where('month', now()->month);
    }
}
