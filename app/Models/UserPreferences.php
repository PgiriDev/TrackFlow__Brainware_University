<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreferences extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_format',
        'first_day_of_week',
        'default_export_format',
        'sync_frequency_hours',
        'email_notifications',
        'push_notifications',
        'budget_alerts',
        'large_transaction_alerts',
        'large_transaction_threshold',
    ];

    protected $casts = [
        'sync_frequency_hours' => 'integer',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'budget_alerts' => 'boolean',
        'large_transaction_alerts' => 'boolean',
        'large_transaction_threshold' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function createDefault(int $userId): self
    {
        return self::create([
            'user_id' => $userId,
            'date_format' => 'Y-m-d',
            'first_day_of_week' => 'monday',
            'default_export_format' => 'pdf',
            'sync_frequency_hours' => 6,
            'email_notifications' => true,
            'push_notifications' => true,
            'budget_alerts' => true,
            'large_transaction_alerts' => true,
            'large_transaction_threshold' => 1000,
        ]);
    }
}
