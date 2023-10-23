<?php

namespace MicaelDias\SingleFileRoutes\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Workbench\App\Providers\RouteServiceProvider;
use Workbench\App\Providers\WorkbenchServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            RouteServiceProvider::class,
            WorkbenchServiceProvider::class,
        ];
    }
}
