<?php

use Workbench\App\Http\Api\TeamController;
use Workbench\App\Http\Api\User\Get;

use function Pest\Laravel\get;

it('registers the class route', function () {
    expect(route(Get::class, ['id' => 123]))->toBe('http://localhost/api/user/123');
});

it('calls the class route', function () {
    get(route(Get::class, ['id' => 123]))->assertExactJson([
        'id' => 123,
        'name' => 'Test User',
    ]);
});

$teamsRoute = "Workbench\App\Http\Api\TeamController::index()";

it('registers the controller method route', function () use ($teamsRoute) {
    expect(route($teamsRoute))
        ->toBe('http://localhost/api/teams');
});

it('calls the controller method route', function () use ($teamsRoute) {
    get(route($teamsRoute))->assertExactJson([
        'teams' => [1, 2, 3]
    ]);
});
