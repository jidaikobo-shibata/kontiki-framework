<?php

namespace App\Config;

use App\Controller;
use Jidaikobo\Kontiki\Config\Routes as DefalutRoutes;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use DI\Container;
use Slim\App;

class Routes extends DefalutRoutes
{
    public function register(App $app, Container $container): void
    {
        parent::register($app, $container);

        $app->get('/test', [Controller\TestController::class, 'test'])
            ->setName('test')
            ->add($container->get(AuthMiddleware::class));
    }
}
