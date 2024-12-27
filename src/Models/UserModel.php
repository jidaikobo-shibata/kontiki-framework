<?php

namespace jidaikobo\kontiki\Models;

class UserModel extends BaseModel
{
    protected string $table = 'users';

    public function getDisplayFields(): array
    {
        return ['id', 'username', 'created_at'];
    }

    public function getFieldDefinitions(array $params = []): array
    {
        $id = $params['id'] ?? null;

        return [
            'id' => [
                'label' => 'ID',
            ],
            'username' => [
                'label' => __('username', 'Username'),
                'type' => 'text',
                'attributes' => ['class' => 'form-control'],
                'label_attributes' => ['class' => 'form-label'],
                'default' => '',
                'searchable' => true,
                'rules' => [
                    'required',
                    ['lengthMin', 3],
                    ['unique', 'users', 'username', $id]
                ],
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'template' => 'default',
                'group' => 'main',
                'fieldset_template' => 'forms/fieldset/flat.php',
            ],
            'password' => [
                'label' => __('password', 'Password'),
                'description' => __('users_edit_message', 'If the password is blank, the password will not be changed.'),
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
                'label' => __('created_at', 'Created'),
            ],
        ];
    }

    public function processEditFieldDefinitions(array $fieldDefinitions): array
    {
        // Exclude `required` password rules
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
        // Branching password processing
        if (isset($data['password'])) {
            if (trim($data['password']) === '') {
                unset($data['password']);
            } else {
                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }
        }

        return parent::update($id, $data);
    }
}
