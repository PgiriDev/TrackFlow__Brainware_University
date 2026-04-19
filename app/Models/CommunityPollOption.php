<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPollOption extends Model
{
    protected $table = 'community_poll_options';

    protected $fillable = [
        'poll_id',
        'option_text',
        'votes_count',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function poll()
    {
        return $this->belongsTo(CommunityPoll::class, 'poll_id');
    }

    public function votes()
    {
        return $this->hasMany(CommunityPollVote::class, 'option_id');
    }

    public function getVotePercentageAttribute()
    {
        $totalVotes = $this->poll->unique_voters;
        if ($totalVotes === 0) {
            return 0;
        }
        return round(($this->votes_count / $totalVotes) * 100, 1);
    }

    public function updateVotesCount()
    {
        $this->votes_count = $this->votes()->count();
        $this->save();
    }
}
