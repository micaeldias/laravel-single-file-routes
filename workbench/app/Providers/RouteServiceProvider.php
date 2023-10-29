<?php

namespace Workbench\App\Providers;

use MicaelDias\SingleFileRoutes\Routing\RouteGroup;
use MicaelDias\SingleFileRoutes\Routing\RouteServiceProvider as SingleFileRoutesServiceProvider;

class RouteServiceProvider extends SingleFileRoutesServiceProvider
{
    /**
     * @var RouteGroup[]|string[]
     */
    protected array $groups = [
        \Workbench\App\Http\Api\ApiRouteGroup::class,
    ];
}
