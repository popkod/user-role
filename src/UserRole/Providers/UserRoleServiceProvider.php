<?php

namespace PopCode\UserRole\Providers;

use Illuminate\Support\ServiceProvider;
use PopCode\UserRole\Models\User;
use Config;
use Carbon\Carbon;

class UserRoleServiceProvider extends ServiceProvider
{
    protected $root;

    public function boot() {

        $root = __DIR__ . '/../../../';

    }
}
