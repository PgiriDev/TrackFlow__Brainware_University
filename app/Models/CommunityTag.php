<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityTag extends Model
{
    protected $table = 'community_tags';

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    public function posts()
    {
        return $this->belongsToMany(CommunityPost::class, 'community_post_tags', 'tag_id', 'post_id');
    }
}
