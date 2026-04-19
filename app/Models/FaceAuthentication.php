<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceAuthentication extends Model
{
    protected $fillable = [
        'user_id',
        'face_vector',
        'face_hash',
        'model_version',
        'revoked_at',
        'last_used_at'
    ];

    protected $casts = [
        'revoked_at' => 'datetime'
    ];
}
