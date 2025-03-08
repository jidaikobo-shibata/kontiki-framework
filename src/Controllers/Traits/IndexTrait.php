<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Jidaikobo\Kontiki\Handlers\TableHandler;
use Jidaikobo\Kontiki\Renderers\TableRenderer;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait IndexTrait
{
    private function index(Request $request, Response $response, string $context): Response
    {
        // Get data using the new getData method
        $data = $this->model->getIndexData($context, $request->getQueryParams());

        // render table
        $tableRenderer = new TableRenderer(
            $this->model,
            $this->view,
            $this->adminDirName,
            $this->getRoutes(),
            $data,
            $context
        );
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
        $content .= $this->model->getPagination()->render(env('BASEPATH', '') . "/{$this->adminDirName}/index");

        $title = 'x_index_' . $context;
        $title_placeholder = $context . ' index of :name';

        return $this->renderResponse(
            $response,
            __($title, $title_placeholder, ['name' => __($this->label)]),
            $content
        );
    }
}
