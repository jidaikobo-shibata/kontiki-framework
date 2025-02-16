<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class FrontendIndexRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/' . $basePath . '/index',  [$controllerClass, 'frontendIndex']);
    }
}
