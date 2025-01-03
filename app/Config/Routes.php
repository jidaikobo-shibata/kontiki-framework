<?php

namespace App\Config;

use App\Controllers;
use Jidaikobo\Kontiki\Config\Routes as DefalutRoutes;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use DI\Container;
use Slim\App;

class Routes extends DefalutRoutes
{
    public function register(App $app, Container $container): void
    {
        parent::register($app, $container);
/*
        $app->get('/info', [Controller\TestController::class, 'test'])
            ->setName('test')
            ->add($container->get(AuthMiddleware::class));
*/
        Controllers\InformationController::registerRoutes($app, 'informations');
    }
}
