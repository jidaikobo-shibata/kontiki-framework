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
        Controllers\SampleController::registerRoutes($app, 'sample');
    }
}
