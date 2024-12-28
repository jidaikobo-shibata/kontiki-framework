<?php

use Slim\Routing\RouteCollectorProxy;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;

return function ($app) use ($container) {

    \Jidaikobo\Kontiki\Controllers\AuthController::registerRoutes($app);
    \Jidaikobo\Kontiki\Controllers\DashboardController::registerRoutes($app);
    \Jidaikobo\Kontiki\Controllers\FileController::registerRoutes($app);
    \Jidaikobo\Kontiki\Controllers\UserController::registerRoutes($app, 'users');
    \Jidaikobo\Kontiki\Controllers\PostController::registerRoutes($app, 'posts');
//    $app->get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);
};
