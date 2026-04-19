<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPoll extends Model
{
    protected $table = 'community_polls';

    protected $fillable = [
        'post_id',
        'question',
        'multiple_choice',
        'ends_at',
    ];

    protected $casts = [
        'multiple_choice' => 'boolean',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function options()
    {
        return $this->hasMany(CommunityPollOption::class, 'poll_id');
    }

    public function votes()
    {
        return $this->hasMany(CommunityPollVote::class, 'poll_id');
    }

    public function getTotalVotesAttribute()
    {
        return $this->votes()->count();
    }

    public function getUniqueVotersAttribute()
    {
        return $this->votes()->distinct('user_id')->count('user_id');
    }

    public function hasUserVoted($userId)
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function getUserVotes($userId)
    {
        return $this->votes()->where('user_id', $userId)->pluck('option_id')->toArray();
    }

    public function isExpired()
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function isActive()
    {
        return !$this->isExpired();
    }
}
