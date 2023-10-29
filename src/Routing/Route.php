<?php

namespace MicaelDias\SingleFileRoutes\Routing;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Route
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * The HTTP method of the request.
     */
    public static string $method;

    /**
     * The URI of the request.
     */
    public static string $uri;

    /**
     * Add middleware to the request.
     *
     *  @var string[]
     */
    public static array $middleware = [];
}
