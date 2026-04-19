<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommunityPost extends Model
{
    use SoftDeletes;

    protected $table = 'community_posts';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'type',
        'status',
        'is_anonymous',
        'is_pinned',
        'comment_count',
        'vote_score',
        'view_count',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_pinned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Post types
    const TYPE_FEEDBACK = 'feedback';
    const TYPE_SUGGESTION = 'suggestion';
    const TYPE_OPINION = 'opinion';
    const TYPE_BUG = 'bug';
    const TYPE_ANNOUNCEMENT = 'announcement';

    // Status types
    const STATUS_OPEN = 'open';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_IMPLEMENTED = 'implemented';
    const STATUS_REJECTED = 'rejected';

    public static function getTypes(): array
    {
        return [
            self::TYPE_FEEDBACK => ['label' => 'Feedback', 'icon' => 'fa-comment-dots', 'color' => 'blue'],
            self::TYPE_SUGGESTION => ['label' => 'Suggestion', 'icon' => 'fa-lightbulb', 'color' => 'yellow'],
            self::TYPE_OPINION => ['label' => 'Opinion', 'icon' => 'fa-comments', 'color' => 'purple'],
            self::TYPE_BUG => ['label' => 'Bug Report', 'icon' => 'fa-bug', 'color' => 'red'],
            self::TYPE_ANNOUNCEMENT => ['label' => 'Announcement', 'icon' => 'fa-bullhorn', 'color' => 'green'],
        ];
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_OPEN => ['label' => 'Open', 'icon' => 'fa-circle', 'color' => 'gray'],
            self::STATUS_UNDER_REVIEW => ['label' => 'Under Review', 'icon' => 'fa-search', 'color' => 'blue'],
            self::STATUS_PLANNED => ['label' => 'Planned', 'icon' => 'fa-calendar', 'color' => 'purple'],
            self::STATUS_IN_PROGRESS => ['label' => 'In Progress', 'icon' => 'fa-spinner', 'color' => 'yellow'],
            self::STATUS_IMPLEMENTED => ['label' => 'Implemented', 'icon' => 'fa-check-circle', 'color' => 'green'],
            self::STATUS_REJECTED => ['label' => 'Rejected', 'icon' => 'fa-times-circle', 'color' => 'red'],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'post_id');
    }

    public function votes()
    {
        return $this->hasMany(CommunityVote::class, 'post_id');
    }

    public function reactions()
    {
        return $this->hasMany(CommunityReaction::class, 'post_id');
    }

    public function tags()
    {
        return $this->belongsToMany(CommunityTag::class, 'community_post_tags', 'post_id', 'tag_id');
    }

    public function reports()
    {
        return $this->hasMany(CommunityReport::class, 'post_id');
    }

    public function poll()
    {
        return $this->hasOne(CommunityPoll::class, 'post_id');
    }

    public function hasPoll()
    {
        return $this->poll()->exists();
    }

    public function getTypeInfoAttribute()
    {
        return self::getTypes()[$this->type] ?? null;
    }

    public function getStatusInfoAttribute()
    {
        return self::getStatuses()[$this->status] ?? null;
    }

    public function scopeTrending($query)
    {
        return $query->orderByRaw('(vote_score + comment_count * 2 + view_count / 10) DESC')
            ->orderBy('created_at', 'desc');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeMostVoted($query)
    {
        return $query->orderBy('vote_score', 'desc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function updateVoteScore()
    {
        $upvotes = $this->votes()->where('vote', 'up')->count();
        $downvotes = $this->votes()->where('vote', 'down')->count();
        $this->vote_score = $upvotes - $downvotes;
        $this->save();
    }

    public function updateCommentCount()
    {
        $this->comment_count = $this->comments()->count();
        $this->save();
    }
}
