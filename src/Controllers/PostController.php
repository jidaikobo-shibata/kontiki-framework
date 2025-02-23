<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\PostModel;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Views\PhpRenderer;

class PostController extends BaseController
{
    use Traits\FrontendIndexTrait;
    use Traits\FrontendReadTrait;
    use Traits\IndexTrait;
    use Traits\IndexAllTrait;
    use Traits\IndexPublishedTrait;
    use Traits\IndexDraftTrait;
    use Traits\IndexReservedTrait;
    use Traits\IndexExpiredTrait;
    use Traits\CreateEditTrait;
    use Traits\TrashRestoreTrait;
    use Traits\DeleteTrait;
    use Traits\MarkdownHelpTrait;
    use Traits\PreviewTrait;

    protected string $adminDirName = 'post';
    protected string $label = 'Post';
    protected PostModel $model;

    public function __construct(
        PhpRenderer $view,
        Session $session,
        GetRoutesService $getRoutesService
    ) {
        parent::__construct($view, $session, $getRoutesService);
    }

    protected function setModel(): void
    {
        $db = Database::getInstance()->getConnection();
        $this->model = new PostModel($db);
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        // set admin routes
        parent::registerRoutes($app, $basePath);

        // set frontend routes
        $app->get('/' . $basePath . '/index', PostController::class . ':frontendIndex');
        $app->get('/' . $basePath . '/slug/{slug}', PostController::class . ':frontendReadBySlug');
    }
}
