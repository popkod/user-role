<?php

namespace PopCode\UserRole\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class RouteXRole extends \Eloquent
{
    public $table = 'route_x_role';

    public $timestamps = false;

    protected $casts = [
        'id'      => 'integer',
        'role_id' => 'integer',
    ];

    protected $fillable = [
        'role_id',
        'route',
        'method',
    ];

    public function role() {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}
