<?php

namespace Jidaikobo\Kontiki\Config;

use Jidaikobo\Kontiki\Controllers;
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
    }
}
