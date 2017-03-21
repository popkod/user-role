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

        if (!empty($guards)) {
            $this->loadUser();
            if (\Config::get('popcode-userrole.inclusive', false)) {
                if ($this->user->hasAtLeastRole($this->getMinGuardId($guards))) {
                    return $next($request);
                }
            } else {
                if ($this->user->hasAnyRole($guards)) {
                    return $next($request);
                }
            }
            abort(403, 'Permission denied');
        }

        $this->checkAccess();
        return $next($request);
    }

    protected function loadUser() {
        if ($this->user) {
            return;
        }

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

        if (!method_exists($user, 'hasAtLeastRole')) {
            throw new MethodNotFoundException('The method hasAtLeastRole must be implemented on User model!', get_class($user), 'hasAtLeastRole');
        }

    }

    protected function getUserRoles() {
        return $this->user->getRoles();
    }

    protected function checkAccess() {

        $routesXRoles = (new \PopCode\UserRole\Helpers\CacheHelper())->get();

        if (!isset($routesXRoles[$this->route]) || !isset($routesXRoles[$this->route][$this->method])) {
            return;
        }

        $this->loadUser();
        $roles = $this->getRolesByGuards($this->getUserRoles()->pluck('id')->toArray());
        foreach($roles as $role) {
//            $key = is_numeric($role) ? 'by_num' : 'by_label';
            $key = 'by_num';
            if (isset($routesXRoles[$this->route][$this->method][$key][$role])) {
                return;
            }
        }

        abort(403, 'Permission denied');
    }

    protected function getRolesByGuards($guards) {
        $ids = [];
        $labels = [];
        foreach ($guards as $guard) {
            if (is_numeric($guard)) {
                $ids[] = $guard;
            } else {
                $labels[] = $guard;
            }
        }
        if (!empty($labels)) {
            $ids = array_merge($ids, Models\Role::whereIn('label', $labels)->get()->pluck('id')->toArray());
        }
        if (\Config::get('popcode-userrole.inclusive', false)) {
            $ids = range(0, max($ids));
        }
        return $ids;
    }

    protected function getMinGuardId($guards) {
        return min($this->getRolesByGuards($guards));
    }
}
