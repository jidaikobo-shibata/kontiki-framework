<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class MarkdownHelpRoutes
{
    public static function register(
        RouteCollectorProxy $app,
        string $basePath,
        string $controllerClass
    ): void {
        $app->get("/{$basePath}/markdown-help", [$controllerClass, 'showMarkdownHelp']);
    }
}
