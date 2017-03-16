<?php

namespace PopCode\UserRole\Middleware;

use Illuminate\Auth\AuthenticationException;
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
        $this->route = Route::current()->uri;
        $this->method = strtoupper($request->method());

        // TODO: user->role / user->roles
        $role = [1];
        $this->checkAccess($role);
        return $next($request);
    }

    protected function checkAccess($roles) {
        $routesXRoles = (new \PopCode\UserRole\Helpers\CacheHelper())->get();
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!isset($routesXRoles[$this->route]) || !isset($routesXRoles[$this->route][$this->method])) {
            return;
        }

        foreach($roles as $role) {
            $key = is_numeric($role) ? 'by_num' : 'by_label';
            if (isset($routesXRoles[$this->route][$this->method][$key][$role])) {
                return;
            }
        }

        throw new AuthenticationException;
    }
}
