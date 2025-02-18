<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class TrashRestoreRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        // $group->get('/index/trash', [$controllerClass, 'trashIndex'])->setName("{$basePath}_index_trash");
        $group->get('/index/trash', [$controllerClass, 'trashIndex']);
        $group->get('/trash/{id}', [$controllerClass, 'trash']);
        $group->post('/trash/{id}', [$controllerClass, 'handleTrash']);
        $group->get('/restore/{id}', [$controllerClass, 'restore']);
        $group->post('/restore/{id}', [$controllerClass, 'handleRestore']);
    }
}
