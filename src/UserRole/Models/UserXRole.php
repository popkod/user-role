<?php

namespace PopCode\UserRole\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserXRole extends Eloquent
{
    protected $table = 'user_x_role';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    protected $casts = [
        'id'      => 'integer',
        'user_id' => 'integer',
        'role_id' => 'integer',
    ];
}
