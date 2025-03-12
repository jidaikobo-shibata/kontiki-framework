<?php

namespace Jidaikobo\Kontiki;

use DI\Container;
use Dotenv\Dotenv;
use Jidaikobo\Log;
use Slim\Factory\AppFactory;
use Slim\App;

class Bootstrap
{
    public static function init(string $env = 'production')
    {
        // check response performance
        $GLOBALS['KONTIKI_START_TIME'] = microtime(true);

        // Set the error log handler
        Log::getInstance()->registerHandlers();

        // load config
        $projectPath = $env == 'production' ? dirname(__DIR__, 4) : dirname(__DIR__);
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

        // Set Singleton - use it minimum!
        Core\Database::setInstance([
                'driver' => 'sqlite',
                'database' => env('PROJECT_PATH', '') . '/' . env('DB_DATABASE', ''),
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ]);
        Core\Auth::setInstance($app->getContainer()->get(\Aura\Session\Session::class));

        // Set Route
        $routesClass = class_exists('App\Config\Routes')
            ? new \App\Config\Routes()
            : new \Jidaikobo\Kontiki\Config\Routes();
        $routesClass->register($app, $container);

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
