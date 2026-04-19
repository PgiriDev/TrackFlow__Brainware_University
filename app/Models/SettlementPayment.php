<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettlementPayment extends Model
{
    protected $fillable = [
        'group_id',
        'payer_member_id',
        'receiver_member_id',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'proof_screenshot',
        'payment_note',
        'upi_id_used',
        'paid_at',
        'verified_by',
        'verified_at',
        'rejection_reason'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFICATION_PENDING = 'verification_pending';
    const STATUS_AUTO_VERIFIED = 'auto_verified';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the group this payment belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the payer member
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class, 'payer_member_id');
    }

    /**
     * Get the receiver member
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class, 'receiver_member_id');
    }

    /**
     * Get the user who verified this payment
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is awaiting verification
     */
    public function isAwaitingVerification(): bool
    {
        return in_array($this->status, [self::STATUS_VERIFICATION_PENDING, self::STATUS_AUTO_VERIFIED]);
    }

    /**
     * Check if payment is confirmed
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if payment was rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_VERIFICATION_PENDING => 'blue',
            self::STATUS_AUTO_VERIFIED => 'indigo',
            self::STATUS_PAID => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_VERIFICATION_PENDING => 'Awaiting Verification',
            self::STATUS_AUTO_VERIFIED => 'Auto Verified',
            self::STATUS_PAID => 'Paid',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for payments awaiting verification
     */
    public function scopeAwaitingVerification($query)
    {
        return $query->whereIn('status', [self::STATUS_VERIFICATION_PENDING, self::STATUS_AUTO_VERIFIED]);
    }

    /**
     * Scope for confirmed payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Check for duplicate transaction ID
     */
    public static function isDuplicateTransaction(string $transactionId, ?int $excludeId = null): bool
    {
        $query = self::where('transaction_id', $transactionId)
            ->whereNotNull('transaction_id')
            ->where('transaction_id', '!=', '');
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * Auto verify payment based on rules
     */
    public function autoVerify(): bool
    {
        // Check if already verified
        if ($this->status === self::STATUS_PAID) {
            return true;
        }

        // Rules for auto-verification
        $rules = [
            'has_transaction_id' => !empty($this->transaction_id),
            'has_proof' => !empty($this->proof_screenshot),
            'amount_valid' => $this->amount > 0,
            'not_duplicate' => !self::isDuplicateTransaction($this->transaction_id, $this->id),
            'status_valid' => in_array($this->status, [self::STATUS_PENDING, self::STATUS_VERIFICATION_PENDING])
        ];

        // All rules must pass for auto-verification
        if (collect($rules)->every(fn($passed) => $passed === true)) {
            $this->update([
                'status' => self::STATUS_AUTO_VERIFIED
            ]);
            return true;
        }

        return false;
    }
}
