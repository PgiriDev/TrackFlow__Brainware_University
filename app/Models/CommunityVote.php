<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityVote extends Model
{
    protected $table = 'community_votes';

    protected $fillable = [
        'user_id',
        'post_id',
        'comment_id',
        'vote',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function comment()
    {
        return $this->belongsTo(CommunityComment::class, 'comment_id');
    }
}
