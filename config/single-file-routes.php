<?php

// micaeldias/laravel-single-file-routes

return [

    /*
    |--------------------------------------------------------------------------
    | Routes Namespace
    |--------------------------------------------------------------------------
    |
    | The routes' namespace is where your route groups will be created when you
    | use CLI generators. There is no restriction on having multiple ones, you
    | you can override on a case by case basis with the --namespace CLI arg.
    |
    */

    'routes-namespace' => 'App\\Http\\Routes',

    /*
    |--------------------------------------------------------------------------
    | Routes Auto Discovery
    |--------------------------------------------------------------------------
    |
    | If you set auto discovery to false, single file routes will not attempt
    | to discover routes found under a route group's directory. Routes will
    | then have to be manually registered on the desired route group.
    |
    */

    'auto-discovery' => true,

];
