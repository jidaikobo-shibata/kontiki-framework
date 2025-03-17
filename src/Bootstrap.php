<?php

namespace Jidaikobo\Kontiki;

use DI\Container;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\App;

use Jidaikobo\Log;

class Bootstrap
{
    public static function init(string $env = 'production')
    {
        // check response performance
        $GLOBALS['KONTIKI_START_TIME'] = microtime(true);

        // Set the error log handler
        Log::getInstance()->registerHandlers();

        // load config
        $projectPath = $env == 'development' ? dirname(__DIR__) : dirname(__DIR__, 4);
        $dotenv = Dotenv::createImmutable($projectPath . "/config/{$env}/");
        $dotenv->load();

        // Load Functions
        require __DIR__ . '/functions/functions.php';

        // setenv
        setenv('ENV', $env);
        setenv('PROJECT_PATH', $projectPath);

        // Load default language on class load
        $language = env('LANG', 'en');
        Utils\Lang::setLanguage($language);

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
        $auth = $app->getContainer()->get(Core\Auth::class);
        $routesClass = class_exists('App\Config\Routes')
            ? new \App\Config\Routes()
            : new \Jidaikobo\Kontiki\Config\Routes();
        $routesClass->register($app, $container, $auth);

        return $app;
    }

    public static function run(App $app): void
    {
        $app->run();
        self::performance();
    }

    public static function performance($timer = false): void
    {
        // $timer = true;
        if ($timer) {
            $elapsedTime = microtime(true) - $GLOBALS['KONTIKI_START_TIME'];
            jlog("Total execution time: " . number_format($elapsedTime, 6) . " seconds");
        }
    }
}
