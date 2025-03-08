<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Controllers\FileControllerTraits;
use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\FileModel;
use Jidaikobo\Kontiki\Services\FileService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class FileController extends BaseController
{
    use FileControllerTraits\CRUDTrait;
    use FileControllerTraits\JavaScriptTrait;
    use FileControllerTraits\ListTrait;
    use FileControllerTraits\MessagesTrait;

    protected FileService $fileService;
    protected FileModel $model;

    public function __construct(App $app, FileService $fileService)
    {
        parent::__construct($app);
        $this->fileService = $fileService;
    }

    protected function setModel(): void
    {
        $db = Database::getInstance()->getConnection();
        $this->model = new FileModel($db);
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        $app->get('/get_csrf_token', FileController::class . ':callGetCsrfToken');
        $app->get('/filelist', FileController::class . ':callFilelist');
        $app->post('/upload', FileController::class . ':callHandleFileUpload');
        $app->post('/update', FileController::class . ':callHandleUpdate');
        $app->post('/delete', FileController::class . ':callHandleDelete');
        $app->get('/fileManager.js', FileController::class . ':callServeJs');
        $app->get('/fileManagerInstance.js', FileController::class . ':callServeInstanceJs');
    }

    public function callGetCsrfToken(Request $request, Response $response): Response
    {
        return $this->getCsrfToken($request, $response);
    }

    public function callFilelist(Request $request, Response $response): Response
    {
        return $this->filelist($request, $response);
    }

    public function callHandleFileUpload(Request $request, Response $response): Response
    {
        return $this->handleFileUpload($request, $response);
    }

    public function callHandleUpdate(Request $request, Response $response): Response
    {
        return $this->handleUpdate($request, $response);
    }

    public function callHandleDelete(Request $request, Response $response): Response
    {
        return $this->handleDelete($request, $response);
    }

    public function callServeJs(Request $request, Response $response): Response
    {
        return $this->serveJs($request, $response);
    }

    public function callServeInstanceJs(Request $request, Response $response): Response
    {
        return $this->serveInstanceJs($request, $response);
    }
}
