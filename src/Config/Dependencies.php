<?php

namespace Jidaikobo\Kontiki\Config;

use Aura\Session\SessionFactory;
use Aura\Session\Session;
use DI\Container;
use Slim\App;
use Slim\Views\PhpRenderer;

use Jidaikobo\Kontiki\Services\FileService;
use Jidaikobo\Kontiki\Services\RoutesService;
use Jidaikobo\Kontiki\Core\Database;

class Dependencies
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function register(): void
    {
        /** @var Container $container */
        $container = $this->app->getContainer();

        // Set up App
        $container->set(App::class, $this->app);

        // database
        $container->set(
            Database::class,
            function () {
                return new Database([
                        'driver' => 'sqlite',
                        'database' => env('PROJECT_PATH', '') . '/' . env('DB_DATABASE', ''),
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                        'prefix' => '',
                    ]);
            }
        );

        // Set up a Aura\Session instance
        $container->set(
            Session::class,
            function () {
                // cache JavaScript and image
                $request_uri = $_SERVER['REQUEST_URI'];
                if (
                    strpos($request_uri, '.js') !== false ||
                    strpos($request_uri, '.css') !== false ||
                    strpos($request_uri, '.ico') !== false
                ) {
                    session_cache_limiter('private_no_expire');
                }

                $sessionFactory = new SessionFactory();
                return $sessionFactory->newInstance($_COOKIE);
            }
        );

        // Register PhpRenderer
        $container->set(
            PhpRenderer::class,
            function () {
                return new PhpRenderer(__DIR__ . '/../../src/views');
            }
        );

        // Register FileService
        $container->set(
            FileService::class,
            function () {
                $uploadDir = env('PROJECT_PATH', '') . env('UPLOADDIR', '');
                $allowedTypesJson = env('ALLOWED_MIME_TYPES', '[]');
                $allowedTypes = json_decode($allowedTypesJson, true);
                $maxSize = env('MAXSIZE', 5000000);
                return new FileService($uploadDir, $allowedTypes, $maxSize);
            }
        );

        // Set up Routes
        $container->set(
            RoutesService::class,
            function () {
                return new RoutesService(
                    $this->app->getRouteCollector()
                );
            }
        );

    }
}
