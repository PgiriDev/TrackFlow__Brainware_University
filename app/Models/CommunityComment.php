<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityComment extends Model
{
    use SoftDeletes;

    protected $table = 'community_comments';

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
        'vote_score',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(CommunityComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(CommunityComment::class, 'parent_id');
    }

    public function votes()
    {
        return $this->hasMany(CommunityVote::class, 'comment_id');
    }

    public function reports()
    {
        return $this->hasMany(CommunityReport::class, 'comment_id');
    }

    public function updateVoteScore()
    {
        $upvotes = $this->votes()->where('vote', 'up')->count();
        $downvotes = $this->votes()->where('vote', 'down')->count();
        $this->vote_score = $upvotes - $downvotes;
        $this->save();
    }
}
