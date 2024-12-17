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
                'attributes' => ['class' => 'form-control'],
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
                'description' => Lang::get("users_edit_message", 'If the password is blank, the password will not be changed.'),
                'type' => 'password',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'rules' => ['required', ['lengthMin', 8]],
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
        $result = $result === false ? NULL : $result;

        return $result ?? null;
    }

    public function processFieldDefinitions($fields): array
    {
        $fields['password']['rules'] = array_filter($fields['password']['rules'], function ($rule) {
            return $rule !== 'required';
        });
        return $fields;
    }

    public function update(int $id, array $data): bool
    {
        $fieldDefinitions = $this->getFieldDefinitions();

        // パスワード処理を分岐
        if (isset($data['password'])) {
            if (trim($data['password']) === '') {
                unset($data['password']);
                $fieldDefinitions = $this->processFieldDefinitions($fieldDefinitions);
            } else {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
        }

        $data = $this->filterAllowedFields($data);

        $validation = $this->validate($data, $fieldDefinitions);
        if (!$validation['valid']) {
            throw new InvalidArgumentException('Validation failed: ' . json_encode($validation['errors']));
        }

        return $this->executeUpdate($id, $data);
    }
}
