<?php

namespace Jidaikobo\Kontiki;

use DI\Container;
use Jidaikobo\Log;
use Slim\Factory\AppFactory;

class Bootstrap
{
    public static function init(string $env = 'production')
    {
        // Set the error log handler
        Log::getInstance()->registerHandlers();

        // set project path
        $projectPath = $env === 'development' ? dirname(__DIR__) : dirname(__DIR__, 4);
        define('KONTIKI_PROJECT_PATH', $projectPath);

        // load config
        Utils\Env::loadConfigPath(KONTIKI_PROJECT_PATH . "/config/{$env}/");

        // Load default language on class load
        $language = Utils\Env::get('LANG') ?? 'en';
        Utils\Lang::setLanguage($language);

        // Load Functions
        if ($env === 'development') {
            require __DIR__ . '/functions/dev/functions.php';
        }
        require __DIR__ . '/functions/functions.php';

        // Configure a PHP-DI container
        $container = new Container();
        AppFactory::setContainer($container);

        // Create a Slim application
        $app = AppFactory::create();
        $app->addErrorMiddleware(true, true, true);
        $basePath = Utils\Env::get('BASEPATH') ?? '/';
        $app->setBasePath($basePath);

        // Add a header for security measures
        $app->add(Middleware\SecurityHeadersMiddleware::class);

        // Set dependencies
        $dependencies = new Config\Dependencies($app);
        $dependencies->register();

        // Set Route
        $routesClass = class_exists('App\Config\Routes')
            ? new \App\Config\Routes()
            : new \Jidaikobo\Kontiki\Config\Routes();
        $routesClass->register($app, $container);

        return $app;
    }
}
