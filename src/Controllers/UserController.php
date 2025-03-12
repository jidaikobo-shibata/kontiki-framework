<?php

namespace Jidaikobo\Kontiki\Controllers;

use Slim\App;

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\FormService;

class UserController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\IndexAllTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    protected string $adminDirName = 'user';
    protected string $label = 'User';

    private FormService $formService;
    private UserModel $model;

    public function __construct(
        App $app,
        FormService $formService,
        UserModel $model
    ) {
        parent::__construct($app);
        $this->formService = $formService;
        $this->formService->setModel($model);
        $this->model = $model;
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
