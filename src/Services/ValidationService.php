<?php

namespace jidaikobo\kontiki\Services;

use Valitron\Validator;
use jidaikobo\kontiki\Database\DatabaseHandler;
use jidaikobo\kontiki\Utils\Env;
use jidaikobo\kontiki\Utils\Lang;

class ValidationService
{
    protected DatabaseHandler $db;

    /**
     * ValidationService constructor.
     *
     * @param DatabaseHandler $db Instance of DatabaseHandler for database operations.
     */
    public function __construct(DatabaseHandler $db)
    {
        $this->db = $db;

        Validator::addRule(
            'unique',
            function ($field, $value, array $params, array $fields) {
                return $this->isUnique($params[0], $params[1], $value, $params[2] ?? null);
            },
            Lang::get('is_already_exists', 'is already exists')
        );
    }

    /**
     * Validate the given data based on field definitions.
     *
     * @param array $data The input data to validate.
     * @param array $fieldDefinitions The validation rules and configurations for each field.
     * @return array Validation result with 'valid' (bool) and 'errors' (array).
     */
    public function validate(array $data, array $fieldDefinitions): array
    {
        Validator::lang(Env::get('LANG'));
        $validator = new Validator($data);
        $validator->setPrependLabels(false);

        foreach ($fieldDefinitions as $field => $definition) {
            if (isset($definition['rules'])) {
                foreach ($definition['rules'] as $rule) {
                    if (is_array($rule)) {
                        $validator->rule($rule[0], $field, ...array_slice($rule, 1));
                    } else {
                        $validator->rule($rule, $field);
                    }
                }
            }
        }

        $isValid = $validator->validate();

        $errors = [];

        if (!$isValid) {
            foreach ($validator->errors() as $field => $messages) {
                $errors[$field] = [
                    'messages' => $messages,
                    'htmlName' => $field,
                ];
            }
        }

        return [
            'valid' => $isValid,
            'errors' => $errors,
        ];
    }

    /**
     * Check if a value is unique in the database for a specific field, optionally excluding a specific record by ID.
     *
     * @param string $table The table name to check in.
     * @param string $column The column name to check.
     * @param mixed $value The value to check for uniqueness.
     * @param int|null $excludeId The ID of the record to exclude from the check (used for updates).
     * @return bool True if the value is unique, false otherwise.
     */
    public function isUnique(string $table, string $column, mixed $value, ?int $excludeId = null): bool
    {
        $whereClause = "$column = :value" . ($excludeId !== null ? " AND id != :excludeId" : '');
        $params = ['value' => $value];
        if ($excludeId !== null) {
            $params['excludeId'] = $excludeId;
        }

        $count = $this->db->countAll($table, "WHERE $whereClause", $params);

        return $count === 0;
    }
}
