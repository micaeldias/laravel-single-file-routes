<?php

namespace Workbench\App\Providers;

use MicaelDias\SingleFileRoutes\Routing\RouteServiceProvider as SingleFileRoutesServiceProvider;

class RouteServiceProvider extends SingleFileRoutesServiceProvider
{
    public function getAppNamespace(): string
    {
        return "Workbench\\App";
    }
}
