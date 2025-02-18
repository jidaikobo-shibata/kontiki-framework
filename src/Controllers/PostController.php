<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Models\PostModel;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

class PostController extends BaseController
{
    // use order affects the menu order...
    use Traits\IndexTrait;
    use Traits\DeleteTrait;
    use Traits\FrontendIndexTrait;
    use Traits\FrontendReadTrait;
    use Traits\IndexPublishedTrait;
    use Traits\IndexDraftTrait;
    use Traits\IndexReservedTrait;
    use Traits\IndexExpiredTrait;
    use Traits\MarkdownHelpTrait;
    use Traits\TrashRestoreTrait;
    use Traits\CreateEditTrait;
    use Traits\PreviewTrait;

    public function __construct(
        PhpRenderer $view,
        Session $session,
        PostModel $model,
        GetRoutesService $getRoutesService
    ) {
        parent::__construct($view, $session, $model, $getRoutesService);

        $previewPath = file_exists(KONTIKI_PROJECT_PATH . '/app/views/post') ?
            KONTIKI_PROJECT_PATH . '/app/views/post' :
            KONTIKI_PROJECT_PATH . '/src/views/post' ;
        $this->previewRenderer = new PhpRenderer($previewPath);
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
