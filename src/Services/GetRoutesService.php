<?php

namespace Jidaikobo\Kontiki\Services;

use Slim\Routing\RouteCollector;
use Slim\Routing\RouteParser;

class GetRoutesService
{
    private RouteParser $routeParser;
    private RouteCollector $routeCollector;

    public function __construct(RouteParser $routeParser, RouteCollector $routeCollector)
    {
        $this->routeParser = $routeParser;
        $this->routeCollector = $routeCollector;
    }

    protected function getRoutes(): array
    {
        return $this->routeCollector->getRoutes();
    }

    public function getLinks($controller = null): array
    {
        $routes = self::getRoutes();
        $routeList = [];

        foreach ($routes as $route) {
            if (
                !is_null($controller) &&
                strpos($route->getPattern(), '/' . $controller . '/') === false
            ) {
                continue;
            }

            $routeList[] = [
                'methods' => implode(', ', $route->getMethods()),
                'path' => $route->getPattern(),
                'name' => $route->getName() ?? 'Unnamed'
            ];
        }

        return $routeList;
    }

    public function getSidebar(): array
    {
        $routes = self::getRoutes();

        $groupedLinks = [];
        $groupNames = [];

        foreach ($this->filterRoutes($routes) as $route) {
            $name = $route->getName();
            $pattern = $route->getPattern();
            $group = $this->extractGroupFromPattern($pattern);

            // グループ名を設定
            if (!isset($groupNames[$group])) {
                $groupNames[$group] = $this->translateGroupName($group);
            }

            // グループごとにリンクを分類
            $groupedLinks[$group][] = $this->generateLink($name, $group);
        }

        return [
            'groupedLinks' => $groupedLinks,
            'groupNames' => $groupNames,
        ];
    }

    /**
     * Filters the routes to exclude unnecessary ones.
     *
     * @param array $routes The list of routes to filter.
     * @return array The filtered routes.
     */
    private function filterRoutes(array $routes): array
    {
        return array_filter($routes, function ($route) {
            $name = $route->getName();

            // 名前が "dashboard" または "login" のルートはスキップ
            if (!$name || in_array($name, ['dashboard', 'login'])) {
                return false;
            }

            // 名前が "." から始まるルートはスキップ
            if (strpos($name, '.') === 0) {
                return false;
            }

            return true;
        });
    }

    /**
     * Translates the group name.
     *
     * @param string $group The group name.
     * @return string The translated group name.
     */
    private function translateGroupName(string $group): string
    {
        return __("x_management", ':name Management', ['name' => __($group)]);
    }

    /**
     * Generates a single link for the sidebar.
     *
     * @param string $name The route name.
     * @param string $group The group name.
     * @return array The generated link.
     */
    private function generateLink(string $name, string $group): array
    {
        $langLabel = preg_replace('/^[^_]+/', 'x', $name);

        return [
            'name' => __($langLabel, ':name index', ['name' => __($group)]),
            'url' => $this->routeParser->urlFor($name),
            'icon' => 'fa-' . strtolower($name) . '-alt',
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
