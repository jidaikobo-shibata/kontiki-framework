<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class CreateEditRoutes
{
    public static function register(RouteCollectorProxy $app, string $basePath, string $controllerClass): void
    {
        $app->get("/{$basePath}/create", [$controllerClass, 'renderCreateForm'])
            ->setName("{$basePath}|x_create|dashboard,createButton");
        $app->post("/{$basePath}/create", [$controllerClass, 'handleCreate']);
        $app->get("/{$basePath}/edit/{id}", [$controllerClass, 'renderEditForm']);
        $app->post("/{$basePath}/edit/{id}", [$controllerClass, 'handleEdit']);
    }
}
