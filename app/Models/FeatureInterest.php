<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeatureInterest extends Model
{
    protected $fillable = [
        'user_id',
        'feature_id',
        'note',
        'notify_on_release',
        'notified_at',
    ];

    protected $casts = [
        'notify_on_release' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ComingSoonFeature::class, 'feature_id');
    }

    public function markAsNotified(): void
    {
        $this->update(['notified_at' => now()]);
    }
}
