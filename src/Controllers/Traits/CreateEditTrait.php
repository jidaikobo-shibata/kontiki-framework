<?php

namespace jidaikobo\kontiki\Controllers\Traits;

use jidaikobo\kontiki\Utils\Lang;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait CreateEditTrait
{
    public function prepareCreateEditData(array $default = []): Array
    {
        return $this->flashManager->getData('data', $default);
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $this->prepareCreateEditData([]);

        return $this->renderForm(
            $response,
            "/admin/{$this->table}/create",
            Lang::get("{$this->table}_create", 'Create ' . ucfirst($this->table)),
            $this->model->getFieldDefinitionsWithDefaults($data),
            '',
            Lang::get("create", 'Create'),
        );
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $this->prepareCreateEditData($this->model->getById($id));

        if (!$data) {
            return $this->redirect($request, $response, "/admin/{$this->table}/index");
        }

        return $this->renderForm(
            $response,
            "/admin/{$this->table}/edit/{$id}",
            Lang::get("{$this->table}_edit", 'Edit ' . ucfirst($this->table)),
            $this->model->getFieldDefinitionsWithDefaults($data),
            '',
            Lang::get("update", 'Update'),
        );
    }

    public function handleCreate(Request $request, Response $response): Response
    {
        return $this->handleSave($request, $response, 'create');
    }

    public function handleEdit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        return $this->handleSave($request, $response, 'edit', $id);
    }

    protected function handleSave(Request $request, Response $response, string $actionType, ?int $id = null): Response
    {
        $data = $request->getParsedBody();
        $this->flashManager->setData('data', $data);

        $defaultRedirect = $actionType === 'create'
            ? "/admin/{$this->table}/create"
            : "/admin/{$this->table}/edit/{$id}";

        if (empty($data['_csrf_value']) || !$this->csrfManager->isValid($data['_csrf_value'])) {
            $this->flashManager->addErrors([
                Lang::get("csrf_invalid", 'Invalid CSRF token.'),
            ]);
            return $this->redirect($request, $response, $defaultRedirect);
        }

        $validationResult = $this->validateData($data);
        if (!$validationResult['valid']) {
            $this->flashManager->addErrors($validationResult['errors']);
            return $this->redirect($request, $response, $defaultRedirect);
        }

        try {
            if ($actionType === 'create') {
                $this->model->create($data);
                $id = $this->model->getLastInsertId();
            } elseif ($actionType === 'edit' && $id !== null) {
                $this->model->update($id, $data);
            }

            $this->flashManager->addMessage(
                'success',
                Lang::get("{$this->table}_save_success", 'Saved successfully.')
            );

            return $this->redirect($request, $response, "/admin/{$this->table}/edit/{$id}");
        } catch (\Exception $e) {
            $this->flashManager->addErrors([
                [$e->getMessage()],
            ]);
            return $this->redirect($request, $response, $defaultRedirect);
        }
    }

    /**
     * Validate the given data using the model's validation logic.
     *
     * @param array $data
     * @return array Validation result with 'valid' and 'errors' keys.
     */
    protected function validateData(array $data): array
    {
        return $this->model->validate($data, $this->model->getFieldDefinitions());
    }
}
