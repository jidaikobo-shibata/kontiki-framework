<?php

namespace Jidaikobo\Kontiki\Models;

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Validation\BaseValidator;

class FileModel extends BaseModel
{
    use Traits\CRUDTrait;

    protected string $table = 'files';

    public function __construct(
        Database $db,
        BaseValidator $validator
    ) {
        parent::__construct($db);
        $this->setValidator($validator);
    }

    protected function defineFieldDefinitions(array $params = []): void
    {
        $this->fieldDefinitions = [
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
                'rules' => [
                    // ['lengthMin', 3]
                ],
                'filter' => FILTER_UNSAFE_RAW,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
        ];
    }
}
