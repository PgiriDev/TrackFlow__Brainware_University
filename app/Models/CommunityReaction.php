<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityReaction extends Model
{
    protected $table = 'community_reactions';

    protected $fillable = [
        'user_id',
        'post_id',
        'reaction',
    ];

    const REACTION_LOVE = 'love';
    const REACTION_USEFUL = 'useful';
    const REACTION_MINDBLOWN = 'mindblown';
    const REACTION_CONFUSED = 'confused';

    public static function getReactions(): array
    {
        return [
            self::REACTION_LOVE => ['label' => 'Love', 'emoji' => '❤️', 'icon' => 'fa-heart', 'color' => 'red'],
            self::REACTION_USEFUL => ['label' => 'Useful', 'emoji' => '🔥', 'icon' => 'fa-fire', 'color' => 'orange'],
            self::REACTION_MINDBLOWN => ['label' => 'Mind-blowing', 'emoji' => '🤯', 'icon' => 'fa-brain', 'color' => 'purple'],
            self::REACTION_CONFUSED => ['label' => 'Needs work', 'emoji' => '😕', 'icon' => 'fa-meh', 'color' => 'yellow'],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'post_id');
    }
}
