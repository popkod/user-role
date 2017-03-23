<?php

namespace PopCode\UserRole\Models;

class UserXRole
{
    protected $table = 'user_x_role';

    protected $fillable = [
        'user_id',
        'role_id',
    ];
}
