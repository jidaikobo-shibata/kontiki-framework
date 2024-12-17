<?php

namespace jidaikobo\kontiki\Controllers;

use jidaikobo\kontiki\Utils\Lang;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    protected string $modelClass = '\\jidaikobo\\kontiki\\Models\\User';

    public function prepareCreateEditData($default): Array
    {
        $data = $this->flashManager->getData('data', $default);
        unset($data['password']);
        return $data;
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $this->prepareCreateEditData($this->model->getById($id));

        if (!$data) {
            return $this->redirect($request, $response, "/admin/{$this->table}/index");
        }

        $fields = $this->model->processFieldDefinitions($this->model->getFieldDefinitionsWithDefaults($data));

        return $this->renderForm(
            $response,
            "/admin/{$this->table}/edit/{$id}",
            Lang::get("{$this->table}_edit", 'Edit ' . ucfirst($this->table)),
            $fields,
            '',
            Lang::get("update", 'Update'),
        );
    }

    protected static function getBasePath(): string
    {
        return 'users';
    }

    protected function validateData(array $data): array
    {
        $fieldDefinitions = $this->model->getFieldDefinitions();
        $fieldDefinitions = $this->model->processFieldDefinitions($fieldDefinitions);
        return $this->model->validate($data, $fieldDefinitions);
    }
}
