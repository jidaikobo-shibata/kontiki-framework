<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Middleware\AuthMiddleware;
use jidaikobo\kontiki\Models\FileModel;
use jidaikobo\kontiki\Utils\CsrfManager;
use jidaikobo\kontiki\Utils\Env;
use jidaikobo\kontiki\Utils\Pagination;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

class FileController
{
    protected Session $session;
    protected PhpRenderer $view;
    protected FileModel $fileModel;
    protected CsrfManager $csrfManager;

    public function __construct(Session $session, PhpRenderer $view, FileModel $fileModel)
    {
        $this->csrfManager = new CsrfManager($session);
        $this->session = $session;
        $this->view = $view;
        $this->fileModel = $fileModel;
    }

    public static function registerRoutes(App $app): void
    {
        $app->group(
            '/admin',
            function (RouteCollectorProxy $group) {
                $group->get('/get_csrf_token', [FileController::class, 'getCsrfToken']);
                $group->get('/filelist', [FileController::class, 'filelist']);
                $group->get('/upload', [FileController::class, 'fileUpload']);
                $group->get('/fileManager.js', [FileController::class, 'serveJs']);
                $group->get('/fileManagerInstance.js', [FileController::class, 'serveInstanceJs']);
            }
        )->add(AuthMiddleware::class);
    }

    // Default messages
    protected $messages = [
        'invalid_request' => 'Invalid request. Please try again.',

        'validation_failed' => 'Data validation failed. Please check your input.',
        'upload_success' => 'The file has been successfully uploaded.',
        'upload_error' => 'The file could not be uploaded. Please try again.',
        'database_update_failed' => 'Failed to update the database. Please try again.',
        'file_missing' => 'No file uploaded or the file is corrupted.',
        'method_not_allowed' => 'Method not allowed.',

        'invalid_request' => 'Invalid request. Please try again.',
        'file_not_found' => 'File not found.',
        'update_success' => 'The database has been updated successfully.',
        'update_failed' => 'Failed to update the database. Please try again.',

        'file_id_required' => 'File ID is required.',
        'file_not_found' => 'File not found.',
        'file_delete_failed' => 'Failed to delete the file.',
        'db_update_failed' => 'Failed to update the database.',
        'file_delete_success' => 'File has been deleted successfully.',
        'unexpected_error' => 'An unexpected error occurred. Please try again later.'
    ];

    public function getCsrfToken(Request $request, Response $response): Response
    {
        $token = $this->csrfManager->getToken();
        $data = ['csrf_token' => $token];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(200);
    }

