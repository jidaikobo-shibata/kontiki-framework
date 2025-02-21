<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

class DashboardController
{
    private PhpRenderer $view;
    private array $routes;

    public function __construct(PhpRenderer $view, GetRoutesService $getRoutesService)
    {
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $getRoutesService->getSidebar()]);
        $this->routes = $getRoutesService->getLinks();
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
        $namedRoutes = array_filter($this->routes, function ($item) {
            return isset($item['name']) &&
                (
                    strpos($item['name'], 'index') !== false ||
                    strpos($item['name'], 'create') !== false
                );
        });

        $postTypes = [];
        foreach ($namedRoutes as $namedRoute)
        {
            $postType = str_replace(['_index', '_create'], '', $namedRoute['name']);
            if (strpos($namedRoute['name'], $postType) === false) continue;
            $postTypes[$postType][] = $namedRoute;
        }

        $html = '';
        foreach ($postTypes as $postTypeName => $postTypeVals)
        {
            $html.= '<div class="card"><div class="card-header">';
            $html.= '<h2 class="card-title">' . __($postTypeName) . '</h3>';
            $html.= '</div><div class="card-body"><ul>';
            foreach ($postTypeVals as $val)
            {
                $langLabel = preg_replace('/^[^_]+/', 'x', $val['name']);
                $html.= '<li>';
                $html.= '<a href="' . env('BASEPATH') . $val['path'] . '">';
                $html.= __($langLabel, ':name index', ['name' => __($postTypeName)]);
                $html.= '</a>';
                $html.= '</li>';
            }
            $html.= '</ul></div></div>';
        }

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => __('management_portal', 'Management Portal'),
                'content' => $html,
            ]
        );
        ;
    }
}
