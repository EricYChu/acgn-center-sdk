<?php

namespace Acgn\Center\Models;

use Carbon\Carbon;

/**
 * @property int $id
 * @property string $username
 * @property string $country_code
 * @property string $phone_number
 * @property string $phone
 * @property string $email
 * @property int $version
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $can_update
 * @property bool $can_delete
 */
class User extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function __construct(\stdClass $input)
    {
        parent::__construct($input);
        $this->phone = $this->country_code.$this->phone_number;
    }
}