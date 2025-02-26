<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class CreateEditRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/create', [$controllerClass, 'renderCreateForm'])
            ->setName("{$basePath}|x_create|dashboard,createButton");
        $group->post('/create', [$controllerClass, 'handleCreate']);
        $group->get('/edit/{id}', [$controllerClass, 'renderEditForm']);
        $group->post('/edit/{id}', [$controllerClass, 'handleEdit']);
    }
}
