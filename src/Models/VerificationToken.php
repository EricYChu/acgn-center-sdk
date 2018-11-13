<?php

namespace Acgn\Center\Models;

use Carbon\Carbon;

/**
 * @property string $uuid
 * @property string $token
 * @property Carbon $expired_at
 */
class VerificationToken extends Model
{
    protected $casts = [
        'expired_at' => 'datetime'
    ];
}