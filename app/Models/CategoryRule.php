<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'merchant_pattern',
        'description_pattern',
        'is_regex',
        'priority',
        'is_system',
    ];

    protected $casts = [
        'is_regex' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function matches(string $merchant, ?string $description = null): bool
    {
        $merchantMatch = $this->matchesPattern($merchant, $this->merchant_pattern);

        if (!$this->description_pattern) {
            return $merchantMatch;
        }

        $descriptionMatch = $description
            ? $this->matchesPattern($description, $this->description_pattern)
            : false;

        return $merchantMatch && $descriptionMatch;
    }

    protected function matchesPattern(string $value, string $pattern): bool
    {
        if ($this->is_regex) {
            return preg_match($pattern, $value) === 1;
        }

        return stripos($value, $pattern) !== false;
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere('is_system', true);
        })->orderBy('priority', 'desc');
    }
}
