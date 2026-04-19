<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'color',
        'icon',
        'order',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Clear category cache when model is saved/deleted
     */
    protected static function booted()
    {
        static::saved(function ($category) {
            if ($category->user_id) {
                Cache::forget("user_categories:{$category->user_id}");
            }
        });

        static::deleted(function ($category) {
            if ($category->user_id) {
                Cache::forget("user_categories:{$category->user_id}");
            }
        });
    }

    /**
     * Get all categories for a user (cached)
     */
    public static function getCachedForUser($userId)
    {
        return Cache::remember("user_categories:{$userId}", 300, function () use ($userId) {
            return static::where('user_id', $userId)
                ->orWhereNull('user_id')
                ->orderBy('order')
                ->get()
                ->keyBy('id');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function rules()
    {
        return $this->hasMany(CategoryRule::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereNull('user_id'); // System categories
        });
    }

    public function scopeSystemCategories($query)
    {
        return $query->whereNull('user_id')->where('is_system', true);
    }

    public function scopeUserCategories($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
