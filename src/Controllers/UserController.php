<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\SidebarService;
use Slim\Views\PhpRenderer;

class UserController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\IndexNormalTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    public function __construct(
        PhpRenderer $view,
        SidebarService $sidebarService,
        Session $session,
        UserModel $model
    ) {
        parent::__construct($view, $sidebarService, $session, $model);
    }

    public function prepareCreateEditData($default): array
    {
        $data = $this->flashManager->getData('data', $default);
        unset($data['password']);
        return $data;
    }
}
