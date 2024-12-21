<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Models\User as UserModel;
use jidaikobo\kontiki\Services\SidebarService;
use Slim\Views\PhpRenderer;

class UserController extends BaseController
{
    use Traits\IndexTrait;
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

    public function prepareCreateEditData($default): Array
    {
        $data = $this->flashManager->getData('data', $default);
        unset($data['password']);
        return $data;
    }
}
