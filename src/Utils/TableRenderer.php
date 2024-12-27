<?php

namespace jidaikobo\kontiki\Utils;

use Slim\Views\PhpRenderer;
use jidaikobo\kontiki\Models\BaseModel;

class TableRenderer
{
    protected $model;
    protected $fields;
    protected $data;
    protected $view;
    protected $context; // Context: "normal" or "trash"

    public function __construct(BaseModel $model, array $data, PhpRenderer $view, string $context = 'normal')
    {
        // Automatically retrieve field definitions and display fields from the model
        $fieldDefinitions = $model->getFieldDefinitions();
        $displayFields = $model->getDisplayFields();

        // Filter fields based on the display fields defined in the model
        $this->fields = array_filter($fieldDefinitions, function ($key) use ($displayFields) {
            return in_array($key, $displayFields, true);
        }, ARRAY_FILTER_USE_KEY);

        $this->model = $model;
        $this->data = $data;
        $this->view = $view;
        $this->context = $context;
    }

    public function render(): string
    {
        $headers = $this->renderHeaders();
        $rows = array_map(function ($row) {
            return $this->renderRow($row, $this->model);
        }, $this->data);

        return $this->view->fetch('tables/table.php', [
            'headers' => $headers,
            'rows' => implode("\n", $rows),
        ]);
    }

    protected function renderHeaders(): string
    {
        $headerHtml = '';
        foreach ($this->fields as $name => $config) {
            $label = htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8');
            $headerHtml .= sprintf('<th>%s</th>', $label);
        }
        $headerHtml .= '<th>' . __('actions') . '</th>'; // Add actions column
        return $headerHtml;
    }

    protected function renderRow(array $row, BaseModel $model): string
    {
        $cellsHtml = '';
        foreach ($this->fields as $name => $config) {
            $value = $row[$name] ?? '';
            $cellsHtml .= sprintf('<td>%s</td>', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        // Render the actions column
        $cellsHtml .= $this->renderActions($row, $model);

        return sprintf('<tr>%s</tr>', $cellsHtml);
    }

    protected function renderActions(array $row, BaseModel $model): string
    {
        $actions = $model->getActions($this->context);
        $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');

        $html = '';
        foreach ($actions as $action) {
            switch ($action) {
                case 'edit':
                    $html .= sprintf('<a href="./edit/%s" class="btn btn-primary btn-sm">' . __('edit') . '</a> ', $id);
                    break;
                case 'delete':
                    $html .= sprintf('<a href="./delete/%s" class="btn btn-danger btn-sm">' . __('delete') . '</a> ', $id);
                    break;
                case 'moveToTrash':
                    $html .= sprintf('<a href="./trash/%s" class="btn btn-warning btn-sm">' . __('trash') . '</a> ', $id);
                    break;
                case 'restore':
                    $html .= sprintf('<a href="./restore/%s" class="btn btn-success btn-sm">' . __('restore') . '</a> ', $id);
                    break;
            }
        }

        return sprintf('<td class="text-nowrap">%s</td>', $html);
    }
}
