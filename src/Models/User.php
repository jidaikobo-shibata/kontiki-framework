<?php

namespace jidaikobo\kontiki\Models;

use PDO;
use jidaikobo\kontiki\Utils\Lang;

class User extends BaseModel
{
    protected PDO $pdo;

    protected string $table = 'users';

    public function getDisplayFields(): array
    {
        return ['username', 'created_at'];
    }

    public function getFieldDefinitions(): array
    {
        return [
            'username' => [
                'label' => Lang::get('username', 'Username'),
                'type' => 'text',
                'attributes' => ['class' => 'input-title'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'rules' => ['required', ['lengthMin', 3]],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'password' => [
                'label' => Lang::get('password', 'Password'),
                'type' => 'password',
                'attributes' => [],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'rules' => ['required', ['lengthMin', 3]],
                'filter' => FILTER_UNSAFE_RAW,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
        ];
    }

    /**
     * Get the user by their username.
     *
     * @param  string $username The username to search for.
     * @return array|null user information, or null if not.
     */
    public function getByUsername(string $username): ?array
    {
        $query = "SELECT password FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['username' => $username]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?? null;
    }
}
