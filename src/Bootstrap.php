<?php

namespace Jidaikobo\Kontiki;

use DI\Container;
use Dotenv\Dotenv;
use Jidaikobo\Log;
use Slim\Factory\AppFactory;

class Bootstrap
{
    public static function init(string $env = 'production', bool $frontend = FALSE)
    {
        // check performance
        // $startTime = microtime(true);

        // Set the error log handler
        Log::getInstance()->registerHandlers();

        // set project path
        $projectPath = $env === 'development' ? dirname(__DIR__) : dirname(__DIR__, 4);
        define('KONTIKI_PROJECT_PATH', $projectPath);

        // load config
        $dotenv = Dotenv::createImmutable(KONTIKI_PROJECT_PATH . "/config/{$env}/");
        $dotenv->load();

        // Load Functions
        if ($env === 'development') {
            require __DIR__ . '/functions/dev/functions.php';
        }
        require __DIR__ . '/functions/functions.php';
        // jlog('Functions loaded: ' . microtime(true) - $startTime);

        // Load default language on class load
        $language = env('LANG', 'en');
        Utils\Lang::setLanguage($language);
        // jlog('Language set: ' . microtime(true) - $startTime);

        if ($frontend) {
            return;
        }

        // Configure a PHP-DI container
        $container = new Container();
        AppFactory::setContainer($container);
        // jlog('DI container configured: ' . microtime(true) - $startTime);

        // Create a Slim application
        $app = AppFactory::create();
        $app->addErrorMiddleware(true, true, true);
        $basePath = env('BASEPATH', '/');
        $app->setBasePath($basePath);
        // jlog('Slim app created: ' . microtime(true) - $startTime);

        // Add a header for security measures
        $app->add(Middleware\SecurityHeadersMiddleware::class);
        // jlog('Security headers middleware added: ' . microtime(true) - $startTime);

        // Set dependencies
        $dependencies = new Config\Dependencies($app);
        $dependencies->register();
        // jlog('Dependencies registered: ' . microtime(true) - $startTime);

        // Set Route
        $routesClass = class_exists('App\Config\Routes')
            ? new \App\Config\Routes()
            : new \Jidaikobo\Kontiki\Config\Routes();
        $routesClass->register($app, $container);
        // jlog('Routes registered and Bootstrap init finished: ' . microtime(true) - $startTime);

       return $app;
    }
}
