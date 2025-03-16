<?php

namespace Jidaikobo\Kontiki\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

use Jidaikobo\Kontiki\Managers\CsrfManager;
use Jidaikobo\Kontiki\Managers\FlashManager;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\RoutesService;
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
        CsrfManager $csrfManager,
        FlashManager $flashManager,
        PhpRenderer $view,
        RoutesService $routesService,
        FormService $formService,
        TableService $tableService,
        UserModel $model
    ) {
        parent::__construct(
            $csrfManager,
            $flashManager,
            $view,
            $routesService
        );
        $this->formService = $formService;
        $this->formService->setModel($model);
        $this->tableService = $tableService;
        $this->tableService->setModel($model);
        $this->model = $model;
    }

    public function canDelete(
        Request $request,
        Response $response,
        array $args
    ): ?Response {
        $id = $args['id'];
        $data = $this->model->getById($id);

        if ($data['role'] !== 'admin') return null;
        $this->flashManager->addErrors([['messages' => [__('cannot_delete_admin')]]]);

        return $this->redirectResponse(
            $request,
            $response,
            "/{$this->adminDirName}/index"
        );
    }
}
