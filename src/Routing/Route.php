<?php

namespace MicaelDias\SingleFileRoutes\Routing;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Route
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * The HTTP method of the request.
     *
     * @var string
     */
    public static $method;

    /**
     * The URI of the request.
     *
     *  @var string
     */
    public static $uri;

    /**
     * Add middleware to the request.
     *
     *  @var string[]
     */
    public static $middleware = [];
}
