<?php

namespace Jidaikobo\Kontiki\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Views\PhpRenderer;

use Jidaikobo\Kontiki\Controllers\FileControllerTraits;
use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Managers\CsrfManager;
use Jidaikobo\Kontiki\Managers\FlashManager;
use Jidaikobo\Kontiki\Models\FileModel;
use Jidaikobo\Kontiki\Services\RoutesService;
use Jidaikobo\Kontiki\Services\FileService;

class FileController extends BaseController
{
    use FileControllerTraits\CRUDTrait;
    use FileControllerTraits\JavaScriptTrait;
    use FileControllerTraits\ListTrait;
    use FileControllerTraits\MessagesTrait;

    private FileModel $model;
    private FileService $fileService;

    public function __construct(
        CsrfManager $csrfManager,
        FlashManager $flashManager,
        PhpRenderer $view,
        RoutesService $routesService,
        FileModel $model,
        FileService $fileService
    ) {
        parent::__construct(
            $csrfManager,
            $flashManager,
            $view,
            $routesService
        );
        $this->model = $model;
        $this->fileService = $fileService;
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
