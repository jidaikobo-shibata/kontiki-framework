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
            return $this->redirectResponse($request, $response, "{$this->postType}_index");
        }

        $data = $this->model->getFieldDefinitionsWithDefaults($data);
        $data = $this->processFieldForDelete($data);

        $formHtml = $this->formService->formHtml(
            "/admin/{$this->postType}/delete/{$id}",
            $data,
            $this->csrfManager->getToken(),
            __(
                "x_delete_confirm",
                "Are you sure you want to delete this :name?",
                ['name' => __($this->postType)]
            ),
            __("delete", "Delete"),
        );
        $formHtml = $this->formService->addMessages(
            $formHtml,
            $this->flashManager->getData('errors', [])
        );

        return $this->renderResponse(
            $response,
            __(
                "x_delete",
                "Delete :name",
                ['name' => __($this->postType)]
            ),
            $formHtml
        );
    }

    public function handleDelete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $data = $request->getParsedBody() ?? [];

        // validate csrf token
        $redirectTo = "/admin/{$this->postType}/delete/{$id}";
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
                        ['name' => __($this->postType)]
                    )
                );
                return $this->redirectResponse($request, $response, "/admin/{$this->postType}/index");
            }
        } catch (\Exception $e) {
            $this->flashManager->addErrors([
                __("x_delete_failed", "Failed to delete :name", ['name' => __($this->postType)])
              ]);
        }

        $redirectTo = "/admin/{$this->postType}/edit/{$id}";
        return $this->redirectResponse($request, $response, $redirectTo);
    }
}
