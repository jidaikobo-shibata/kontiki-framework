<?php

namespace jidaikobo\kontiki\Controllers;

use jidaikobo\kontiki\Utils\Lang;
use jidaikobo\kontiki\Services\SidebarService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteParser;
use Slim\Views\PhpRenderer;

class DashboardController
{
    private PhpRenderer $view;
    private RouteParser $routeParser;
    private RouteCollector $routeCollector;

    //    public function __construct(RouteParser $routeParser, RouteCollector $routeCollector, PhpRenderer $view, SidebarService $sidebarService)
    public function __construct(PhpRenderer $view, SidebarService $sidebarService)
    {
        //        $this->routeParser = $routeParser;
        //        $this->routeCollector = $routeCollector;
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $sidebarService->getLinks()]);
    }

    public function dashboard(Request $request, Response $response): Response
    {
        /*
        // 名前付きルートを取得
        $routes = $this->routeCollector->getRoutes();
        $groupedLinks = [];
        $groupNames = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            $pattern = $route->getPattern();

            // 名前が "dashboard" または "login" のルートはスキップ
            if (!$name || in_array($name, ['dashboard', 'login'])) {
                continue;
            }

            // グループの抽出
            $group = $this->extractGroupFromPattern($pattern);

            // グループ名を設定
            if (!isset($groupNames[$group])) {
                $groupNames[$group] = Lang::get("{$group}_management", ucfirst($group) . 'Management');
            }

            // グループごとにリンクを分類
            $groupedLinks[$group][] = [
                'name' => Lang::get($name, $name), // ルート名のローカライズ
                'url' => $this->routeParser->urlFor($name),
            ];
        }

        // ビューに渡してレンダリング
        $content = $this->view->fetch('Dashboard/dashboard.php', [
            'groupedLinks' => $groupedLinks,
            'groupNames' => $groupNames,
          ]);
        */
        return $this->view->render(
            $response,
            'layout.php',
            [
            'pageTitle' => Lang::get('management_portal', 'Management Portal'),
            //          'content' => $content
            ]
        );
        ;
    }

    /**
     * Extract the group name from the route pattern.
     *
     * @param  string $pattern The route pattern (e.g., "/admin/users").
     * @return string The extracted group name (e.g., "admin").
     */
    private function extractGroupFromPattern(string $pattern): string
    {
        // 2つめのセグメントをグループ名として抽出
        $segments = explode('/', trim($pattern, '/'));
        return $segments[1] ?? 'general';
    }
}
