<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'group_code',
        'created_by'
    ];

    /**
     * Generate a unique group code
     */
    public static function generateUniqueCode(): string
    {
        do {
            // Generate 8 character alphanumeric code (uppercase)
            $code = strtoupper(Str::random(8));
            // Ensure it contains both letters and numbers for better uniqueness
            if (!preg_match('/[A-Z]/', $code) || !preg_match('/[0-9]/', $code)) {
                continue;
            }
        } while (self::where('group_code', $code)->exists());

        return $code;
    }

    /**
     * Get the user who created the group
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all members of the group
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get the leader of the group
     */
    public function leader()
    {
        return $this->members()->where('role', 'leader')->first();
    }

    /**
     * Get all transactions for the group
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(GroupTransaction::class);
    }

    /**
     * Get active members
     */
    public function activeMembers()
    {
        return $this->members()->where('status', 'active');
    }
}
