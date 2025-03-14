<?php

namespace Jidaikobo\Kontiki\Controllers;

use Slim\App;

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\FormService;
use Jidaikobo\Kontiki\Services\TableService;

class UserController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\IndexAllTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    protected string $adminDirName = 'user';
    protected string $label = 'User';

    private UserModel $model;
    private FormService $formService;
    private TableService $tableService;

    public function __construct(
        App $app,
        FormService $formService,
        TableService $tableService,
        UserModel $model
    ) {
        parent::__construct($app);
        $this->formService = $formService;
        $this->formService->setModel($model);
        $this->tableService = $tableService;
        $this->tableService->setModel($model);
        $this->model = $model;
    }
}
