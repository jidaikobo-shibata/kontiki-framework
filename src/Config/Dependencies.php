<?php

namespace Jidaikobo\Kontiki\Config;

use Aura\Session\SessionFactory;
use Aura\Session\Session;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use DI\Container;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\FileService;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\PhpRenderer;

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

        // Set up a Aura\Session instance
        $container->set(
            Session::class,
            function () {
                // cache JavaScript and image
                $request_uri = $_SERVER['REQUEST_URI'];
                if (
                    strpos($request_uri, '.js') !== false ||
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
                return new PhpRenderer(env('PROJECT_PATH', '') . '/src/views');
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

        // Set up getRoutes
        $container->set(
            GetRoutesService::class,
            function () {
                return new GetRoutesService(
                    $this->app->getRouteCollector()->getRouteParser(),
                    $this->app->getRouteCollector()
                );
            }
        );
    }
}
