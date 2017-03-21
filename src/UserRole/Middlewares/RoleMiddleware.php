<?php

namespace PopCode\UserRole\Middleware;

use Illuminate\Auth\AuthenticationException;
use Prophecy\Exception\Doubler\MethodNotFoundException;
use Route;
use Closure;
use PopCode\UserRole\Models;

class RoleMiddleware
{
    protected $route;
    protected $method;

    /**
     * @var \PopCode\UserRole\Models\User
     */
    protected $user;

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

        $whiteList = \Config::get('popcode-userrole.path_whitelist', []);
        if (in_array(ltrim($request->getPathInfo(), '/'), $whiteList)) {
            return $next($request);
        }

        $this->loadUser();

        if (!empty($guards)) {
            if ($this->user->hasAnyRole($guards)) {
                return $next($request);
            }
            throw new AuthenticationException;
        }

        $this->checkAccess();
        return $next($request);
    }

    protected function loadUser() {
        if (class_exists('\\PCAuth')) {
            $user = \PCAuth::user();
        } else {
            $user = \Auth::user();
        }
        if (!$user) {
            throw new AuthenticationException;
        }

        $this->user = $user;

        if (!method_exists($user, 'getRoles')) {
            throw new MethodNotFoundException('The method getRoles must be implemented on User model!', get_class($user), 'getRoles');
        }

    }

    protected function getUserRoles() {
        return $this->user->getRoles();
    }

    protected function checkAccess() {
        $roles = $this->getUserRoles()->pluck('id')->toArray();

        $routesXRoles = (new \PopCode\UserRole\Helpers\CacheHelper())->get();

        if (!isset($routesXRoles[$this->route]) || !isset($routesXRoles[$this->route][$this->method])) {
            return;
        }

        foreach($roles as $role) {
//            $key = is_numeric($role) ? 'by_num' : 'by_label';
            $key = 'by_num';
            if (isset($routesXRoles[$this->route][$this->method][$key][$role])) {
                return;
            }
        }

        throw new AuthenticationException;
    }
}
