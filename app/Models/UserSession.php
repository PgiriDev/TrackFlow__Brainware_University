<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'os',
        'browser',
        'device',
        'ip_address',
        'device_fingerprint',
        'is_trusted',
        'requires_2fa',
        'trusted_at',
        'last_activity',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'requires_2fa' => 'boolean',
        'trusted_at' => 'datetime',
        'last_activity' => 'datetime',
        'login_time' => 'datetime',
    ];
}
