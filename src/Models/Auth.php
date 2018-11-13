<?php

namespace Acgn\Center\Models;

use Carbon\Carbon;

/**
 * @property string $token
 * @property Carbon $expired_at
 * @property Carbon $renewal_expired_at
 */
class Auth extends Model
{
    protected $casts = [
        'expired_at' => 'datetime',
        'renewal_expired_at' => 'datetime',
        'user' => User::class,
    ];
}