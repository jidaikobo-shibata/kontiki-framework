<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Jidaikobo\Kontiki\Utils\Pagination;
use Jidaikobo\Kontiki\Handlers\TableHandler;
use Jidaikobo\Kontiki\Renderers\TableRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexTrait
{
    protected string $context;
    protected Pagination $pagination;

    /**
     * Retrieve data with pagination and additional conditions applied.
     *
     * @param array $queryParams Query parameters from the request.
     * @return array Processed data array.
     */
    public function getIndexData(array $queryParams): array
    {
        // Build search conditions based on query parameters
        $query = $this->model->buildSearchConditions($queryParams['s'] ?? '', []);
        $query = $this->model->getAdditionalConditions($query, $this->context);

        // Set up pagination
        $totalItems = $query->count();
        $this->pagination = new Pagination((int)($queryParams['paged'] ?? 1), 10);
        $this->pagination->setTotalItems($totalItems);

        // Fetch and process data
        $data = $query->limit($this->pagination->getLimit())
                      ->offset($this->pagination->getOffset())
                      ->get()
                      ->map(fn($item) => (array) $item)
                      ->toArray();

        return array_map(fn($item) => $this->model->processDataBeforeGet($item), $data);
    }

    public function index(Request $request, Response $response): Response
    {
        // Get data using the new getData method
        $data = $this->getIndexData($request->getQueryParams());

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
        $content .= $this->pagination->render(env('BASEPATH', '') . "/admin/{$this->table}/index");

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
