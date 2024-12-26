<?php

namespace jidaikobo\kontiki\Controllers\Traits;

use jidaikobo\kontiki\Utils\Lang;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait DeleteTrait
{
    public function processFieldForDelete(array $data): array
    {
        foreach ($data as &$field) {
            $field['attributes']['readonly'] = 'readonly';

            $existingClass = $field['attributes']['class'] ?? '';
            $field['attributes']['class'] = trim($existingClass . ' form-control-plaintext p-2');

            $field['description'] = '';
        }
        unset($field);

        return $data;
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $this->model->getById($id);

        if (!$data) {
            return $this->redirectResponse($request, $response, "{$this->table}_index");
        }

        $data = $this->model->getFieldDefinitionsWithDefaults($data);
        $data = $this->processFieldForDelete($data);

        $formHtml = $this->formService->formHtml(
            "/admin/{$this->table}/delete/{$id}",
            $data,
            Lang::get("confirm_delete_message", "Are you sure you want to delete this {$this->table}?"),
            Lang::get("delete", "Delete"),
        );
        $formHtml = $this->formService->processFormHtml($formHtml);

        return $this->renderResponse(
            $response,
            Lang::get("delete_{$this->table}", "Delete " . ucfirst($this->table)),
            $formHtml
        );
    }

    public function handleDelete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody();

        // validate csrf token
        $redirectTo = "/admin/{$this->table}/delete/{$id}";
        $redirectResponse = $this->validateCsrfToken($data, $request, $response, $redirectTo);
        if ($redirectResponse) {
            return $redirectResponse;
        }

        // データ削除
        try {
            if ($this->model->delete($id)) {
                $this->flashManager->addMessage(
                  'success',
                  Lang::get("{$this->table}_delete_success", ucfirst($this->table) . " deleted successfully.")
                );
                return $this->redirectResponse($request, $response, "/admin/{$this->table}/index");
            }
        } catch (\Exception $e) {
            $this->flashManager->addErrors([Lang::get("{$this->table}_delete_failed", "Failed to delete " . ucfirst($this->table) . ".")]);
        }

        $redirectTo = "/admin/{$this->table}/edit/{$id}";
        return $this->redirectResponse($request, $response, $redirectTo);
    }
}
