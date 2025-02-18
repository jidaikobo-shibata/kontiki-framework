<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class IndexDraftRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
//        $group->get('/index/draft', [$controllerClass, 'draftIndex'])->setName("{$basePath}_index_draft");
        $group->get('/index/draft', [$controllerClass, 'draftIndex']);
    }
}
