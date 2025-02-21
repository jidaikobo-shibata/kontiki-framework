<?php

namespace Jidaikobo\Kontiki\Controllers\FileControllerTraits;

use Jidaikobo\Log;
use Jidaikobo\Kontiki\Utils\MessageUtils;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait CRUDTrait
{
    public function getCsrfToken(Request $request, Response $response): Response
    {
        $data = ['csrf_token' => $this->csrfManager->getToken()];
        return $this->jsonResponse($response, $data);
    }

    /**
     * Handles file upload via an AJAX request.
     * This method processes the uploaded file, moves it to the specified directory,
     * and returns a JSON response indicating the result of the operation.
     *
     * @return Response
     *
     * @throws Exception If there is an issue with moving the uploaded file
     */
    public function handleFileUpload(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody() ?? [];

        // CSRF Token validation
        $errorResponse = $this->validateCsrfForJson($parsedBody, $response);
        if ($errorResponse) {
            return $errorResponse;
        }

        // prepare file
        $uploadedFile = $this->prepareUploadedFile($request);
        if (!$uploadedFile) {
            return $this->errorResponse($response, $this->getMessages()['file_missing'], 400);
        }

        // upload file
        $uploadResult = $this->fileService->upload($uploadedFile);
        if (!$uploadResult['success']) {
            return $this->errorResponse($response, $this->getMessages()['upload_error'], 500);
        }

        // update database
        $fileData = ['path' => $uploadResult['path']];
        if ($validationError = $this->validateAndSave($fileData, $response)) {
            return $validationError;
        }

        // success
        return $this->successResponseHtml($response, $this->getMessages()['upload_success']);
    }

    protected function prepareUploadedFile(Request $request): ?array
    {
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['attachment'] ?? null;

        if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            return [
                'name' => $uploadedFile->getClientFilename(),
                'type' => $uploadedFile->getClientMediaType(),
                'tmp_name' => $uploadedFile->getStream()->getMetadata('uri'),
                'size' => $uploadedFile->getSize(),
            ];
        }

        return null;
    }

    private function validateAndSave(array $fileData, Response $response): ?Response
    {
        $fields = $this->model->getFieldDefinitions();
        $fields = $this->model->processFieldDefinitions('create', $fields);
        $validationResult = $this->model->validateByFields($fileData, $fields);

        if (!$validationResult['valid']) {
            return $this->messageResponse(
                $response,
                MessageUtils::errorHtml($validationResult['errors'], $this->model),
                405
            );
        }
        if (!$this->model->create($fileData)) {
            return $this->errorResponse(
                $response,
                $this->getMessages()['database_update_failed'],
                500
            );
        }
        return null;
    }

    /**
     * Handles the AJAX request to update a file's data in the database.
     * Validates the CSRF token, retrieves the file details by ID,
     * updates the file information, and returns a JSON response indicating success or failure.
     *
     * @return Response
     */
    public function handleUpdate(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody() ?? [];

        // CSRF Token validation
        $errorResponse = $this->validateCsrfForJson($parsedBody, $response);
        if ($errorResponse) {
            return $errorResponse;
        }

        // Get the file ID from the POST request
        $fileId = $parsedBody['id'] ?? 0; // Default to 0 if no ID is provided

        // Retrieve the file details from the database using the file ID
        $data = $this->model->getById($fileId);

        if (!$data) {
            $message = $this->getMessages()['file_not_found'];
            return $this->messageResponse($response, $message, 405);
        }

        // Update the description field
        $data['description'] = $parsedBody['description'] ?? $data['description'];

        // Update the main item
        $result = $this->update($data, $fileId);

        if ($result['success']) {
            $message = $this->getMessages()['update_success'];
            return $this->messageResponse($response, $message, 200);
        } else {
            $message = MessageUtils::errorHtml($result['errors'], $this->model);
            return $this->messageResponse($response, $message, 405);
        }
    }

    /**
     * Validate and process data for create or edit actions.
     *
     * @param array $data The input data.
     * @param int|null $id The ID of the item to update (required for edit).
     * @return array The result containing 'success' (bool) and 'errors' (array).
     */
    protected function update(array $data, int $id = null)
    {
        $fields = $this->model->getFieldDefinitions();
        $fields = $this->model->processFieldDefinitions('edit', $fields);
        $results = $this->model->validateByFields($data, $fields);

        if ($results['valid'] !== true) {
            if (isset($results['errors']['description'])) {
                $newKey = 'eachDescription_' . $id;
                $results['errors']['description']['htmlName'] = $newKey;
            }

            return [
                'success' => false,
                'errors' => $results['errors']
            ];
        }

        // Process if valid
        $success = $this->model->update($id, $data);

        return [
            'success' => $success,
            'errors' => $success ? [] : ["Failed to update item."]
        ];
    }

    /**
     * Handles the deletion of a file via AJAX.
     *
     * This method validates the CSRF token, checks the POST request for the file ID,
     * retrieves the file from the database, deletes the corresponding file from the server,
     * and updates the database to remove the file record.
     * If any of these steps fail, an appropriate error message is returned as a JSON response.
     *
     * @return Response
     * @throws ResponseException If there is an error during the deletion process.
     */
    public function handleDelete(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody() ?? [];

        // CSRF Token validation
        $errorResponse = $this->validateCsrfForJson($parsedBody, $response);
        if ($errorResponse) {
            return $errorResponse;
        }

        // Get the file ID from the POST request
        $fileId = $parsedBody['id'] ?? 0; // Default to 0 if no ID is provided

        // Retrieve the file details from the database using the file ID
        $data = $this->model->getById($fileId);

        if (!$data) {
            $message = $this->getMessages()['file_not_found'];
            return $this->messageResponse($response, $message, 405);
        }

        // Delete the file from the server
        $filePath = $data['path'];

        if ($this->deleteFileFromSystem($filePath)) {
            Log::write("File deleted: " . $filePath);
        } else {
            $message = $this->getMessages()['file_delete_failed'];
            return $this->messageResponse($response, $message, 500);
        }

        // Remove the file record from the database
        $deleteSuccess = $this->model->delete($fileId);
        if (!$deleteSuccess) {
            $message = $this->getMessages()['db_update_failed'];
            return $this->messageResponse($response, $message, 500);
        }

        // Send a success response back
        $message = $this->getMessages()['file_delete_success'];
        return $this->messageResponse($response, $message, 200);
    }

    private function deleteFileFromSystem(string $filePath): bool
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
