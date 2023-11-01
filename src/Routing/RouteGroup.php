<?php

namespace MicaelDias\SingleFileRoutes\Routing;

interface RouteGroup
{
    /**
     * The URI prefix for all routes on this group.
     */
    public static function prefix(): string;

    /**
     * The middleware used for all routes on this group.
     *
     *  @return  string[]
     */
    public static function middleware(): array;

    /**
     * Assign this route group to a subdomain.
     */
    public static function domain(): ?string;
}
