<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class DeleteRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/delete/{id}', [$controllerClass, 'delete']);
        $group->post('/delete/{id}', [$controllerClass, 'handleDelete']);
    }
}
