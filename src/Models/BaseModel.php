<?php

namespace Jidaikobo\Kontiki\Models;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Jidaikobo\Kontiki\Services\ValidationService;

/**
 * BaseModel provides common CRUD operations for database interactions.
 * Extend this class to create specific models for different database tables.
 */
abstract class BaseModel implements ModelInterface
{
    protected Connection $db;
    protected ValidationService $validationService;
    protected string $table;
    protected string $deleteType = 'hardDelete';

    /**
     * BaseModel constructor.
     */
    public function __construct(Connection $db, ValidationService $validationService)
    {
        $this->db = $db;
        $this->validationService = $validationService;
    }

    public function getDeleteType(): string
    {
        return $this->deleteType;
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    abstract public function getDisplayFields(): array;

    /**
     * Get the field definitions.
     *
     * @param array $params Optional parameters for dynamic adjustments.
     * @return array Field definitions.
     */
    abstract public function getFieldDefinitions(array $params = []): array;

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
     * Get searchable fields from the model's properties.
     *
     * @return array
     */
    protected function getSearchableFields(): array
    {
        $searchableColumns = [];
        foreach ($this->getFieldDefinitions() as $column => $config) {
            if (isset($config['searchable']) && $config['searchable'] === true) {
                $searchableColumns[] = $column;
            }
        }
        return $searchableColumns;
    }

    /**
     * Validate the given data against the field definitions.
     *
     * @param  array $data The data to validate.
     * @param  array $fieldDefinitions The field definitions.
     *
     * @return array An array with 'valid' (bool) and 'errors' (array of errors).
     */
    public function validateByFields(array $data, array $fieldDefinitions): array
    {
        return $this->validationService->validate($data, $fieldDefinitions);
    }

    public function processFieldDefinitions(string $context, array $fieldDefinitions): array
    {
        return $fieldDefinitions;
    }

    /**
     * Get options in the form of id => field value.
     *
     * @param string $fieldName The field name to use as the value.
     * @param bool $includeEmpty Whether to include an empty option at the start.
     * @param string $emptyLabel The label for the empty option (default: '').
     * @return array Associative array of id => field value.
     */
    public function getOptions(string $fieldName, bool $includeEmpty = false, string $emptyLabel = ''): array
    {
        if (empty($fieldName)) {
            throw new \InvalidArgumentException('Field name cannot be empty.');
        }

        $results = $this->db->table($this->table)
            ->select(['id', $fieldName])
            ->get();

        $options = [];
        foreach ($results as $row) {
            if (isset($row->id, $row->$fieldName)) {
                $options[$row->id] = $row->$fieldName;
            }
        }

        if ($includeEmpty) {
            $options = ['' => $emptyLabel] + $options;
        }

        return $options;
    }

    /**
     * Filter the given data array to include only allowed fields.
     *
     * @param array $data The data to filter.
     *
     * @return array The filtered data.
     */
    public function filterAllowedFields(array $data): array
    {
        $allowedFields = array_keys($this->getFieldDefinitions());
        return array_intersect_key($data, array_flip($allowedFields));
    }

    public function getById(int $id): ?array
    {
        $result = $this->db->table($this->table)
            ->where('id', $id)
            ->first();

        return $result ? (array)$result : null;
    }

    public function getByField(string $field, mixed $value): ?array
    {
        $result = $this->db->table($this->table)
            ->where($field, $value)
            ->first();

        return $result ? (array)$result : null;
    }

    /**
     * Create a new record in the table.
     *
     * @param array $data Key-value pairs of column names and values.
     *
     * @return int|null The ID of the newly created record, or null if the operation failed.
     * @throws InvalidArgumentException If validation fails.
     */
    public function create(array $data, bool $skipFieldFilter = false): ?int
    {
        if (!$skipFieldFilter) {
            $data = $this->filterAllowedFields($data);
        }
        $success = $this->db->table($this->table)->insert($data);
        return $success ? $this->db->getPdo()->lastInsertId() : null;
    }

    /**
     * Update a record in the table by its ID.
     *
     * @param  int   $id   The ID of the record to update.
     * @param  array $data Key-value pairs of column names and values to update.
     *
     * @return bool True if the record was updated, false otherwise.
     */
    public function update(int $id, array $data, bool $skipFieldFilter = false): bool
    {
        if (!$skipFieldFilter) {
            $data = $this->filterAllowedFields($data);
        }
        return $this->db->table($this->table)
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Delte a record in the table by its ID.
     *
     * @param  int   $id   The ID of the record to update.
     *
     * @return bool True if the record was updated, false otherwise.
     */
    public function delete(int $id): bool
    {
        if (!$this->getById($id)) {
            return false;
        }
        return (bool)$this->db->table($this->table)
            ->where('id', $id)
            ->delete();
    }

    public function getAdditionalConditions(Builder $query, string $context = 'normal' ): Builder
    {
        return $query;
    }

    public function buildSearchConditions(string $keyword = ''): Builder
    {
        $query = $this->db->table($this->table);

        // キーワード条件
        if (!empty($keyword)) {
            $searchableColumns = $this->getSearchableFields();

            $query->where(function ($q) use ($keyword, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$keyword}%");
                }
            });
        }

        return $query;
    }
}
