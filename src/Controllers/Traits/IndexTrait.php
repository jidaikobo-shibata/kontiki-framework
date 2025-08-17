<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Jidaikobo\Kontiki\Handlers\TableHandler;
use Jidaikobo\Kontiki\Renderers\TableRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexTrait
{
    private function index(
        Request $request,
        Response $response,
        string $context
    ): Response {
        // Get data using the new getData method
        $data = $this->model->getIndexData($context, $request->getQueryParams());

        // render table
        $content = $this->tableService->tableHtml(
            $data,
            $this->adminDirName,
            $this->getRoutes(),
            $context
        );

        // set messages
        $error = $this->flashManager->getData('errors', []);
        $success = $this->flashManager->getData('success', []);

        // render messages
        $content = $this->tableService->addMessages(
            $content,
            $error,
            $success
        );

        // pagination
        $paginationSuffix = $context == 'all' ? '' : '/' . $context;
        $content .= $this->model->getPagination()->render(
            env('BASEPATH', '') . "/{$this->adminDirName}/index" . $paginationSuffix
        );
        $totalItems = $this->model->getPagination()->getTotalItems();

        $title = 'x_index_' . $context;
        $title_placeholder = $context . ' index of :name';

        return $this->renderResponse(
            $response,
            __($title, $title_placeholder, ['name' => __($this->label)]) . ' (' . $totalItems . ')',
            $content
        );
    }
}
