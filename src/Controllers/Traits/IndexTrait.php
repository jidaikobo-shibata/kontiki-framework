<?php

namespace jidaikobo\kontiki\Controllers\Traits;

use jidaikobo\kontiki\Utils\Lang;
use jidaikobo\kontiki\Utils\TableHandler;
use jidaikobo\kontiki\Utils\TableRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexTrait
{
    /**
     * Register routes for this trait.
     *
     * @param App $app
     * @param string $basePath
     * @param string $controllerClass
     */
    public static function registerRoutesForTrait(App $app, string $basePath, string $controllerClass): void
    {
        $app->group($basePath, function (RouteCollectorProxy $group) use ($controllerClass) {
            $group->get('/index', [$controllerClass, 'index'])->setName("{$basePath}_index");
        });
    }

    public function index(Request $request, Response $response): Response
    {
        $error = $this->flashManager->getData('errors', []);
        $success = $this->flashManager->getData('success', []);
        $data = $this->model->getAll();

        $tableRenderer = new TableRenderer($this->model, $data, $this->view);
        $content = $tableRenderer->render();

        $tableHandler = new TableHandler();

        if (!empty($error)) {
            $content = $tableHandler->addErrors($content, $error);
        }

        if (!empty($success)) {
            $content = $tableHandler->addSuccessMessages($content, $success);
        }

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => Lang::get("{$this->table}_management", ucfirst($this->table) . ' Management'),
                'content' => $content,
            ]
        );
    }
}
