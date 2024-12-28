<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Jidaikobo\Kontiki\Utils\Env;
use Jidaikobo\Kontiki\Utils\Pagination;
use Jidaikobo\Kontiki\Utils\TableHandler;
use Jidaikobo\Kontiki\Utils\TableRenderer;
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
            $content = $tableHandler->addErrors($content, $error, $this->model);
        }

        if (!empty($success)) {
            $content = $tableHandler->addSuccessMessages($content, $success);
        }
        $content .= $pagination->render(Env::get('BASEPATH') . "/admin/{$this->table}/index");

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => __("x_index", 'Index of :name', ['name' => __($this->table)]),
                'content' => $content,
            ]
        );
    }
}
