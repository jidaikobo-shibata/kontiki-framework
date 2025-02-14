<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait CreateEditTrait
{
    public function prepareDataForRenderForm(array $default = []): array
    {
        return $this->flashManager->getData('data', $default);
    }

    public function processDataForRenderForm(string $actionType, array $data): array
    {
        return $data;
    }

    public function renderCreateForm(Request $request, Response $response): Response
    {
        $data = $this->prepareDataForRenderForm();
        $data = $this->processDataForRenderForm('create', $data);

        $fields = $this->model->getFieldDefinitionsWithDefaults($data);
        $fields = $this->model->processFieldDefinitions('create', $fields);

        $formHtml = $this->formService->formHtml(
            "/admin/{$this->table}/create",
            $fields,
            $this->csrfManager->getToken(),
            '',
            __("create", 'Create'),
        );
        $formHtml = $this->formService->addMessages(
            $formHtml,
            $this->flashManager->getData('errors', [])
        );

        return $this->renderResponse(
            $response,
            __("{$this->table}_create", 'Create ' . ucfirst($this->table)),
            $formHtml
        );
    }

    public function renderEditForm(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $this->prepareDataForRenderForm($this->model->getById($id));
        $data = $this->processDataForRenderForm('edit', $data);

        if (!$data) {
            return $this->redirectResponse($request, $response, "/admin/{$this->table}/index");
        }

        $fields = $this->model->getFieldDefinitionsWithDefaults($data);
        $fields = $this->model->processFieldDefinitions('edit', $fields);

        $formHtml = $this->formService->formHtml(
            "/admin/{$this->table}/edit/{$id}",
            $fields,
            $this->csrfManager->getToken(),
            '',
            __("update", 'Update'),
        );
        $formHtml = $this->formService->addMessages(
            $formHtml,
            $this->flashManager->getData('errors', []),
            $this->flashManager->getData('success', [])
        );

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

    public function processDataForSave(string $actionType, array $data): array
    {
        return $data;
    }

    protected function getDefaultRedirect(string $actionType, ?int $id = null): string
    {
        return $actionType === 'create'
            ? "/admin/{$this->table}/create"
            : "/admin/{$this->table}/edit/{$id}";
    }

    protected function getFieldDefinitionsForAction(string $actionType, ?int $id = null): array
    {
        $fields = $actionType === 'create'
            ? $this->model->getFieldDefinitions()
            : $this->model->getFieldDefinitions(['id' => $id]);

        return $this->model->processFieldDefinitions($actionType, $fields);
    }

    protected function saveData(string $actionType, ?int $id, array $data): int
    {
        $data = $this->processDataForSave($actionType, $data);

        if ($actionType === 'create') {
            $newId = $this->model->create($data);
            if ($newId === null) {
                throw new \RuntimeException('Failed to create record. No ID returned.');
            }
            return $newId;
        }

        if ($actionType === 'edit' && $id !== null) {
            $this->model->update($id, $data);
            return $id;
        }

        throw new \InvalidArgumentException('Invalid action type or missing ID.');
    }

    protected function handleSave(Request $request, Response $response, string $actionType, ?int $id = null): Response
    {
        $data = $request->getParsedBody() ?? [];
        $this->flashManager->setData('data', $data);

        // redirect preview
        if (isset($data['preview']) && $data['preview'] === '1') {
            return $this->redirectResponse($request, $response, "/admin/{$this->table}/preview");
        }

        $defaultRedirect = $this->getDefaultRedirect($actionType, $id);

        // validate csrf token
        $errorResponse = $this->validateCsrfToken($data, $request, $response, $defaultRedirect);
        if ($errorResponse) {
            return $errorResponse;
        }

        // field definition
        $fields = $this->getFieldDefinitionsForAction($actionType, $id);

        // Validate post data
        $validationResult = $this->model->validateByFields($data, $fields);
        if (!$validationResult['valid']) {
            $this->flashManager->addErrors($validationResult['errors']);
            return $this->redirectResponse($request, $response, $defaultRedirect);
        }

        try {
            $id = $this->saveData($actionType, $id, $data);
            $redirectTo = "/admin/{$this->table}/edit/{$id}";
            $this->flashManager->addMessage(
                'success',
                __("x_save_success", ':name Saved successfully.', ['name' => __($this->table)])
            );
            return $this->redirectResponse($request, $response, $redirectTo);
        } catch (\Exception $e) {
            $this->flashManager->addErrors([[$e->getMessage()]]);
            return $this->redirectResponse($request, $response, $defaultRedirect);
        }
    }

    public function preview(Request $request, Response $response): Response
    {
        $data = $this->prepareDataForRenderForm();

        if (!isset($data['title']) || !isset($data['content'])) {
            $content = $this->view->fetch('preview/content.php', [
                'title' => __('cannot_preview_title', 'Cannot Render Preview'),
                'content' => __('cannot_preview_desc', 'Preview cannot be reloaded. Please close the preview window and preview again.'),
            ]);
        } else {
            $content = $this->view->fetch('preview/content.php', [
                'title' => $data['title'],
                'content' => $data['content'],
            ]);
        }

        return $this->renderResponse(
            $response,
            __("preview"),
            $content,
            'preview/layout.php'
        );
    }
}
