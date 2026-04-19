<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityNotification extends Model
{
    protected $table = 'community_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'data',
        'is_read',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    const TYPE_NEW_COMMENT = 'new_comment';
    const TYPE_NEW_VOTE = 'new_vote';
    const TYPE_STATUS_CHANGE = 'status_change';
    const TYPE_MENTION = 'mention';
    const TYPE_ANNOUNCEMENT = 'announcement';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }

    public static function createNotification($userId, $type, $data)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'data' => $data,
            'is_read' => false,
        ]);
    }
}
