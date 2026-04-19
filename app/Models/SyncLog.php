<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_account_id',
        'action',
        'status',
        'message',
        'transactions_fetched',
        'error_details',
    ];

    protected $casts = [
        'error_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public static function logSync(
        int $userId,
        ?int $bankAccountId,
        string $action,
        string $status,
        ?string $message = null,
        int $transactionsFetched = 0,
        ?array $errorDetails = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'bank_account_id' => $bankAccountId,
            'action' => $action,
            'status' => $status,
            'message' => $message,
            'transactions_fetched' => $transactionsFetched,
            'error_details' => $errorDetails,
        ]);
    }
}
