<?php

namespace Workbench\App\Http\Api\User;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MicaelDias\SingleFileRoutes\Routing\Route;
use Workbench\App\Http\ApiRouteGroup;

#[Route(group: ApiRouteGroup::class, method: 'GET', uri: '/user/{id}')]
class Get
{
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
