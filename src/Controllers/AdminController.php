<?php

namespace Jidaikobo\Kontiki\Controllers;

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
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
        $app->get('/admin.js', AdminController::class . ':serveJs');
        $app->get('/favicon.ico', AdminController::class . ':serveFavicon');
    }

    /**
     * Serve the requested JavaScript file.
     *
     * @return Response
     */
    public function serveJs(Request $request, Response $response): Response
    {
        $content = $this->view->fetch(
            'js/admin.js.php',
            [
                'publishing' => __('publishing'),
                'reserved' => __('reserved'),
                'expired' => __('expired'),
            ]
        );
        $response->getBody()->write($content);
        return $response->withHeader(
            'Content-Type',
            'application/javascript; charset=utf-8'
        )->withStatus(200);
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
            ->withHeader('Cache-Control', 'public, max-age=86400')
            ->withStatus(200);
    }
}
