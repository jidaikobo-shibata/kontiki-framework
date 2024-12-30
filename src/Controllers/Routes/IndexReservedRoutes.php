<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class IndexReservedRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/index/reserved', [$controllerClass, 'reservedIndex'])->setName("{$basePath}_index_reserved");
    }
}
