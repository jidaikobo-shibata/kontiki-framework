<?php

namespace jidaikobo\kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class CreateEditRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/create', [$controllerClass, 'create'])->setName("{$basePath}_create");
        $group->post('/create', [$controllerClass, 'handleCreate']);
        $group->get('/edit/{id}', [$controllerClass, 'edit']);
        $group->post('/edit/{id}', [$controllerClass, 'handleEdit']);
    }
}
