<?php

namespace Jidaikobo\Kontiki\Config;

use Aura\Session\SessionFactory;
use Aura\Session\Session;
use DI\Container;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\FileService;
use Jidaikobo\Kontiki\Services\SidebarService;
use Jidaikobo\Kontiki\Utils\Env;
use Psr\Container\ContainerInterface;
use PDO;
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

        // Set up a PDO instance
        $container->set(
            PDO::class,
            function () {
                $pdo = new PDO('sqlite:' . KONTIKI_PROJECT_PATH . '/' . Env::get('DB_DATABASE'));
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                return $pdo;
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
                return new PhpRenderer(KONTIKI_PROJECT_PATH . '/src/Views');
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
