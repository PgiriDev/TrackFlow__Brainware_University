<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityReputation extends Model
{
    protected $table = 'community_reputation';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'points',
        'level',
        'last_updated',
    ];

    protected $casts = [
        'last_updated' => 'datetime',
    ];

    const LEVEL_NEWBIE = 'Newbie';
    const LEVEL_CONTRIBUTOR = 'Contributor';
    const LEVEL_TOP_VOICE = 'Top Voice';
    const LEVEL_COMMUNITY_LEADER = 'Community Leader';

    public static function getLevels(): array
    {
        return [
            self::LEVEL_NEWBIE => ['min_points' => 0, 'icon' => 'fa-seedling', 'color' => 'gray'],
            self::LEVEL_CONTRIBUTOR => ['min_points' => 50, 'icon' => 'fa-star', 'color' => 'blue'],
            self::LEVEL_TOP_VOICE => ['min_points' => 200, 'icon' => 'fa-fire', 'color' => 'orange'],
            self::LEVEL_COMMUNITY_LEADER => ['min_points' => 500, 'icon' => 'fa-crown', 'color' => 'yellow'],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addPoints($points)
    {
        $this->points += $points;
        $this->updateLevel();
        $this->last_updated = now();
        $this->save();
    }

    public function updateLevel()
    {
        $levels = self::getLevels();
        $newLevel = self::LEVEL_NEWBIE;

        foreach ($levels as $level => $info) {
            if ($this->points >= $info['min_points']) {
                $newLevel = $level;
            }
        }

        $this->level = $newLevel;
    }

    public static function getOrCreate($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            ['points' => 0, 'level' => self::LEVEL_NEWBIE, 'last_updated' => now()]
        );
    }
}
