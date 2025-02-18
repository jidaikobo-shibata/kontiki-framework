<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class PreviewRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/preview', [$controllerClass, 'handlePreview']);
        $group->get('/preview/{id}', [$controllerClass, 'handlePreviewById']);
    }
}
