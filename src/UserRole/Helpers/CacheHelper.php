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
        $byRoleNum = [];
        $byRoleLabel = [];
        $routes->each(function($route) use (&$byRoleNum, &$byRoleLabel) {
            isset($byRoleNum[$route->route]) || $byRoleNum[$route->route] = [];
            isset($byRoleLabel[$route->route]) || $byRoleLabel[$route->route] = [];
            $methods = explode('|', $route->method);
            foreach ($methods as $method) {
                isset($byRoleNum[$route->route][$method]) || $byRoleNum[$route->route][$method] = [];
                isset($byRoleLabel[$route->route][$method]) || $byRoleLabel[$route->route][$method] = [];
                $byRoleNum[$route->route][$method][$route->role->id] = $route->role->label;
                $byRoleLabel[$route->route][$method][$route->role->label] = $route->role->id;
            }
        });
        $processed = [
            'by_num' => $byRoleNum,
            'by_label' => $byRoleLabel,
        ];

        \File::put($this->cacheFile, json_encode($processed));
        return $processed;
    }

    protected function checkDirectory() {
        \File::isDirectory($this->cachePath) || \File::makeDirectory($this->cachePath, 0755, true);
    }
}
