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
    | Base classes
    |--------------------------------------------------------------------------
    |
    | You can customise the base class of your routes or route groups for extra
    | customization, this setting only applies to newly generated routes and
    | groups, so you will need to manually update any existing ones.
    |
    */

    'route-group-class' => 'MicaelDias\\SingleFileRoutes\\Routing\\RouteGroup',
    'route-class' => 'MicaelDias\\SingleFileRoutes\\Routing\\Route',

];
