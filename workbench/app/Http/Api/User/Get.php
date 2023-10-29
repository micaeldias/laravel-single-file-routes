<?php

namespace Workbench\App\Http\Api\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MicaelDias\SingleFileRoutes\Routing\Route;

class Get extends Route
{
    /**
     * {@inheritdoc}
     */
    public static string $method = 'GET';

    /**
     * {@inheritdoc}
     */
    public static string $uri = '/user/{id}';

    /**
     * {@inheritdoc}
     */
    public static array $middleware = [];

    /**
     * Handle the request.
     */
    public function __invoke(Request $request, int $id): JsonResponse
    {
        return new JsonResponse([
            'id' => $id,
            'name' => 'Test User',
        ]);
    }
}
