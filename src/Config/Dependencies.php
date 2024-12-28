<?php

namespace Jidaikobo\Kontiki\Config;

use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\FileService;
use Jidaikobo\Kontiki\Services\SidebarService;
use Jidaikobo\Kontiki\Utils\Env;
use Aura\Session\SessionFactory;
use Aura\Session\Session;
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
            function (ContainerInterface $container) {
                return new PhpRenderer(KONTIKI_PROJECT_PATH . '/src/Views');
            }
        );

        // Register FileService
        $container->set(
            FileService::class,
            function (ContainerInterface $container) {
                $uploadDir = KONTIKI_PROJECT_PATH . Env::get('UPLOADDIR');
                $allowedTypes = json_decode(Env::get('ALLOWED_MIME_TYPES'), true);
                $maxSize = Env::get('MAXSIZE');
                return new FileService($uploadDir, $allowedTypes, $maxSize);
            }
        );

        // Set up Sidebar
        $container->set(
            SidebarService::class,
            function ($container) {
                return new SidebarService(
                    $this->app->getRouteCollector()->getRouteParser(),
                    $this->app->getRouteCollector()
                );
            }
        );
    }
}
