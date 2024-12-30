<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class IndexExpiredRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/index/expired', [$controllerClass, 'expiredIndex'])->setName("{$basePath}_index_expired");
    }
}
