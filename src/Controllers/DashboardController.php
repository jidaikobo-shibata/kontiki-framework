<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Services\RoutesService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

class DashboardController
{
    private PhpRenderer $view;
    private array $routes;

    public function __construct(PhpRenderer $view, RoutesService $routesService)
    {
        $this->view = $view;
        $this->routes = $routesService->getRoutesByType('dashboard');
        $this->view->setAttributes([
                'sidebarItems' => $routesService->getRoutesByType('sidebar')
            ]);
    }

    public static function registerRoutes(App $app): void
    {
        $app->get('/dashboard', DashboardController::class . ':dashboard')
            ->setName('dashboard');
    }

    public function dashboard(Request $request, Response $response): Response
    {
        $content = $this->view->fetch(
            'dashboard/dashboard.php',
            ['dashboardItems' => $this->routes]
        );
        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => __('management_portal', 'Management Portal'),
                'content' => $content,
            ]
        );
    }
}
