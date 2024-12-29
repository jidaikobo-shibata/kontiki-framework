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
    protected string $context;
    protected string $deleteType;

    public function normalIndex(Request $request, Response $response): Response
    {
        $this->context = 'normal';
        self::isUsesTrashRestoreTrait();
        return static::index($request, $response);
    }

    public function trashIndex(Request $request, Response $response): Response
    {
        $this->context = 'trash';
        self::isUsesTrashRestoreTrait();
        return static::index($request, $response);
    }

    public function reservedIndex(Request $request, Response $response): Response
    {
        $this->context = 'reserved';
        self::isUsesTrashRestoreTrait();
        return static::index($request, $response);
    }

    public function expiredIndex(Request $request, Response $response): Response
    {
        $this->context = 'expired';
        self::isUsesTrashRestoreTrait();
        return static::index($request, $response);
    }

    protected function isUsesTrashRestoreTrait(): void
    {
        $usesTrashRestoreTrait = in_array(
            TrashRestoreTrait::class,
            class_uses($this)
        );
        $this->deleteType = $usesTrashRestoreTrait ? 'softDelete' : 'hardDelete';
    }

    public function index(Request $request, Response $response): Response
    {
        $additionalConditions = $this->model->getAdditionalConditions($this->context, $this->deleteType);

        // set pagination
        $currentPage = (int)($request->getQueryParams()['paged'] ?? 1);
        $itemsPerPage = 10;
        $pagination = new Pagination($currentPage, $itemsPerPage);

        $keyword = $request->getQueryParams()['s'] ?? '';
        $conditions = $this->model->buildSearchConditions($keyword, [], $additionalConditions);

        $totalItems = $this->model->countByConditions($conditions['where'], $conditions['params']);
        $pagination->setTotalItems($totalItems);

        // get data
        $data = $this->model->searchByConditions(
            $conditions['where'],
            $conditions['params'],
            $pagination->getOffset(),
            $pagination->getLimit()
        );

        // render table
        $tableRenderer = new TableRenderer($this->model, $data, $this->view, $this->context, $this->deleteType, $this->table);
        $content = $tableRenderer->render();

        // set messages
        $error = $this->flashManager->getData('errors', []);
        $success = $this->flashManager->getData('success', []);

        // render messages
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
