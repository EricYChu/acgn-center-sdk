<?php

namespace Acgn\Center\Models;

use Carbon\Carbon;

/**
 * @property string $token
 * @property Carbon $expired_at
 */
class IntermediateAuth extends Model
{
    protected $casts = [
        'expired_at' => 'datetime',
    ];
}