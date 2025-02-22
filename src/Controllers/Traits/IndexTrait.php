<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Jidaikobo\Kontiki\Handlers\TableHandler;
use Jidaikobo\Kontiki\Renderers\TableRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexTrait
{
    private function index(Request $request, Response $response, string $context): Response
    {
        // Get data using the new getData method
        $data = $this->model->getIndexData($context, $request->getQueryParams());

        // render table
        $tableRenderer = new TableRenderer($this->model, $data, $this->view, $context, $this->getRoutes());
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
        $content .= $this->model->getPagination()->render(env('BASEPATH', '') . "/admin/{$this->adminDirName}/index");

        $title = 'x_index';
        $title .= $context === 'normal' ? '' : '_' . $context ;
        $title_placeholder = 'Index of :name';
        $title_placeholder = $context === 'normal'
          ? $title_placeholder
          : $context . ' ' . $title_placeholder;
        $postType = $this->model->getPostType() ?? $this->model->getPsudoPostType();

        return $this->renderResponse(
            $response,
            __($title, $title_placeholder, ['name' => __($postType)]),
            $content
        );
    }
}
