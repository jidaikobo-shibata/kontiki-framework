<?php

namespace Jidaikobo\Kontiki\Config;

use Aura\Session\SessionFactory;
use Aura\Session\Session;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use DI\Container;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\FileService;
use Jidaikobo\Kontiki\Services\SidebarService;
use Jidaikobo\Kontiki\Utils\Env;
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
        $container = $this->app->getContainer();

        // Set up a CakePHP Connection instance
        $container->set(
            Capsule::class,
            function () {
                $capsule = new Capsule();

                $capsule->addConnection([
                    'driver' => 'sqlite',
                    'database' => KONTIKI_PROJECT_PATH . '/' . Env::get('DB_DATABASE'),
                    'charset' => 'utf8',
                    'collation' => 'utf8_unicode_ci',
                    'prefix' => '',
                ]);

                // Global access for Eloquent or Query Builder
                $capsule->setAsGlobal();
                return $capsule;
            }
        );

        $container->set(
            Connection::class,
            function (Capsule $capsule) {
                return $capsule->getConnection();
            }
        );

        // Set up a Aura\Session instance
        $container->set(
            Session::class,
            function () {
                $sessionFactory = new SessionFactory();
                return $sessionFactory->newInstance($_COOKIE);
            }
        );

        // Register PhpRenderer
        $container->set(
            PhpRenderer::class,
            function () {
                return new PhpRenderer(KONTIKI_PROJECT_PATH . '/src/views');
            }
        );

        // Register FileService
        $container->set(
            FileService::class,
            function () {
                $uploadDir = KONTIKI_PROJECT_PATH . Env::get('UPLOADDIR');
                $allowedTypesJson = Env::get('ALLOWED_MIME_TYPES') ?? '[]';
                $allowedTypes = json_decode($allowedTypesJson, true);
                $maxSize = Env::get('MAXSIZE') ?? 5000000;
                return new FileService($uploadDir, $allowedTypes, $maxSize);
            }
        );

        // Set up Sidebar
        $container->set(
            SidebarService::class,
            function () {
                return new SidebarService(
                    $this->app->getRouteCollector()->getRouteParser(),
                    $this->app->getRouteCollector()
                );
            }
        );
    }
}
