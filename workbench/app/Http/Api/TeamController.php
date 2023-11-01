<?php

namespace Workbench\App\Http\Api;

use Illuminate\Http\JsonResponse;
use MicaelDias\SingleFileRoutes\Routing\Route;
use Workbench\App\Http\ApiRouteGroup;

class TeamController
{
    #[Route(group: ApiRouteGroup::class, method: 'GET', uri: '/teams')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'teams' => [1, 2, 3],
        ]);
    }
}
