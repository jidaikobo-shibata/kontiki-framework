<?php

namespace Jidaikobo\Kontiki\Services;

use Slim\Routing\RouteCollector;
use Slim\Routing\RouteParser;

class SidebarService
{
    private RouteParser $routeParser;
    private RouteCollector $routeCollector;

    public function __construct(RouteParser $routeParser, RouteCollector $routeCollector)
    {
        $this->routeParser = $routeParser;
        $this->routeCollector = $routeCollector;
    }

    public function getLinks(): array
    {
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

            // 名前が "." から始まるルートはメニュー用でない
            if (!$name || strpos($name, '.') === 0) {
                continue;
            }

            // グループの抽出
            $group = $this->extractGroupFromPattern($pattern);

            // グループ名を設定
            if (!isset($groupNames[$group])) {
                $groupNames[$group] = __("x_management", ':name Management', ['name' => __($group)]);
            }

            // グループごとにリンクを分類
            $langLabel = preg_replace('/^[^_]+/', 'x', $name);
            $groupedLinks[$group][] = [
                'name' => __($langLabel, ':name index', ['name' => __($group)]),
                'url' => $this->routeParser->urlFor($name),
                'icon' => 'fa-' . strtolower($name) . '-alt',
            ];
        }

        return [
            'groupedLinks' => $groupedLinks,
            'groupNames' => $groupNames,
          ];
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
