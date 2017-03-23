<?php

namespace PopCode\UserRole\Controllers;

use Illuminate\Routing\Controller as BaseController;
use PopCode\UserRole\Models\Role;

class RoleController extends BaseController
{
    public function index() {
        $roles = Role::all();

        return response()->json($roles);
    }
}
