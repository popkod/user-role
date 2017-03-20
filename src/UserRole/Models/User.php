<?php

namespace PopCode\UserRole\Models;

use PopCode\UserCrud\Models\User as BaseUser;

class User extends BaseUser
{
    protected $roleCahce;

    public function roles() {
        return $this->belongsToMany(Role::class, 'user_x_role');
    }

    public function getRoles() {
        $this->roleCahce = $this->roles()->get();
        return $this->roleCahce;
    }

    public function hasRole($role) {
        if (is_null($this->roleCahce)) {
            $this->getRoles();
        }

        $key = is_numeric($role) ? 'id' : 'label';
        foreach ($this->roleCahce as $ownRole) {
            if ($ownRole->$key === $role) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyRole(array $roles) {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        return false;
    }
}
