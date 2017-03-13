<?php

return [
    /**
     * inclusive:
     * if true than a bigger role includes all lower values
     * else all routes has to be the exact identifier and there will be created a role_x_user table to enable n:n connection
     */
    'inclusive' => true,

    'model' => PopCode\UserRole\Models\Role::class,

    // you can enable default routes here
    // keep in mind this routes are not contain any guards
    'register_default_routes' => false,
];
