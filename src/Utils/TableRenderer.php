<?php

namespace Jidaikobo\Kontiki\Utils;

use Slim\Views\PhpRenderer;
use Jidaikobo\Kontiki\Models\BaseModel;

class TableRenderer
{
    protected $fields;
    protected $data;
    protected $view;
    protected $table;
    protected $context; // Context: "normal" or "trash"
    protected $deleteType; // Context: "hardDelete" or "softDelete"

    public function __construct(BaseModel $model, array $data, PhpRenderer $view, string $context = 'normal')
    {
        // Automatically retrieve field definitions and display fields from the model
        $fieldDefinitions = $model->getFieldDefinitions();
        $displayFields = $model->getDisplayFields();
        $this->deleteType = $model->getDeleteType();
        $this->table = $model->getTableName();;

        // Filter fields based on the display fields defined in the model
        $this->fields = array_filter($fieldDefinitions, function ($key) use ($displayFields) {
            return in_array($key, $displayFields, true);
        }, ARRAY_FILTER_USE_KEY);

        $this->data = $data;
        $this->view = $view;
        $this->context = $context;
    }

    public function render(): string
    {
        $headers = $this->renderHeaders();
        $rows = array_map(function ($row) {
            return $this->renderRow($row);
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
            $label = e($config['label']);
            $headerHtml .= sprintf('<th>%s</th>', $label);
        }
        $headerHtml .= '<th>' . __('actions') . '</th>'; // Add actions column
        return $headerHtml;
    }

    protected function renderRow(array $row): string
    {
        $cellsHtml = '';
        foreach ($this->fields as $name => $config) {
            $value = $row[$name] ?? '';
            $cellsHtml .= sprintf('<td>%s</td>', e($value));
        }

        // Render the actions column
        $cellsHtml .= $this->renderActions($row);

        return sprintf('<tr>%s</tr>', $cellsHtml);
    }

    protected function renderActions(array $row): string
    {
        $id = e($row['id']);

        $uri = env('BASEPATH', '') . "/admin/{$this->table}/%s/%s";
        $tpl = '<a href="' . $uri . '" class="btn btn-%s btn-sm">%s</a> ';
        $actions = [
            'edit' => sprintf($tpl, 'edit', $id, 'primary', __('edit')),
            'delete' => sprintf($tpl, 'delete', $id, 'danger', __('delete')),
            'trash' => sprintf($tpl, 'trash', $id, 'warning', __('trash')),
            'restore' => sprintf($tpl, 'restore', $id, 'success', __('restore')),
        ];

        $html = '';
        if ($this->context == 'normal' && $this->deleteType == 'softDelete') {
            $html .= $actions['edit'] . $actions['trash'];
        } elseif ($this->context == 'normal' && $this->deleteType == 'hardDelete') {
            $html .= $actions['edit'] . $actions['delete'];
        } elseif ($this->context == 'trash') {
            $html .= $actions['restore'] . $actions['delete'];
        } else {
            $html .= $actions['edit'];
        }

        return sprintf('<td class="text-nowrap">%s</td>', $html);
    }
}
