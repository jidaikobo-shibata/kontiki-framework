<?php

namespace Jidaikobo\Kontiki\Models;

class UserModel extends BaseModel
{
    use Traits\CRUDTrait;
    use Traits\MetaDataTrait;
    use Traits\IndexTrait;

    protected string $table = 'users';

    protected function defineFieldDefinitions(): void
    {
        // add dynamic rules at $this->processFieldDefinitions()
        $this->fieldDefinitions = [
            'id' => $this->getIdField(),

            'username' => $this->getField(
                __('username', 'Username'),
                [
                    'rules' => [
                        'required',
                        ['lengthMin', 3]
                    ],
                    'display_in_list' => true
                ]
            ),

            'password' => $this->getField(
                __('password', 'password'),
                [
                    'type' => 'password',
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
    }

    protected function processFieldDefinitions(
        string $context = '',
        array $data = [],
        int $id = null
    ): void {
        // add rule
        $this->fieldDefinitions['username']['rules'][] = [
            'unique',
            $this->table,
            'username',
            $id
        ];

        if ($context == 'create') {
            return;
        }

        // Exclude `required` from password's rules
        // No password specified means no change
        $this->fieldDefinitions['password']['rules'] = array_filter(
            $this->fieldDefinitions['password']['rules'],
            fn($rule) => $rule !== 'required'
        );

        $this->fieldDefinitions['password']['description'] = __('users_edit_message');
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
}
