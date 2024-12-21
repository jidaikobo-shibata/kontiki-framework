<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Models\Post as PostModel;
use jidaikobo\kontiki\Services\SidebarService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class PostController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    protected string $modelClass = '\\jidaikobo\\kontiki\\Models\\Post';

    public function __construct(
        PhpRenderer $view,
        SidebarService $sidebarService,
        Session $session,
        PostModel $model
    ) {
        parent::__construct($view, $sidebarService, $session, $model);
    }

    protected static function getBasePath(): string
    {
        return 'posts';
    }
}
