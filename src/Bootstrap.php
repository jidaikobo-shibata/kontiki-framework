<?php

namespace Jidaikobo\Kontiki;

use DI\Container;
use Dotenv\Dotenv;
use Jidaikobo\Log;
use Slim\Factory\AppFactory;
use Slim\App;

class Bootstrap
{
    public static function init(string $env = 'production', bool $frontend = FALSE)
    {
        // check response performance
        $GLOBALS['KONTIKI_START_TIME'] = microtime(true);

        // Set the error log handler
        Log::getInstance()->registerHandlers();

        // load config
        $projectPath = $env === 'development' ? dirname(__DIR__) : dirname(__DIR__, 4);
        $dotenv = Dotenv::createImmutable($projectPath . "/config/{$env}/");
        $dotenv->load();

        // Load Functions
        if ($env === 'development') {
            require __DIR__ . '/functions/dev/functions.php';
        }
        require __DIR__ . '/functions/functions.php';

        // setenv
        setenv('ENV', $env);
        setenv('PROJECT_PATH', $projectPath);

        // Load default language on class load
        $language = env('LANG', 'en');
        Utils\Lang::setLanguage($language);

        if ($frontend) {
            return;
        }

        // Configure a PHP-DI container
        $container = new Container();
        AppFactory::setContainer($container);

        // Create a Slim application
        $app = AppFactory::create();
        $app->addErrorMiddleware(true, true, true);
        $basePath = env('BASEPATH', '/');
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

    public static function run(App $app, bool $timer = false): void
    {
        $app->run();

        if ($timer) {
            $elapsedTime = microtime(true) - $GLOBALS['KONTIKI_START_TIME'];
            jlog("Total execution time: " . number_format($elapsedTime, 6) . " seconds");
        }
    }
}
