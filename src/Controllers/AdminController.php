<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
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
                $group->get('/admin.js', [AdminController::class, 'serveJs']);
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
}
