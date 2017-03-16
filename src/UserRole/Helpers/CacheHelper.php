<?php

namespace PopCode\UserRole\Helpers;

class CacheHelper
{
    protected $cachePath;
    protected $cacheFile = '/roles.php';

    public function __construct() {
        $this->cachePath = \Config::get('popcode-userrole.cache_path', storage_path('vendor/popcode/'));
        $this->cacheFile = $this->cachePath . $this->cacheFile;
        $this->checkDirectory();
    }

    public function get() {
        if (!\File::exists($this->cacheFile)) {
            return $this->makeCache();
        }
        return json_decode(\File::get($this->cacheFile), true);
    }

    public function makeCache() {
        $model = \Config::get('popcode-userrole.route_model', \PopCode\UserRole\Models\RouteXRole::class);
        $model = new $model();
        $routes = $model->with('role')->get();
        $roles = [];
        $routes->each(function($route) use (&$roles) {
            isset($roles[$route->route]) || $roles[$route->route] = [];
            $methods = explode('|', $route->method);
            foreach ($methods as $method) {
                isset($roles[$route->route][$method]) || $roles[$route->route][$method] = ['by_label' => [], 'by_num' => []];

                $roles[$route->route][$method]['by_label'][$route->role->label] = $route->role->label;
                $roles[$route->route][$method]['by_num'][$route->role->id] = $route->role->id;
            }
        });

        \File::put($this->cacheFile, json_encode($roles));
        return $roles;
    }

    protected function checkDirectory() {
        \File::isDirectory($this->cachePath) || \File::makeDirectory($this->cachePath, 0755, true);
    }
}
