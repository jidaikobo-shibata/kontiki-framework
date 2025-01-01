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
    use Traits\IndexTrait;
    use Traits\IndexNormalTrait;
    use Traits\IndexExpiredTrait;
    use Traits\IndexReservedTrait;
    use Traits\CreateEditTrait;
    use Traits\TrashRestoreTrait;
    use Traits\DeleteTrait;

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
        parent::registerRoutes($app, $basePath);

        $app->group('/admin/' . $basePath, function (RouteCollectorProxy $group) use ($basePath) {
            $group->get('/index/draft', [PostController::class, 'draftIndex'])->setName("{$basePath}_index_draft");
        })->add(AuthMiddleware::class);
    }

    public function draftIndex(Request $request, Response $response): Response
    {
        // see also PostModel::getAdditionalConditions()
        $this->context = 'draft';
        return static::index($request, $response);
    }
}
