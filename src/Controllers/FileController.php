<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Controllers\FileControllerTraits;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Models\FileModel;
use Jidaikobo\Kontiki\Services\FileService;
use Jidaikobo\Kontiki\Utils\CsrfManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

class FileController extends BaseController
{
    use FileControllerTraits\CRUDTrait;
    use FileControllerTraits\JavaScriptTrait;
    use FileControllerTraits\ListTrait;
    use FileControllerTraits\MessagesTrait;

    protected PhpRenderer $view;
    protected FileModel $fileModel;
    protected FileService $fileService;
    protected CsrfManager $csrfManager;

    public function __construct(Session $session, PhpRenderer $view, FileModel $fileModel, FileService $fileService)
    {
        parent::__construct($view, $session, $fileModel);
        $this->fileService = $fileService;
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        $app->group(
            '/admin',
            function (RouteCollectorProxy $group) {
                $group->get('/get_csrf_token', [FileController::class, 'callGetCsrfToken']);
                $group->get('/filelist', [FileController::class, 'callFilelist']);
                $group->post('/upload', [FileController::class, 'callHandleFileUpload']);
                $group->post('/update', [FileController::class, 'callHandleUpdate']);
                $group->post('/delete', [FileController::class, 'callHandleDelete']);
                $group->get('/fileManager.js', [FileController::class, 'callServeJs']);
                $group->get('/fileManagerInstance.js', [FileController::class, 'callServeInstanceJs']);
            }
        )->add(AuthMiddleware::class);
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
