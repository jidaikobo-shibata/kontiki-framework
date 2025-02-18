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

    public function allIndex(Request $request, Response $response): Response
    {
        $this->context = 'all';
        return static::index($request, $response);
    }

    public function getIndexData(array $queryParams): array
    {
        $query = $this->applyFiltersToQuery($queryParams);

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

    /**
     * Applies all filters and conditions to the query builder.
     *
     * @param array $queryParams The query parameters.
     * @return \Illuminate\Database\Query\Builder The modified query.
     */
    private function applyFiltersToQuery(array $queryParams): \Illuminate\Database\Query\Builder
    {
        $query = $this->model->buildSearchConditions($queryParams['s'] ?? '', []);
        $query = $this->model->getAdditionalConditions($query, $this->context);

        $query = $this->applySorting($query, $queryParams);
        $query = $this->applyPostTypeFilter($query);

        return $query;
    }

    /**
     * Applies sorting conditions to the query.
     *
     * @param \Illuminate\Database\Query\Builder $query The query builder instance.
     * @param array $queryParams The query parameters containing sorting details.
     * @return \Illuminate\Database\Query\Builder The modified query.
     */
    private function applySorting(\Illuminate\Database\Query\Builder $query, array $queryParams): \Illuminate\Database\Query\Builder
    {
        if (!empty($queryParams['orderby']) && !empty($queryParams['order'])) {
            $validColumns = ['id', 'name', 'created_at']; // いったんハードコーディング
            $column = in_array($queryParams['orderby'], $validColumns, true) ? $queryParams['orderby'] : 'id';
            $direction = strtoupper($queryParams['order']) === 'DESC' ? 'DESC' : 'ASC';
            return $query->orderBy($column, $direction);
        }

        return $query->orderBy('id', 'DESC');
    }

    /**
     * Applies the post_type filter to the query.
     *
     * @param \Illuminate\Database\Query\Builder $query The query builder instance.
     * @return \Illuminate\Database\Query\Builder The modified query.
     */
    private function applyPostTypeFilter(\Illuminate\Database\Query\Builder $query): \Illuminate\Database\Query\Builder
    {
        if (!empty($this->model->getPostType())) {
            return $query->where('post_type', '=', $this->model->getPostType());
        }
        return $query;
    }

    public function index(Request $request, Response $response): Response
    {
        // Get data using the new getData method
        $data = $this->getIndexData($request->getQueryParams());

        // render table
        $tableRenderer = new TableRenderer($this->model, $data, $this->view, $this->context, $this->getRoutes());
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
        $content .= $this->pagination->render(env('BASEPATH', '') . "/admin/{$this->postType}/index");

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
                'pageTitle' => __($title, $title_placeholder, ['name' => __($this->postType)]),
                'content' => $content,
            ]
        );
    }
}
