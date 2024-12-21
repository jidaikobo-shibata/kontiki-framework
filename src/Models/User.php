<?php

namespace jidaikobo\kontiki\Models;

use jidaikobo\kontiki\Utils\Lang;

class User extends BaseModel
{
    protected string $table = 'users';

    public function getDisplayFields(): array
    {
        return ['id', 'username', 'created_at'];
    }

    public function getFieldDefinitions(): array
    {
        return [
            'id' => [
                'label' => 'ID',
            ],
            'username' => [
                'label' => Lang::get('username', 'Username'),
                'type' => 'text',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => TRUE,
                'rules' => [
                    'required',
                    ['lengthMin', 3],
                    ['unique', 'users', 'username']
                ],
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
                'searchable' => false,
                'rules' => ['required', ['lengthMin', 8]],
                'filter' => FILTER_UNSAFE_RAW,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'created_at' => [
                'label' => Lang::get('created_at', 'Created'),
            ],
        ];
    }

    public function processFieldDefinitions(array $fieldDefinitions): array
    {
        // パスワードの `required` ルールを除外
        if (isset($fieldDefinitions['password']['rules'])) {
            $fieldDefinitions['password']['rules'] = array_filter(
                $fieldDefinitions['password']['rules'],
                fn($rule) => $rule !== 'required'
            );
        }
        return $fieldDefinitions;
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

        return parent::update($id, $data);
    }
}
