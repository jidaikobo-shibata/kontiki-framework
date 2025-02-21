<?php

namespace Jidaikobo\Kontiki\Renderers;

use Carbon\Carbon;
use Slim\Views\PhpRenderer;
use Jidaikobo\Kontiki\Models\BaseModel;

class TableRenderer
{
    protected $fields;
    protected $data;
    protected $view;
    protected $table;
    protected $context;
    protected $routes;
    protected $postType;
    protected $deleteType; // Context: "hardDelete" or "softDelete"

    public function __construct(
        BaseModel $model,
        array $data,
        PhpRenderer $view,
        string $context = 'all',
        array $routes = []
    ) {
        // Automatically retrieve field definitions and display fields from the model
        $fieldDefinitions = $model->getFieldDefinitions();
        $displayFields = $model->getDisplayFields();
        $this->deleteType = $model->getDeleteType();
        $this->postType = $model->getPostType();
        $this->postType = empty($this->postType) ? $model->getPsudoPostType() : $this->postType;

        // Filter fields based on the display fields defined in the model
        $this->fields = array_filter($fieldDefinitions, function ($key) use ($displayFields) {
            return in_array($key, $displayFields, true);
        }, ARRAY_FILTER_USE_KEY);

        $this->data = $data;
        $this->view = $view;
        $this->context = $context;
        $this->routes = $routes;
    }

    public function render(): string
    {
        $displayModes = $this->renderDisplayModes();
        $headers = $this->renderHeaders();
        $rows = array_map(function ($row) {
            return $this->renderRow($row);
        }, $this->data);

        return $this->view->fetch('tables/table.php', [
            'displayModes' => $displayModes,
            'headers' => $headers,
            'rows' => implode("\n", $rows),
        ]);
    }

    protected function renderDisplayModes(): array
    {
        $displayModes = [];
        $seenPaths = [];

        foreach ($this->routes as $route) {
            if (
                strpos($route['path'], '/index') === false ||
                strpos($route['path'], '/admin/') === false
            ) {
                continue;
            }

            if (in_array($route['path'], $seenPaths, true)) {
                continue;
            }

            $displayModes[] = [
                'name' => __(basename($route['path'])),
                'path' => $route['path'],
            ];
            $seenPaths[] = $route['path'];
        }

        return $displayModes;
    }

    protected function renderHeaders(): string
    {
        $headerHtml = '';
        foreach ($this->fields as $name => $config) {
            $label = e($config['label']);
            $headerHtml .= sprintf('<th>%s</th>', $label);
        }
        $headerHtml .= '<th>' . __('actions') . '</th>';
        return $headerHtml;
    }

    protected function renderRow(array $row): string
    {
        $cellsHtml = $this->renderStatus($row);
        $cellsHtml .= $this->renderActions($row);

        return sprintf('<tr>%s</tr>', $cellsHtml);
    }

    protected function renderStatus(array $row): string
    {
        $currentTime = Carbon::now('UTC')->setTimezone(env('TIMEZONE', 'UTC'));
        $cellsHtml = '';

        foreach (array_keys($this->fields) as $name) {
            $values = $this->getStatusValues($name, $row, $currentTime);
            $value = implode(', ', array_filter($values));
            $cellsHtml .= sprintf('<td>%s</td>', e($value));
        }

        return $cellsHtml;
    }

    protected function getStatusValues(string $name, array $row, Carbon $currentTime): array
    {
        $values = [];

        if ($name === 'status') {
            $values[] = __($row[$name]) ?: '';

            // Check for reserved (future publication)
            if (!empty($row['published_at'])) {
                $publishedAt = Carbon::parse($row['published_at']);
                if ($publishedAt->greaterThan($currentTime)) {
                    $values[] = __('reserved');
                }
            }

            // Check for expired
            if (!empty($row['expired_at'])) {
                $expiredAt = Carbon::parse($row['expired_at']);
                if ($currentTime->greaterThan($expiredAt)) {
                    $values[] = __('expired');
                }
            }
        } else {
            $values[] = $row[$name] ?? '';
        }

        return $values;
    }

    protected function renderActions(array $row): string
    {
        $id = e($row['id']);

        $uri = env('BASEPATH', '') . "/admin/{$this->postType}/%s/%s";
        $tpl = '<a href="' . $uri . '" class="btn btn-%s btn-sm">%s</a> ';
        $tplPreview = '<a href="' . $uri . '" class="btn btn-%s btn-sm" target="preview">%s</a> ';
        $actions = [
            'edit' => sprintf($tpl, 'edit', $id, 'primary', __('edit')),
            'delete' => sprintf($tpl, 'delete', $id, 'danger', __('delete')),
            'trash' => sprintf($tpl, 'trash', $id, 'warning', __('to_trash')),
            'restore' => sprintf($tpl, 'restore', $id, 'success', __('restore')),
            'preview' => sprintf($tplPreview, 'preview', $id, 'success', __('preview')),
        ];

        $html = '';
        if ($this->deleteType == 'hardDelete') {
            $html .= $actions['edit'] . $actions['delete'];
        } elseif ($this->context == 'trash') {
            $html .= $actions['restore'] . $actions['preview'] . $actions['delete'];
        } elseif ($this->deleteType == 'softDelete') {
            $html .= $actions['edit'] . $actions['preview'] . $actions['trash'];
        } else {
            $html .= $actions['edit'];
        }

        return sprintf('<td class="text-nowrap">%s</td>', $html);
    }
}
