<?php

namespace jidaikobo\kontiki\Models;

use PDO;

class Post extends BaseModel
{
    protected PDO $pdo;

    protected string $table = 'post';

    protected static array $fieldDefinitions = [
        'username' => [
            'rules' => ['required', ['lengthMin', 3]],
            'default' => '',
            'filter' => FILTER_SANITIZE_STRING,
        ],
        'password' => [
            'rules' => ['required', ['lengthMin', 12]],
            'default' => '',
            'filter' => FILTER_UNSAFE_RAW,
        ],
    ];
}
