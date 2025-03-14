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
}
