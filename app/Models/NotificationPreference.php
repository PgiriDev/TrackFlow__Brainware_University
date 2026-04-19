<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'budget_alerts',
        'goal_updates',
        'group_activities',
        'transaction_alerts',
        'bill_reminders',
        'feature_updates',
        'email_notifications',
        'push_notifications',
        'budget_threshold_percentage',
    ];

    protected $casts = [
        'budget_alerts' => 'boolean',
        'goal_updates' => 'boolean',
        'group_activities' => 'boolean',
        'transaction_alerts' => 'boolean',
        'bill_reminders' => 'boolean',
        'feature_updates' => 'boolean',
        'email_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'budget_threshold_percentage' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
