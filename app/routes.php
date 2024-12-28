<?php

use Slim\Routing\RouteCollectorProxy;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use App\Controllers\TestController;

return function ($app) use ($container) {
    $app->get('/test', [TestController::class, 'test'])
        ->setName('test')
        ->add($container->get(AuthMiddleware::class));
};
