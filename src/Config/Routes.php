<?php

namespace Jidaikobo\Kontiki\Config;

use Jidaikobo\Kontiki\Controllers;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use DI\Container;
use Slim\App;

class Routes
{
    public function register(App $app, Container $_container): void
    {
        Controllers\AdminController::registerRoutes($app);
        Controllers\AuthController::registerRoutes($app);
        Controllers\DashboardController::registerRoutes($app);
        Controllers\FileController::registerRoutes($app);
        Controllers\UserController::registerRoutes($app, 'user');
        Controllers\PostController::registerRoutes($app, 'post');
//        Controllers\CategoryController::registerRoutes($app, 'post/category');

        $app->add(AuthMiddleware::class);
    }
}
