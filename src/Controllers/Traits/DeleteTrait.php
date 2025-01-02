<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

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
            __(
                "x_delete_confirm",
                "Are you sure you want to delete this :name?",
                ['name' => __($this->table)]
            ),
            __("delete", "Delete"),
        );
        $formHtml = $this->formService->processFormHtml($formHtml);

        return $this->renderResponse(
            $response,
            __(
                "x_delete",
                "Delete :name",
                ['name' => __($this->table)]
            ),
            $formHtml
        );
    }

    public function handleDelete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $this->getParsedBody($request);

        // validate csrf token
        $redirectTo = "/admin/{$this->table}/delete/{$id}";
        $redirectResponse = $this->validateCsrfToken($data, $request, $response, $redirectTo);
        if ($redirectResponse) {
            return $redirectResponse;
        }

        // delete data
        try {
            if ($this->model->delete($id)) {
                $this->flashManager->addMessage(
                    'success',
                    __(
                        "x_delete_success",
                        ":name deleted successfully.",
                        ['name' => __($this->table)]
                    )
                );
                return $this->redirectResponse($request, $response, "/admin/{$this->table}/index");
            }
        } catch (\Exception $e) {
            $this->flashManager->addErrors([
                __("x_delete_failed", "Failed to delete :name", ['name' => __($this->table)])
              ]);
        }

        $redirectTo = "/admin/{$this->table}/edit/{$id}";
        return $this->redirectResponse($request, $response, $redirectTo);
    }
}
