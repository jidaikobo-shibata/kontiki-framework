<?php

use Slim\Routing\RouteCollectorProxy;
use jidaikobo\kontiki\Middleware\AuthMiddleware;
use jidaikobo\kontiki\Controllers\AuthController;
use jidaikobo\kontiki\Controllers\DashboardController;
use jidaikobo\kontiki\Controllers\PostController;
use jidaikobo\kontiki\Controllers\UserController;

return function ($app) use ($container) {

    $app->get('/login', [AuthController::class, 'showLoginForm'])
        ->setName('login');
    $app->post('/login', [AuthController::class, 'processLogin']);
    $app->get('/logout', [AuthController::class, 'logout']);

    $app->group(
        '/admin',
        function (RouteCollectorProxy $group) {

            $group->get('/dashboard', [DashboardController::class, 'dashboard'])
                ->setName('dashboard');

            $group->group(
                '/users',
                function (RouteCollectorProxy $subgroup) {
                    $subgroup->get('/index', [UserController::class, 'index'])
                        ->setName('users_list');
                    $subgroup->get('/create', [UserController::class, 'create'])
                        ->setName('add_new_user');
                    $subgroup->get('/edit/{id}', [UserController::class, 'edit']);
                    $subgroup->get('/delete/{id}', [UserController::class, 'delete']);
                }
            );

            $group->group(
                '/posts',
                function (RouteCollectorProxy $subgroup) {
                    $subgroup->get('/index', [PostController::class, 'index'])
                        ->setName('posts_list');
                    $subgroup->get('/create', [PostController::class, 'create'])
                        ->setName('add_new_post');
                    $subgroup->get('/edit/{id}', [UserController::class, 'edit']);
                    $subgroup->get('/trash/{id}', [UserController::class, 'trash']);
                    $subgroup->get('/restore/{id}', [UserController::class, 'restore']);
                    $subgroup->get('/delete/{id}', [UserController::class, 'delete']);
                }
            );
        }
    )->add($container->get(AuthMiddleware::class));

    $app->get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);
};
