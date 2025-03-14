<?php

namespace Jidaikobo\Kontiki\Models;

class UserModel extends BaseModel
{
    use Traits\CRUDTrait;
    use Traits\MetaDataTrait;
    use Traits\IndexTrait;

    protected string $table = 'users';

    public function setFieldDefinitions(array $params = []): void
    {
        $id = $params['id'] ?? null;

        $fields = [
            'id' => $this->getIdField(),

            'username' => $this->getField(
                __('username', 'Username'),
                [
                    'rules' => [
                        'required',
                        ['lengthMin', 3],
                        ['unique', $this->table, 'username', $id]
                    ],
                    'display_in_list' => true
                ]
            ),

            'password' => $this->getField(
                __('password', 'password'),
                [
                    'type' => 'password',
                    'description' => __('users_edit_message', 'If the password is blank, the password will not be changed.'),
                    'rules' => [
                        'required',
                        ['lengthMin', 8]
                    ],
                    'filter' => FILTER_UNSAFE_RAW,
                ]
            ),

            'created_at' => $this->getReadOnlyField(
                __('created_at', 'Created'),
                [
                    'display_in_list' => true
                ]
            ),
        ];

        $MetaData = $this->getMetaDataFieldDefinitions($params);
        $this->fieldDefinitions = array_merge($fields, $MetaData);
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    protected function processDataForForm(string $actionType, array $data): array
    {
        if ($actionType == 'edit') {
            $data['password'] = '';
        }
        return $data;
    }

    protected function afterProcessDataBeforeSave(string $context, array $data): array
    {
        if ($context == 'create') {
            $data['password'] = $this->hashPassword($data['password']);
        }

        if ($context == 'update') {
            // Branching password processing
            if (isset($data['password'])) {
                if (trim($data['password']) === '') {
                    unset($data['password']);
                } else {
                    $data['password'] = $this->hashPassword($data['password']);
                }
            }
        }
        return $data;
    }

    public function processFieldDefinitionsForSave(string $context, array $fieldDefinitions): array
    {
        if ($context == 'create') {
            return $fieldDefinitions;
        }

        // Exclude `required` from password's rules
        // No password specified means no change
        if (isset($fieldDefinitions['password']['rules'])) {
            $fieldDefinitions['password']['rules'] = array_filter(
                $fieldDefinitions['password']['rules'],
                fn($rule) => $rule !== 'required'
            );
        }

        return $fieldDefinitions;
    }
}
