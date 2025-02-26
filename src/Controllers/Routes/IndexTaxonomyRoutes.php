<?php

namespace Jidaikobo\Kontiki\Controllers\Routes;

use Slim\Routing\RouteCollectorProxy;

class IndexTaxonomyRoutes
{
    public static function register(RouteCollectorProxy $group, string $basePath, string $controllerClass): void
    {
        $group->get('/index', [$controllerClass, 'indexTaxonomy'])
            ->setName("{$basePath}|x_index_taxonomy|sidebar,dashboard");
    }
}
