<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Jidaikobo\Kontiki\Services\FormService;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait CreateEditTrait
{
    private array $pendingKVSData;

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
        $fields = $this->model->processFieldDefinitionsForSave('create', $fields);

        $formService = new FormService($this->view, $this->model);
        $formHtml = $formService->formHtml(
            "/admin/{$this->adminDirName}/create",
            $fields,
            $this->csrfManager->getToken(),
            '',
            __("x_save", 'Save :name', ['name' => __($this->label)]),
        );
        $formHtml = $formService->addMessages(
            $formHtml,
            $this->flashManager->getData('errors', [])
        );

        return $this->renderResponse(
            $response,
            __("x_create", 'Create :name', ['name' => __($this->label)]),
            $formHtml
        );
    }

    public function renderEditForm(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $this->prepareDataForRenderForm($this->model->getById($id));
        $data = $this->processDataForRenderForm('edit', $data);

        if (!$data) {
            return $this->redirectResponse($request, $response, "/admin/{$this->adminDirName}/index");
        }

        $fields = $this->model->getFieldDefinitionsWithDefaults($data);
        $fields = $this->model->processFieldDefinitionsForSave('edit', $fields);

        $formService = new FormService($this->view, $this->model);
        $formHtml = $formService->formHtml(
            "/admin/{$this->adminDirName}/edit/{$id}",
            $fields,
            $this->csrfManager->getToken(),
            '',
            __("x_save", 'Save :name', ['name' => __($this->label)]),
        );
        $formHtml = $formService->addMessages(
            $formHtml,
            $this->flashManager->getData('errors', []),
            $this->flashManager->getData('success', [])
        );

        return $this->renderResponse(
            $response,
            __("x_edit", 'Edit :name', ['name' => __($this->label)]),
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
            ? "/admin/{$this->adminDirName}/create"
            : "/admin/{$this->adminDirName}/edit/{$id}";
    }

    protected function getFieldDefinitionsForAction(string $actionType, ?int $id = null): array
    {
        $fields = $actionType === 'create'
            ? $this->model->getFieldDefinitions()
            : $this->model->getFieldDefinitions(['id' => $id]);

        return $this->model->processFieldDefinitionsForSave($actionType, $fields);
    }

    protected function saveData(string $actionType, ?int $id, array $data): int
    {
        $data = $this->processDataForSave($actionType, $data);
        $data = $this->divideKVS($data);

        if ($actionType === 'create') {
            $newId = $this->model->create($data);
            if ($newId === null) {
                throw new \RuntimeException('Failed to create record. No ID returned.');
            }
            $this->saveKVS($newId);
            return $newId;
        }

        if ($actionType === 'edit' && $id !== null) {
            $this->model->update($id, $data);
            $this->saveKVS($id);
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
            return $this->redirectResponse($request, $response, "/admin/{$this->adminDirName}/preview");
        }

        $defaultRedirect = $this->getDefaultRedirect($actionType, $id);

        // validate csrf token
        $errorResponse = $this->validateCsrfToken($data, $request, $response, $defaultRedirect);
        if ($errorResponse) {
            return $errorResponse;
        }

        // Validate post data
        if (!$this->isValidData($data, $actionType, $id)) {
            return $this->redirectResponse($request, $response, $defaultRedirect);
        }

        return $this->processAndRedirect($request, $response, $actionType, $id, $data);
    }

    /**
     * Validate input data against the field definitions.
     */
    private function isValidData(array $data, string $actionType, ?int $id): bool
    {
        $fields = $this->getFieldDefinitionsForAction($actionType, $id);
        $validationResult = $this->model->validateByFields($data, $fields);

        if (!$validationResult['valid']) {
            $this->flashManager->addErrors($validationResult['errors']);
            return false;
        }

        return true;
    }

    /**
     * Process the save operation and handle redirection.
     */
    private function processAndRedirect(
        Request $request,
        Response $response,
        string $actionType,
        ?int $id,
        array $data
    ): Response {
        try {
            $id = $this->saveData($actionType, $id, $data);
            $this->flashManager->addMessage(
                'success',
                __("x_save_success", ':name Saved successfully.', ['name' => __($this->label)])
            );
            return $this->redirectResponse($request, $response, "/admin/{$this->adminDirName}/edit/{$id}");
        } catch (\Exception $e) {
            $this->flashManager->addErrors([[$e->getMessage()]]);
            return $this->redirectResponse($request, $response, $this->getDefaultRedirect($actionType, $id));
        }
    }

    protected function divideKVS(array $data): array
    {
        $kvsData = [];
        foreach ($this->model->getKVSFieldDefinitions() as $key => $definition) {
            if (isset($data[$key])) {
                $kvsData[$key] = $data[$key];
                unset($data[$key]);
            }
        }

        if (!empty($kvsData)) {
            $this->pendingKVSData = $kvsData;
        }

        return $data;
    }

    protected function saveKVS(int $id): void
    {
        if (empty($this->pendingKVSData)) {
            return;
        }

        foreach ($this->pendingKVSData as $key => $value) {
            $existing = $this->model->getKVS($id, $key);

            if ($value === '' || $value === null) {
                $this->model->deleteKVS($id, $key);
            } else if ($existing !== null) {
                $this->model->updateKVS($id, $key, $value);
            } else {
                $this->model->createKVS($id, $key, $value);
            }
        }
        $this->pendingKVSData = [];
    }
}
