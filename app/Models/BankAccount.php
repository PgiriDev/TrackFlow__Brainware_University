<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number_masked',
        'account_type',
        'balance',
        'currency',
        'provider',
        'provider_account_id',
        'status',
        'last_synced_at',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function accountToken(): HasOne
    {
        return $this->hasOne(AccountToken::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(SyncLog::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function needsSync(): bool
    {
        if (!$this->last_synced_at) {
            return true;
        }

        $syncFrequency = $this->user->preferences->sync_frequency_hours ?? 6;
        return $this->last_synced_at->addHours($syncFrequency)->isPast();
    }
}
