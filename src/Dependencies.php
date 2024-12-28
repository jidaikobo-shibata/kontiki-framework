<?php

use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\FileService;
use Jidaikobo\Kontiki\Services\SidebarService;
use Jidaikobo\Kontiki\Utils\Env;
use Aura\Session\SessionFactory;
use Aura\Session\Session;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollector;
use Slim\Views\PhpRenderer;

return function (App $app) {
    $container = $app->getContainer();

    // PDOインスタンスを設定
    $container->set(
        PDO::class,
        function () {
            $pdo = new PDO(Env::get('DB_CONNECTION') . ':' . __DIR__ . '/../' . Env::get('DB_DATABASE'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        }
    );

    // Aura\Sessionインスタンスを設定
    $container->set(
        Session::class,
        function () {
            $sessionFactory = new SessionFactory();
            return $sessionFactory->newInstance($_COOKIE);
        }
    );

    // PhpRenderer の登録
    $container->set(
        PhpRenderer::class,
        function (ContainerInterface $container) {
            return new PhpRenderer(__DIR__ . '/Views');
        }
    );

    // FileService の登録
    $container->set(
        FileService::class,
        function (ContainerInterface $container) {
            $uploadDir = dirname(__DIR__, 2) . Env::get('UPLOADDIR');
            $allowedTypes = json_decode(Env::get('ALLOWED_MIME_TYPES'), true);
            $maxSize = Env::get('MAXSIZE');
            return new FileService($uploadDir, $allowedTypes, $maxSize);
        }
    );

    // サイドバーの設定
    $container->set(
        SidebarService::class,
        function ($container) use ($app) {
            return new SidebarService(
                $app->getRouteCollector()->getRouteParser(),
                $app->getRouteCollector()
            );
        }
    );
};
