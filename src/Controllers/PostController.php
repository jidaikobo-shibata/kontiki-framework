<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Models\PostModel;
use Jidaikobo\Kontiki\Services\SidebarService;
use Slim\Views\PhpRenderer;

class PostController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    public function __construct(
        PhpRenderer $view,
        SidebarService $sidebarService,
        Session $session,
        PostModel $model
    ) {
        parent::__construct($view, $sidebarService, $session, $model);
    }
}
