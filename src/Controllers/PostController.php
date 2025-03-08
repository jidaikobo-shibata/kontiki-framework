<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\PostModel;
use Jidaikobo\Kontiki\Services\AuthService;
use Slim\App;

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

    protected function setModel(): void
    {
        $db = Database::getInstance()->getConnection();
        $this->model = new PostModel($db, $this->app->getContainer()->get(AuthService::class));
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        // set admin routes
        parent::registerRoutes($app, $basePath);

        // set frontend routes
        $controllerClass = static::class;
        // $app->get('/' . $basePath . '/index', $controllerClass . ':frontendIndex');
        // $app->get('/' . $basePath . '/slug/{slug}', $controllerClass . ':frontendReadBySlug');
    }
}
