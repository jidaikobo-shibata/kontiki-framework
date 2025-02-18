<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Slim\Views\PhpRenderer;

class UserController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    public function __construct(
        PhpRenderer $view,
        Session $session,
        UserModel $model,
        GetRoutesService $getRoutesService
    ) {
        parent::__construct($view, $session, $model, $getRoutesService);
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function processDataForRenderForm(string $actionType, array $data): array
    {
        if ($actionType == 'edit') {
            $data['password'] = '';
        }
        return $data;
    }

    public function processDataForSave(string $actionType, array $data): array
    {
        if ($actionType == 'create') {
            $data['password'] = $this->hashPassword($data['password']);
        }

        if ($actionType == 'edit') {
            // Branching password processing
            if (isset($data['password'])) {
                if (trim($data['password']) === '') {
                    unset($data['password']);
                } else {
                    $data['password'] = $this->hashPassword($data['password']);
                }
            }
        }
        return $data;
    }
}
