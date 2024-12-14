<?php

namespace jidaikobo\kontiki\Models;

use jidaikobo\kontiki\Utils\Env;
use PDO;
use PDOException;
use Valitron\Validator;

/**
 * BaseModel provides common CRUD operations for database interactions.
 * Extend this class to create specific models for different database tables.
 */
abstract class BaseModel implements ModelInterface
{
    /**
     * PDO instance for database connection.
     *
     * @var PDO
     */
    protected PDO $pdo;

    /**
     * Table name associated with the model.
     *
     * @var string
     */
    protected string $table;

    /**
     * BaseModel constructor.
     *
     * @param PDO $pdo Database connection instance.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get field definitions for the model.
     * This method must be implemented in child classes.
     *
     * @return array Field definitions.
     */
    abstract public function getFieldDefinitions(): array;

    public function getFieldDefinitionsWithDefaults(array $data): array
    {
        $fields = $this->getFieldDefinitions();

        foreach ($fields as $fieldName => &$field) {
            if (isset($data[$fieldName])) {
                $field['default'] = $data[$fieldName];
            }
        }

        return $fields;
    }

    /**
     * Validate the given data against the field definitions.
     *
     * @param  array $data The data to validate.
     * @return array An array with 'valid' (bool) and 'errors' (array of errors).
     */
    public function validate(array $data): array
    {
        Validator::lang(Env::get('LANG'));
        $validator = new Validator($data);
        $validator->setPrependLabels(false);

        foreach ($this->getFieldDefinitions() as $field => $definition) {
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

        return [
            'valid' => $isValid,
            'errors' => $isValid ? [] : $validator->errors(),
        ];
    }

    /**
     * Retrieve all records from the table.
     *
     * @return array List of all records.
     */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve a specific record by its ID.
     *
     * @param  int $id The ID of the record.
     * @return array|null The record if found, or null if not.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Filter the given data array to include only allowed fields.
     *
     * @param array $data The data to filter.
     * @return array The filtered data.
     */
    protected function filterAllowedFields(array $data): array
    {
      $allowedFields = array_keys($this->getFieldDefinitions());
      return array_intersect_key($data, array_flip($allowedFields));
    }

    /**
     * Create a new record in the table.
     *
     * @param  array $data Key-value pairs of column names and values.
     * @return bool True if the record was created, false otherwise.
     * @throws InvalidArgumentException If validation fails.
     */
    public function create(array $data): bool
    {
        $data = $this->filterAllowedFields($data);

        // Validate the data
        $validation = $this->validate($data);

        if (!$validation['valid']) {
            throw new InvalidArgumentException('Validation failed: ' . json_encode($validation['errors']));
        }

    try {
        // Prepare the SQL query for insertion
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($query);

        $success = $stmt->execute($data);

        $this->lastInsertId = $success ? (int) $this->pdo->lastInsertId() : null;

        return $success;
    } catch (\PDOException $e) {

        // PDOExceptionをRuntimeExceptionに変換
        throw new \RuntimeException(
            'Database error: ' . $e->getMessage(),
            $e->getCode(),
            $e
        );
    }
    }

    public function getLastInsertId(): ?int
    {
        return $this->lastInsertId;
    }

    /**
     * Update a record in the table by its ID.
     *
     * @param  int   $id   The ID of the record to update.
     * @param  array $data Key-value pairs of column names and values to update.
     * @return bool True if the record was updated, false otherwise.
     */
    public function update(int $id, array $data): bool
    {
        $data = $this->filterAllowedFields($data);

        // Validate the data
        $validation = $this->validate($data);

        if (!$validation['valid']) {
            throw new InvalidArgumentException('Validation failed: ' . json_encode($validation['errors']));
        }

        // Prepare the SQL query for update
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));

        $query = "UPDATE {$this->table} SET $setClause WHERE id = :id";
        $stmt = $this->pdo->prepare($query);

        $data['id'] = $id;
        return $stmt->execute($data);
    }

    /**
     * Delete a record from the table by its ID.
     *
     * @param  int $id The ID of the record to delete.
     * @return bool True if the record was deleted, false otherwise.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
