<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMember extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'name',
        'email',
        'phone',
        'picture',
        'role',
        'status',
        'last_active_at',
        'is_settled',
        'settled_at'
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'is_settled' => 'boolean',
        'settled_at' => 'datetime'
    ];

    /**
     * Get the group this member belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user account (if linked)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get transactions paid by this member
     */
    public function paidTransactions(): HasMany
    {
        return $this->hasMany(GroupTransaction::class, 'paid_by_member_id');
    }

    /**
     * Get transaction participations
     */
    public function transactionParticipations(): HasMany
    {
        return $this->hasMany(GroupTransactionMember::class, 'member_id');
    }

    /**
     * Check if member is leader
     */
    public function isLeader(): bool
    {
        return $this->role === 'leader';
    }

    /**
     * Check if member is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if member is linked to a user account
     */
    public function isLinked(): bool
    {
        return $this->user_id !== null;
    }

    /**
     * Get the profile picture - prioritizes linked user's picture
     * This ensures the picture is always up-to-date when user changes their profile
     */
    public function getProfilePictureAttribute(): ?string
    {
        // If linked to a user, always use the user's current profile picture
        if ($this->user_id && $this->relationLoaded('user') && $this->user) {
            return $this->user->profile_picture;
        } elseif ($this->user_id) {
            // Lazy load user if not already loaded
            $user = $this->user;
            if ($user && $user->profile_picture) {
                return $user->profile_picture;
            }
        }

        // Fallback to member's own picture (for unlinked members)
        return $this->picture;
    }

    /**
     * Get the display name - prioritizes linked user's name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->user_id && $this->relationLoaded('user') && $this->user) {
            return $this->user->name;
        } elseif ($this->user_id) {
            $user = $this->user;
            if ($user) {
                return $user->name;
            }
        }

        return $this->name;
    }
}
