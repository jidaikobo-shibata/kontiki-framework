<?php

use Slim\Routing\RouteCollectorProxy;
use jidaikobo\kontiki\Middleware\AuthMiddleware;
use jidaikobo\kontiki\Controllers\PostController;

return function ($app) use ($container) {

    \jidaikobo\kontiki\Controllers\AuthController::registerRoutes($app);
    \jidaikobo\kontiki\Controllers\DashboardController::registerRoutes($app);
    \jidaikobo\kontiki\Controllers\UserController::registerRoutes($app);
    $app->get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);

};
