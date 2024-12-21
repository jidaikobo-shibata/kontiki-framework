<?php

namespace jidaikobo\kontiki\Controllers\Traits;

use jidaikobo\kontiki\Utils\Env;
use jidaikobo\kontiki\Utils\Lang;
use jidaikobo\kontiki\Utils\Pagination;
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

        $currentPage = (int)($request->getQueryParams()['paged'] ?? 1);
        $itemsPerPage = 10;
        $pagination = new Pagination($currentPage, $itemsPerPage);

        $keyword = $request->getQueryParams()['s'] ?? '';
        $totalItems = $this->model->countByKeyword($keyword);

        $pagination->setTotalItems($totalItems);

        $data = $this->model->search($keyword, $pagination->getOffset(), $pagination->getLimit());

        $tableRenderer = new TableRenderer($this->model, $data, $this->view);
        $content = $tableRenderer->render();

        $tableHandler = new TableHandler();

        if (!empty($error)) {
            $content = $tableHandler->addErrors($content, $error);
        }

        if (!empty($success)) {
            $content = $tableHandler->addSuccessMessages($content, $success);
        }
        $content.= $pagination->render(Env::get('BASEPATH') . "/admin/{$this->table}/index");

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
