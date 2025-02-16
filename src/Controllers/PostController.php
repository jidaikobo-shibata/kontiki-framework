<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Models\PostModel;
use Jidaikobo\Kontiki\Services\SidebarService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

class PostController extends BaseController
{
    // use order affects the menu order...
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;
    use Traits\FrontendIndexTrait;
    use Traits\FrontendReadTrait;
    use Traits\IndexTrait;
    use Traits\IndexNormalTrait;
    use Traits\IndexDraftTrait;
    use Traits\IndexReservedTrait;
    use Traits\IndexExpiredTrait;
    use Traits\MarkdownHelpTrait;
    use Traits\TrashRestoreTrait;

    public function __construct(
        PhpRenderer $view,
        Session $session,
        PostModel $model,
        SidebarService $sidebarService
    ) {
        parent::__construct($view, $session, $model, $sidebarService);
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        // set admin routes
        parent::registerRoutes($app, $basePath);

        // set frontend routes
        $controllerClass = static::class;
        $app->get('/' . $basePath . '/index',  [$controllerClass, 'frontendIndex']);
        $app->get('/' . $basePath . '/slug/{slug}',  [$controllerClass, 'frontendReadBySlug']);
    }
}
