<?php

namespace PopCode\UserRole\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Role extends Eloquent
{
    public $table = 'roles';

    protected $casts = [
        'id' => 'integer',
    ];

    public function users() {
        return $this->belongsToMany(
            \Config::get('popcode-usercrud.model'),
            'user_x_role',
            'role_id',
            'user_id'
        );
    }

    public function routes() {
        return $this->hasMany(RouteXRole::class, 'role_id', 'id');
    }
}
