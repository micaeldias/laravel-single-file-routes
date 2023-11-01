<?php

namespace Workbench\App\Http;

use MicaelDias\SingleFileRoutes\Routing\RouteGroup;

class ApiRouteGroup implements RouteGroup
{
    /**
     * {@inheritdoc}
     */
    public static function prefix(): string
    {
        return '/api';
    }

    /**
     * {@inheritdoc}
     */
    public static function middleware(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function domain(): ?string
    {
        return null;
    }
}
