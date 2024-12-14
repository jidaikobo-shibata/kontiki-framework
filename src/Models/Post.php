<?php

namespace jidaikobo\kontiki\Models;

use PDO;

class Post extends BaseModel
{
    protected PDO $pdo;

    protected string $table = 'post';

    public function getFieldDefinitions(): array
    {
        return [
            'title' => [
                'label' => Lang::get('title', 'Title'),
                'type' => 'text',
                'attributes' => ['class' => 'input-title'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'rules' => ['required'],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'content' => [
                'label' => Lang::get('content', 'Content'),
                'type' => 'text',
                'attributes' => [],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'rules' => [],
                'filter' => FILTER_UNSAFE_RAW,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
        ];
    }
}
