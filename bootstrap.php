<?php

require __DIR__ . '/vendor/autoload.php';

// Load .env (if .dev exists, it's the development environment)
use Jidaikobo\Kontiki\Utils\Env;
$env = file_exists(__DIR__ . '/.dev') ? 'development' : 'production';
Env::setPath(__DIR__ . "/config/{$env}/");

// Load Functions
if ($env === 'development') {
    require __DIR__ . '/src/functions/dev/functions.php';
}
require __DIR__ . '/src/functions/functions.php';

// Configure a PHP-DI container
use DI\Container;
use Slim\Factory\AppFactory;

$container = new Container();
AppFactory::setContainer($container);

// Load default language on class load
use Jidaikobo\Kontiki\Utils\Lang;
Lang::setLanguage(Env::get('LANG'));

// Create a Slim application
$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);
$app->setBasePath(Env::get('BASEPATH'));

// Add a header for security measures
use Jidaikobo\Kontiki\Middleware\SecurityHeadersMiddleware;
$app->add(SecurityHeadersMiddleware::class);

// Set dependencies
(require __DIR__ . '/src/Dependencies.php')($app);

// Set the error log handler
use Jidaikobo\Log;
Log::getInstance()->registerHandlers();

return $app;