    /**
     * Handles file upload via an AJAX request.
     * This method processes the uploaded file, moves it to the specified directory,
     * and returns a JSON response indicating the result of the operation.
     *
     * @return void
     *
     * @throws Exception If there is an issue with moving the uploaded file or invalid request method.
     */
    public function fileUpload(Request $request, Response $response): Response
    {
        try {
            // CSRF Token validation
            $csrfToken = $request->getParsedBody()['csrf_token'] ?? null;
            if (!$this->csrfManager->validateToken($this->tokenname, $csrfToken)) {
                $data = ['message' => $this->messages['invalid_request']];
                $response->getBody()->write(json_encode($data));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(405);
            }

            // check method
            if ($request->getMethod() !== 'POST') {
                $data = ['message' => $this->messages['method_not_allowed']];
                $response->getBody()->write(json_encode($data));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(405);
            }

            // validation
            $data = $request->getParsedBody();
            $errors = $this->fileModel->validateData($data, false);
            if ($errors !== true) {
                $data = ['message' => generateAllErrorMessagesHtml($errors)];
                $response->getBody()->write(json_encode($data));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }

            // upload
            $uploadedFiles = $request->getUploadedFiles();
            $uploadedFile = $uploadedFiles['attachment'] ?? null;

            if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
                // ファイル情報を取得
                $fileInfo = [
                    'name' => $uploadedFile->getClientFilename(),
                    'type' => $uploadedFile->getClientMediaType(),
                    'tmp_name' => $uploadedFile->getStream()->getMetadata('uri'),
                    'size' => $uploadedFile->getSize(),
                ];

                // ファイルのアップロード処理
                $result = $this->fileUploader->upload($fileInfo);

                if ($result['success']) {
                    $data['path'] = $result['path'];
                    $isDbUpdate = $this->fileModel->createItem($data);

                    if ($isDbUpdate) {
                        $response->getBody()->write(json_encode(['message' => generateStatusSection('success', $this->messages['upload_success'])]));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
                    } else {
                        $response->getBody()->write(json_encode(['message' => generateStatusSection('error', $this->messages['database_update_failed'])]));
                        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                    }
                } else {
                    $response->getBody()->write(json_encode(['message' => generateStatusSection('error', $this->messages['upload_error'])]));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
                }
            } else {
                // アップロードに失敗した場合
                $response->getBody()->write(json_encode(['message' => generateStatusSection('error', $this->messages['file_missing'])]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        } catch (\Exception $e) {
            // 例外処理とエラーログ
            Log::write('Unexpected error in ajaxHandleFileUpload: ' . $e->getMessage(), 'ERROR');
            $response->getBody()->write(json_encode(['message' => $this->messages['invalid_request']]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    /**
     * Handles the AJAX request to update a file's data in the database.
     * Validates the CSRF token, retrieves the file details by ID,
     * updates the file information, and returns a JSON response indicating success or failure.
     *
     * @return void
     */
    public function ajaxHandleFileUpdate()
    {
        try {
            // Check if it's a POST request
            if (!Input::isPostRequest()) {
                return;
            }

            // CSRF Token validation
            if (!Csrf::validateToken($this->tokenname)) {
                Response::sendJson(['message' => $this->messages['invalid_request']], 405);
                return;
            }

            // Get the file ID from the POST request
            $fileId = Input::post('id', 0); // Default to 0 if no ID is provided

            // Retrieve the file details from the database using the file ID
            $data = $this->fileModel->getItemById($fileId);

            if (!$data) {
                Response::sendJson(['message' => $this->messages['file_not_found']], 404);
                return;
            }

            // Update the description field
            $data['description'] = Input::post('description', $data['description']);

            // Update the main item
            $result = $this->update($data, $fileId);

            if ($result['success']) {
                Response::sendJson(['message' => $this->messages['update_success']]);
            } else {
                Response::sendJson(['message' => generateAllErrorMessagesHtml($result['errors'])], 500);
            }
        } catch (\Exception $e) {
            // Log unexpected errors and return a generic error message
            Log::write('Unexpected error in ajaxHandleFileUpdate: ' . $e->getMessage(), 'ERROR');
            Response::sendJson(['message' => $this->messages['invalid_request']], 500);
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
        $isEdit = true;
        $errors = $this->fileModel->validateData($data, $isEdit, $id);

        if ($errors !== true) {
            if (isset($errors['description'])) {
                    $newKey = 'eachDescription_' . $id;
                    $errors[$newKey] = $errors['description'];
                    unset($errors['description']);
            }

            return [
                'success' => false,
                'errors' => $errors
            ];
        }

        // Process if valid
        $success = $this->fileModel->updateItem($id, $data);

        return [
            'success' => $success,
            'errors' => $success ? [] : ["Failed to update item."]
        ];
    }

    /**
     * Handles the AJAX request to fetch the file list.
     *
     * This method retrieves a list of files from the model, applies security headers
     * to the response, and then renders a view to display the file list.
     *
     * @return void
     */
    public function filelist(Request $request, Response $response): Response
    {
        // Initialize Pagination and set total items
        $page = $request->getQueryParams()['page'] ?? 1;
        $itemsPerPage = 10;
        $pagination = new Pagination($page, $itemsPerPage);

        $keyword = $request->getQueryParams()['s'] ?? '';
        $totalItems = $this->fileModel->countByKeyword($keyword);

        $pagination->setTotalItems($totalItems);
        $paginationHtml = $pagination->render(Env::get('BASEPATH') . "/admin/filelist");

        $items = $this->fileModel->search($keyword, $pagination->getOffset(), $pagination->getLimit());

        $content = $this->view->fetch(
            'forms/incFilelist.php',
            [
                'items' => $items,
                'pagination' => $paginationHtml
            ]
        );

        $response->getBody()->write($content);
        return $response->withHeader('Content-Type', 'text/html')->withStatus(200);
    }

    /**
     * Handles the deletion of a file via AJAX.
     *
     * This method validates the CSRF token, checks the POST request for the file ID,
     * retrieves the file from the database, deletes the corresponding file from the server,
     * and updates the database to remove the file record.
     * If any of these steps fail, an appropriate error message is returned as a JSON response.
     *
     * @return void
     * @throws ResponseException If there is an error during the deletion process.
     */
    public function ajaxHandleFileDelete()
    {
        try {
            // CSRF Token validation
            if (!Csrf::validateToken($this->tokenname)) {
                Response::sendJson(['message' => $this->messages['invalid_request']], 405);
                return;
            }

            // Check if it's a POST request
            if (Input::isPostRequest()) {
                // Get the file ID from the POST request
                $fileId = Input::post('id', 0); // Default to 0 if no ID is provided
                if (!$fileId) {
                    Response::sendJson(['message' => $this->messages['file_id_required']], 400);
                    return;
                }

                // Retrieve the file details from the database using the file ID
                $file = $this->fileModel->getItemById($fileId);

                if (!$file) {
                    Response::sendJson(['message' => $this->messages['file_not_found']], 404);
                    return;
                }

                // Delete the file from the server
                $filePath = $file['path'];

                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        Log::write("File deleted: " . $filePath);
                    } else {
                        Response::sendJson(['message' => $this->messages['file_delete_failed']], 500);
                        return;
                    }
                }

                // Remove the file record from the database
                $deleteSuccess = $this->fileModel->hardDelete($fileId);
                if (!$deleteSuccess) {
                    Response::sendJson(['message' => $this->messages['db_update_failed']], 500);
                    return;
                }

                // Send a success response back
                Response::sendJson(['message' => $this->messages['file_delete_success']]);
            }
        } catch (\Exception $e) {
            // Log the exception details for debugging
            Log::write('Unexpected error in ajaxHandleFileDelete: ' . $e->getMessage(), 'ERROR');

            // Send a generic error response to the user
            Response::sendJson(['message' => $this->messages['unexpected_error']], 500);
        }
    }

    /**
     * Serve the requested JavaScript file.
     *
     * @return void
     */
    public function serveJs(Request $request, Response $response): Response
    {
        $filePath = dirname(__DIR__) . '/Views/js/fileManager.js';
        $jsContent = file_get_contents($filePath);
        $response->getBody()->write($jsContent);
        return $response->withHeader('Content-Type', 'application/javascript; charset=utf-8')->withStatus(200);
    }

    /**
     * Serve the requested JavaScript file.
     *
     * @return void
     */
    public function serveInstanceJs(Request $request, Response $response): Response
    {
        $content = $this->view->fetch(
            'js/fileManagerInstance.js.php',
            [
                'basepath' => Env::get('BASEPATH')
            ]
        );
        $response->getBody()->write($content);
        return $response->withHeader('Content-Type', 'application/javascript; charset=utf-8')->withStatus(200);
    }
}
