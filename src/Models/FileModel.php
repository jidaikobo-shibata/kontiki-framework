<?php

namespace jidaikobo\kontiki\Models;

use jidaikobo\kontiki\Utils\Lang;

class FileModel extends BaseModel
{
    protected string $table = 'files';


    public function getDisplayFields(): array
    {
        return ['id', 'name', 'path', 'created_at'];
    }

    public function getFieldDefinitions(): array
    {
        return [
            'id' => [
                'label' => 'ID',
            ],
            'path' => [
                'label' => Lang::get('path', 'Path'),
                'type' => 'text',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => TRUE,
                'rules' => ['required'],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'description' => [
                'label' => Lang::get('description', 'Description'),
                'description' => '',
                'type' => 'textarea',
                'attributes' => [
                  'class' => 'form-control',
                  'rows' => '2'
                ],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => true,
                'rules' => [['lengthMin', 8]],
                'filter' => FILTER_UNSAFE_RAW,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
        ];
    }
}
