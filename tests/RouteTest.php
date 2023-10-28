<?php

use Workbench\App\Http\Api\User\Get;

use function Pest\Laravel\get;

it('registers the Get User route', function () {
    expect(route(Get::class, ['id' => 123]))->toBe('http://localhost/api/user/123');
});

it('calls the Get User route', function () {
    get(route(Get::class, ['id' => 123]))->assertExactJson([
        'id' => 123,
        'name' => 'Test User',
    ]);
});
