<?php

// micaeldias/laravel-single-file-routes

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP Namespace
    |--------------------------------------------------------------------------
    |
    | The HTTP namespace is where your route groups will be created when using
    | the CLI. There are no restrictions on using multiple ones, you can have
    | route groups scattered around your application if it fits your use case.
    |
    */

    'http-namespace' => 'App\\Http',

    /*
    |--------------------------------------------------------------------------
    | Routes Namespace
    |--------------------------------------------------------------------------
    |
    | The routes namespace is where your routes will be created when using the
    | CLI. This is simply used as a convenience to speed up your development,
    | you're free to place routes wherever you want.
    |
    */

    'routes-namespace' => 'App\\Http\\Routes',

    /*
    |--------------------------------------------------------------------------
    | Generate from URI
    |--------------------------------------------------------------------------
    |
    | If this is set true, when running the make:route command, the namespace
    | where the route will be placed will default to match with the URI.
    |
    | E.g. when the full URI of the route is /api/user and the route's
    | namespace is App\Http\Routes the default namespace will be:
    |  - App\Http\Routes\Api\User
    |
    */
    'generate-from-uri' => true,

];
