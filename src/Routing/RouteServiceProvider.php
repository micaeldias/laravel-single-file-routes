<?php

namespace MicaelDias\SingleFileRoutes\Routing;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * @var RouteGroup[]|string[]
     */
    protected $groups = [];

    /**
     * Boot the service.
     */
    public function boot(Router $router): void
    {
        $this->registerRoutes($router);
    }

    /**
     * Register the routes defined by the application.
     */
    protected function registerRoutes(Router $router): void
    {
        foreach ($this->groups as $group) {
            $router->group(['prefix' => $group::$prefix], function (Router $router) use ($group) {
                foreach ($group::$routes as $route) {
                    $router->match(
                        $route::$method,
                        $route::$uri,
                        $route
                    )
                        ->name($route)
                        ->middleware(array_merge($group::$middleware, $route::$middleware));
                }
            });
        }
    }
}
