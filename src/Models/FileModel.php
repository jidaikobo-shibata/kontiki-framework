<?php

namespace jidaikobo\kontiki\Models;

class FileModel extends BaseModel
{
    protected string $table = 'files';

    public function getDisplayFields(): array
    {
        return ['id', 'name', 'path', 'created_at'];
    }

    public function getFieldDefinitions(array $params = []): array
    {
        return [
            'id' => [
                'label' => 'ID',
            ],
            'path' => [
                'label' => __('path'),
                'type' => 'text',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => true,
                'rules' => ['required'],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'description' => [
                'label' => __('description'),
                'description' => '',
                'type' => 'textarea',
                'attributes' => [
                  'class' => 'form-control',
                  'rows' => '2'
                ],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => true,
                'rules' => [],
                'filter' => FILTER_UNSAFE_RAW,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
        ];
    }
}
