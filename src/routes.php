<?php

use Slim\Routing\RouteCollectorProxy;
use jidaikobo\kontiki\Middleware\AuthMiddleware;

return function ($app) use ($container) {

    \jidaikobo\kontiki\Controllers\AuthController::registerRoutes($app);
    \jidaikobo\kontiki\Controllers\DashboardController::registerRoutes($app);
    \jidaikobo\kontiki\Controllers\FileController::registerRoutes($app);
    \jidaikobo\kontiki\Controllers\UserController::registerRoutes($app, 'users');
    \jidaikobo\kontiki\Controllers\PostController::registerRoutes($app, 'posts');
//    $app->get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);

};
