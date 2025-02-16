<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class FrontendReadRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/' . $basePath . '/slug/{slug}',  [$controllerClass, 'frontendReadBySlug']);
    }
}
