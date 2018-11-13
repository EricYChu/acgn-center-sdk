<?php

namespace Acgn\Center\Models;

use Carbon\Carbon;

/**
 * @property string $uuid
 * @property Carbon $expired_at
 */
class Verification extends Model
{
    protected $casts = [
        'expired_at' => 'datetime'
    ];
}