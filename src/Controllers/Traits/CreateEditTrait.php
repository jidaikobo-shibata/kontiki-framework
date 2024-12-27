<?php

namespace jidaikobo\kontiki\Controllers\Traits;

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

        $fields = $this->model->getFieldDefinitionsWithDefaults($data);
        $fields = $this->model->processCreateFieldDefinitions($fields);

        $formHtml = $this->formService->formHtml(
            "/admin/{$this->table}/create",
            $fields,
            '',
            __("create", 'Create'),
        );
        $formHtml = $this->formService->processFormHtml($formHtml);

        return $this->renderResponse(
            $response,
            __("{$this->table}_create", 'Create ' . ucfirst($this->table)),
            $formHtml
        );
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $this->prepareCreateEditData($this->model->getById($id));

        if (!$data) {
            return $this->redirectResponse($request, $response, "/admin/{$this->table}/index");
        }

        $fields = $this->model->getFieldDefinitionsWithDefaults($data);
        $fields = $this->model->processEditFieldDefinitions($fields);

        $formHtml = $this->formService->formHtml(
            "/admin/{$this->table}/edit/{$id}",
            $fields,
            '',
            __("update", 'Update'),
        );
        $formHtml = $this->formService->processFormHtml($formHtml);

        return $this->renderResponse(
            $response,
            __("{$this->table}_edit", 'Edit ' . ucfirst($this->table)),
            $formHtml
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

        // validate csrf token
        $redirectResponse = $this->validateCsrfToken($data, $request, $response, $defaultRedirect);
        if ($redirectResponse) {
            return $redirectResponse;
        }

        // field definition
        $fields = $actionType === 'create'
            ? $this->model->getFieldDefinitions()
            : $this->model->getFieldDefinitions(['id' => $id]);
        $fields = $actionType === 'create'
            ? $this->model->processCreateFieldDefinitions($fields)
            : $this->model->processEditFieldDefinitions($fields);

        // Validate post data
        $validationResult = $this->model->validateByFields($data, $fields);
        if (!$validationResult['valid']) {
            $this->flashManager->addErrors($validationResult['errors']);
            return $this->redirectResponse($request, $response, $defaultRedirect);
        }

        try {
            if ($actionType === 'create') {
                $id = $this->model->create($data);
                if ($id === null) {
                    throw new \RuntimeException('Failed to create record. No ID returned.');
                }
            } elseif ($actionType === 'edit' && $id !== null) {
                $this->model->update($id, $data);
            }

            $this->flashManager->addMessage(
                'success',
                __("x_save_success", ':name Saved successfully.', ['name' => __($this->table)])
            );

            return $this->redirectResponse($request, $response, $defaultRedirect);
        } catch (\Exception $e) {
            $this->flashManager->addErrors([[$e->getMessage()]]);
            return $this->redirectResponse($request, $response, $defaultRedirect);
        }
    }
}
