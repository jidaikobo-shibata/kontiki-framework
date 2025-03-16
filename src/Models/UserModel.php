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
                __('username'),
                [
                    'rules' => [
                        'required',
                        ['lengthMin', 3]
                    ],
                    'display_in_list' => true
                ]
            ),

            'password' => $this->getField(
                __('password'),
                [
                    'type' => 'password',
                    'rules' => [
                        'required',
                        ['lengthMin', 8]
                    ],
                    'filter' => FILTER_UNSAFE_RAW,
                ]
            ),

            'role' => $this->getField(
                __('role'),
                [
                    'type' => 'select',
                    'options' => [
                        'editor' => __('editor'),
                        'admin' => __('admin'),
                    ],
                    'rules' => [
                        'required',
                    ],
                    'attributes' => [
                        'class' => 'form-control form-select'
                    ],
                    'display_in_list' => true
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

        // disable form elements
        if (in_array($context, ['trash', 'restore', 'delete'])) {
            $this->disableFormFieldsForContext();
        }
    }

    /**
     * Override the validation method to ensure that at least one "admin" remains in the system.
     *
     * @param array $data The data to validate.
     * @param array $fieldDefinitions The field definitions used for validation.
     * @return array An array containing 'valid' (boolean) and 'errors'.
     */
    public function validateByFields(array $data, array $fieldDefinitions, ?int $id = NULL): array
    {
        // Execute the parent validation logic
        $result = parent::validateByFields($data, $fieldDefinitions, $id);

        // Check if the "role" field is being modified
        if (isset($data['role'])) {
            // Retrieve the target user's data from the database
            $targetUser = $this->getById($id ?? 0);

            // If the user is an "admin" and is attempting to change their role
            if ($targetUser && $targetUser['role'] === 'admin' && $data['role'] !== 'admin') {
                // Count the number of other admins in the system
                $adminCount = $this->db->table($this->table)
                    ->where('role', 'admin')
                    ->where('id', '!=', $targetUser['id']) // Exclude the current user
                    ->count();

                // If no other admins remain, return a validation error
                if ($adminCount === 0) {
                    $result['valid'] = false;
                    $result['errors']['role']['messages'] = [__('at_least_one_admin')];
                }
            }
        }

        return $result;
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
