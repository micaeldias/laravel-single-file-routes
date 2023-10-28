<?php

namespace MicaelDias\SingleFileRoutes;

use MicaelDias\SingleFileRoutes\Commands\InstallCommand;
use MicaelDias\SingleFileRoutes\Commands\RouteMakeCommand;
use MicaelDias\SingleFileRoutes\Commands\RouteGroupMakeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSingleFileRoutesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-single-file-routes')
            ->publishesServiceProvider('SingleFileRoutesServiceProvider')
            ->hasConfigFile()
            ->hasCommands([
                InstallCommand::class,
                RouteMakeCommand::class,
                RouteGroupMakeCommand::class,
            ]);
    }
}
