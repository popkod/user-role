<?php

namespace PopCode\UserRole\Middleware;

use Route;
use Closure;
use PopCode\UserRole\Models;

class RoleMiddleware
{
    protected $route;
    protected $method;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string[] ...$guards
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->route = Route::current();
        $this->method = strtoupper($request->method());

        $this->checkAccess(1);
        return $next($request);
    }

    protected function checkAccess($role) {
        $routesXRoles = new \PopCode\UserRole\Helpers\CacheHelper();
        $key = is_numeric($role) ? 'by_num' : 'by_label';

        return !isset($routesXRoles[$key][$this->route]) ||
                !isset($routesXRoles[$key][$this->route][$this->method]) ||
                isset($routesXRoles[$key][$this->route][$this->method][$role]);
    }
}
