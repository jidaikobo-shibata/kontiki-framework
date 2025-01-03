<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Jidaikobo\Kontiki\Utils\Pagination;
use Jidaikobo\Kontiki\Utils\TableHandler;
use Jidaikobo\Kontiki\Utils\TableRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexTrait
{
    protected string $context;
    protected string $deleteType;

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
        self::isUsesTrashRestoreTrait();

        $query = $this->model->buildSearchConditions(
            $request->getQueryParams()['s'] ?? '',
            []
        );
        $query = $this->model->getAdditionalConditions($query, $this->context, $this->deleteType);

        $totalItems = $query->count();

        $pagination = new Pagination((int)($request->getQueryParams()['paged'] ?? 1), 10);
        $pagination->setTotalItems($totalItems);

        $data = $query->limit($pagination->getLimit())
                  ->offset($pagination->getOffset())
                  ->get()
                  ->map(fn($item) => (array) $item)
                  ->toArray();

        $data = array_map(
            fn($item) => $this->model->processDataBeforeGet($item),
            $data
        );

        // render table
        $tableRenderer = new TableRenderer($this->model, $data, $this->view, $this->context);
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
        $content .= $pagination->render(env('BASEPATH', '') . "/admin/{$this->table}/index");

        $title = 'x_index';
        $title .= $this->context === 'normal' ? '' : '_' . $this->context ;
        $title_placeholder = 'Index of :name';
        $title_placeholder = $this->context === 'normal'
          ? $title_placeholder
          : $this->context . ' ' . $title_placeholder;

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => __($title, $title_placeholder, ['name' => __($this->table)]),
                'content' => $content,
            ]
        );
    }
}
