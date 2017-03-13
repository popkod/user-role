<?php

namespace PopCode\UserRole\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Role extends Eloquent
{
    public $table = 'roles';

    protected $casts = [
        'id' => 'integer',
    ];
}
