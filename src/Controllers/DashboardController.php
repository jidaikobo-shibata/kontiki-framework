<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
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
                $group->get('/dashboard', DashboardController::class . ':dashboard')
                    ->setName('dashboard');
            }
        )->add(AuthMiddleware::class);
    }

    public function dashboard(Request $request, Response $response): Response
    {
        // 指定のルートをフィルタリング
        $namedRoutes = array_filter($this->routes, fn($item) =>
            isset($item['name']) && (strpos($item['name'], 'index') !== false || strpos($item['name'], 'create') !== false));

        // Post Type を分類
        $postTypes = [];
        foreach ($namedRoutes as $route) {
            $postType = str_replace(['_index', '_create'], '', $route['name']);
            $postTypes[$postType][] = $route;
        }

        // HTML 生成
        $html = $this->generateDashboardHtml($postTypes);

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => __('management_portal', 'Management Portal'),
                'content' => $html,
            ]
        );
    }

    /**
     * Generate the HTML content for the dashboard.
     *
     * @param array $postTypes Categorized post types and their routes.
     * @return string The generated HTML.
     */
    private function generateDashboardHtml(array $postTypes): string
    {
        return array_reduce(array_keys($postTypes), function ($html, $postTypeName) use ($postTypes) {
            $cardContent = array_reduce($postTypes[$postTypeName], function ($listHtml, $route) use ($postTypeName) {
                $langLabel = preg_replace('/^[^_]+/', 'x', $route['name']);
                $link = '<a href="' . env('BASEPATH') . $route['path'] . '">';
                $link .= __($langLabel, ':name index', ['name' => __($postTypeName)]);
                $link .= '</a>';

                return $listHtml . "<li>$link</li>";
            }, '');

            return $html . <<<HTML
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">{$postTypeName}</h2>
                </div>
                <div class="card-body">
                    <ul>{$cardContent}</ul>
                </div>
            </div>
            HTML;
        }, '');
    }
}
