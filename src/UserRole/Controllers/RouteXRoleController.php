<?php

namespace PopCode\UserRole\Controllers;

use PopCode\UserRole\Models\Role;
use Illuminate\Routing\Controller as BaseController;

class RouteXRoleController extends BaseController
{
    public function index() {
        $data = Role::with(['routes'])->get();
        return $this->responseGenerator($data);
    }

    public function update() {
        /**
         * sample request payload:
         * {
         * routes: [
         *      {route: '{any}', role: 1, methods: ['GET']},
         *      {route: '{any}', role: 2, methods: ['GET', 'POST', 'PUT']},
         *      {route: '{any}', role: 3, methods: ['GET', 'POST', 'PUT', 'DELETE']}
         *    ]
         *
         * (opt) route: '{any}'
         * }
         */

        // delete all / or for the provided route
        // insert all
    }

    public function getAllRoutes() {
        $registeredRoutes = \Route::getRoutes();
        $routes = [];
        foreach($registeredRoutes as $route) {
            /* @var \Illuminate\Routing\Route $route */
            $controller = isset($route->getAction()['controller']) ? $route->getAction()['controller'] : '';
            $routes[] = [
                'methods' => $this->filterMethods($route->methods()),
                'route' => $route->uri(),
                'action' => $controller,
            ];
        }
        return $this->responseGenerator($routes);
    }

    protected function afterSaveHook() {
        (new \PopCode\UserRole\Helpers\CacheHelper())->makeCache();
    }

    protected function filterMethods($methods) {
        $head = array_search('HEAD', $methods);
        if ($head !== false) {
            unset($methods[$head]);
        }
        return array_values($methods);
    }

    protected function responseGenerator($responseData, $type = null) {
        if (\Request::ajax() || \Request::wantsJson()) {
            return response()->json($responseData);
        }

        // TODO return view by type
        return $responseData;
    }

    protected function errorResponseGenerator($data, $messages, $type = null, $status = 400) {
        if (\Request::ajax() || \Request::wantsJson()) {
            return response()->json(['error' => $messages], $status);
        }

        // TODO return view by type
        return $messages;
    }
}
