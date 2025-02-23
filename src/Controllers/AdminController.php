<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteParser;
use Slim\Views\PhpRenderer;

class AdminController
{
    private PhpRenderer $view;

    public function __construct(PhpRenderer $view)
    {
        $this->view = $view;
    }

    public static function registerRoutes(App $app): void
    {
        $app->group(
            '/admin',
            function (RouteCollectorProxy $group) {
                $group->get('/admin.js', AdminController::class . ':serveJs');
                $group->get('/favicon.ico', AdminController::class . ':serveFavicon');
            }
        )->add(AuthMiddleware::class);
    }

    /**
     * Serve the requested JavaScript file.
     *
     * @return Response
     */
    public function serveJs(Request $request, Response $response): Response
    {
        $content = $this->view->fetch('js/admin.js.php');
        $response->getBody()->write($content);
        return $response->withHeader('Content-Type', 'application/javascript; charset=utf-8')->withStatus(200);
    }

    /**
     * Serve the requested favicon file.
     *
     * @return Response
     */
    public function serveFavicon(Request $request, Response $response): Response
    {
        $faviconPath = env('PROJECT_PATH', '') . '/src/views/images/favicon.ico';
        $content = file_get_contents($faviconPath);
        $response->getBody()->write($content);
        return $response
            ->withHeader('Content-Type', 'image/x-icon')
            ->withHeader('Cache-Control', 'public, max-age=86400') // 1日キャッシュ
            ->withStatus(200);
    }
}
