<?php

use Slim\Routing\RouteCollectorProxy;
use jidaikobo\kontiki\Middleware\AuthMiddleware;
use jidaikobo\kontiki\Controllers\AuthController;
use jidaikobo\kontiki\Controllers\DashboardController;
use jidaikobo\kontiki\Controllers\PostController;
use jidaikobo\kontiki\Controllers\UserController;
// use jidaikobo\kontiki\Controllers\TestController;

return function ($app) use ($container) {
    // $app->get('/test', [TestController::class, 'test'])
    //     ->add($container->get(AuthMiddleware::class));

    $app->get('/login', [AuthController::class, 'showLoginForm'])
        ->setName('login');
    $app->post('/login', [AuthController::class, 'processLogin']);
    $app->post('/logout', [AuthController::class, 'logout']);

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
                    $subgroup->get('/edit', [UserController::class, 'create'])
                        ->setName('add_new_user');
                }
            );

            $group->group(
                '/posts',
                function (RouteCollectorProxy $subgroup) {
                    $subgroup->get('/index', [PostController::class, 'index'])
                        ->setName('posts_list');
                    $subgroup->get('/edit', [PostController::class, 'create'])
                        ->setName('add_new_post');
                }
            );

            // $group->get('/users/{id}/edit', [UserController::class, 'edit']);
            // $group->delete('/users/{id}', [UserController::class, 'delete']);

            // $group->get('/posts/{id}', [PostController::class, 'show']);
            // $group->put('/posts/{id}', [PostController::class, 'update']);
            // $group->delete('/posts/{id}', [PostController::class, 'delete']);
        }
    )->add($container->get(AuthMiddleware::class));

    $app->get('/posts/slug/{slug}', [PostController::class, 'showBySlug']);
};
