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

class DashboardController
{
    private PhpRenderer $view;
    private RouteParser $routeParser;
    private RouteCollector $routeCollector;

    public function __construct(PhpRenderer $view, GetRoutesService $getRoutesService)
    {
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $getRoutesService->getSidebar()]);
    }

    public static function registerRoutes(App $app): void
    {
        $app->group(
            '/admin',
            function (RouteCollectorProxy $group) {
                $group->get('/dashboard', [DashboardController::class, 'dashboard'])
                    ->setName('dashboard');
            }
        )->add(AuthMiddleware::class);
    }

    public function dashboard(Request $request, Response $response): Response
    {
        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => __('management_portal', 'Management Portal'),
                'content' => '',
            ]
        );
        ;
    }
}
