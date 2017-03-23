<?php

namespace PopCode\UserRole\Models;

use PopCode\UserCrud\Models\User as BaseUser;

class User extends BaseUser
{
    protected $roleCahce;

    public function roles() {
        return $this->belongsToMany(Role::class, 'user_x_role');
    }

    public function userXRole() {
        return $this->hasMany(\Config::get('popcode-userrole.user_x_role_model'), 'user_id', 'id');
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

    /**
     * @param int $role
     *
     * @return bool
     */
    public function hasAtLeastRole($role) {
        if (is_null($this->roleCahce)) {
            $this->getRoles();
        }
        foreach ($this->roleCahce as $ownRole) {
            if ($ownRole->id >= $role) {
                return true;
            }
        }
        return false;
    }
}
