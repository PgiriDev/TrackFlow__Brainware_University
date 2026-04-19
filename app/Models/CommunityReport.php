<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityReport extends Model
{
    protected $table = 'community_reports';

    protected $fillable = [
        'reporter_id',
        'post_id',
        'comment_id',
        'reason',
        'description',
        'status',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_DISMISSED = 'dismissed';

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
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
