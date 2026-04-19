<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserUpi extends Model
{
    use HasFactory;

    protected $table = 'user_upis';

    protected $fillable = [
        'user_id',
        'name',
        'upi_id',
        'qr_code_path',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the UPI.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the QR code URL.
     */
    public function getQrCodeUrlAttribute(): ?string
    {
        if (!$this->qr_code_path) {
            return null;
        }

        return asset('storage/' . $this->qr_code_path);
    }

    /**
     * Scope to get active UPIs only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get primary UPI.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
