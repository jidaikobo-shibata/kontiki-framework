<?php

namespace jidaikobo\kontiki\Utils;

use jidaikobo\kontiki\Utils\FormUtils;
use Slim\Views\PhpRenderer;

class FormRenderer
{
    protected $fields;
    protected $view;

    public function __construct(array $fields, PhpRenderer $view)
    {
        $this->fields = $fields;
        $this->view = $view;
    }

    public function render(): string
    {
        $groupedFields = $this->groupFields();

        $html = '';
        foreach ($groupedFields as $group => $fields) {
            $html .= $this->renderGroup($group, $fields);
        }

        return $html;
    }

    protected function groupFields(): array
    {
        $grouped = [];
        foreach ($this->fields as $name => $config) {
            $group = $config['group'] ?? 'default';
            $grouped[$group][$name] = $config;
        }
        return $grouped;
    }

    protected function renderGroup(string $group, array $fields): string
    {
        $groupTemplate = $this->getGroupTemplate($group);

        $fieldHtml = array_map(function ($name, $config) {
            return $this->renderFieldset($name, $config);
        }, array_keys($fields), $fields);

        return $this->view->fetch($groupTemplate, [
            'fields_html' => implode("\n", $fieldHtml),
            'group' => $group,
        ]);
    }

    protected function renderFieldset(string $name, array $config): string
    {
        $id = FormUtils::nameToId($name);

        $labelAttributes = $this->renderAttributes(array_merge(
            $config['label_attributes'] ?? [],
            ['for' => $id]
        ));

        $fieldHtml = $this->renderField($name, $config);
        $fieldsetTemplate = $config['fieldset_template'] ?? 'forms/fieldset/flat.php';

        return $this->view->fetch($fieldsetTemplate, [
            'label' => sprintf(
                '<label %s>%s</label>',
                $labelAttributes,
                htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8')
            ),
            'field' => $fieldHtml,
        ]);
    }

    protected function renderField(string $name, array $config): string
    {
        $id = FormUtils::nameToId($name);

        $attributes = $config['attributes'] ?? [];

        if (isset($config['rules']) && in_array('required', $config['rules'], true)) {
            $attributes['required'] = 'required';
        }

        $renderedAttributes = $this->renderAttributes($attributes);

        $fieldTemplate = $this->getFieldTemplate($config['type']);

        return $this->view->fetch($fieldTemplate, [
            'id' => $id,
            'name' => $name,
            'type' => $config['type'],
            'value' => htmlspecialchars($config['default'] ?? '', ENT_QUOTES, 'UTF-8'),
            'attributes' => $renderedAttributes,
            'description' => $config['description'] ?? '',
        ]);
    }

    /**
     * Render HTML attributes from an associative array.
     *
     * @param array $attributes Associative array of attributes.
     * @return string Rendered attributes as a string.
     */
    protected function renderAttributes(array $attributes): string
    {
        $result = [];
        foreach ($attributes as $key => $value) {
            $result[] = sprintf(
                '%s="%s"',
                htmlspecialchars($key, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
            );
        }
        return implode(' ', $result);
    }

    protected function getFieldTemplate(string $type): string
    {
        $templates = [
            'text' => 'forms/fields/text_input.php',
            'textarea' => 'forms/fields/textarea_input.php',
            'checkbox' => 'forms/fields/checkbox_input.php',
        ];
        return $templates[$type] ?? 'forms/fields/text_input.php';
    }

    protected function getGroupTemplate(string $group): string
    {
        $templates = [
            'header' => 'forms/groups/header.php',
            'main' => 'forms/groups/main.php',
            'meta' => 'forms/groups/meta.php',
        ];
        return $templates[$group] ?? 'forms/groups/default.php';
    }
}
