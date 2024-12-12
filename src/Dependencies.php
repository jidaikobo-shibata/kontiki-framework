<?php

use jidaikobo\kontiki\Services\SidebarService;
use jidaikobo\kontiki\Utils\Env;
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

    /*
    $container->set(Slim\Routing\RouteParser::class, function () use ($app) {
        return $app->getRouteCollector()->getRouteParser();
    });

    $container->set(RouteCollector::class, function ($container) use ($app) {
        return $app->getRouteCollector();
    });

    $container->set(SidebarService::class, function () {
        return new SidebarService();
    });
    */
};
