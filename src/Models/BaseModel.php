<?php

namespace Jidaikobo\Kontiki\Models;

use Jidaikobo\Kontiki\Database\DatabaseHandler;
use Jidaikobo\Kontiki\Services\ValidationService;
use Jidaikobo\Kontiki\Utils\Env;
use Valitron\Validator;

/**
 * BaseModel provides common CRUD operations for database interactions.
 * Extend this class to create specific models for different database tables.
 */
abstract class BaseModel implements ModelInterface
{
    protected DatabaseHandler $db;
    protected ValidationService $validationService;
    protected string $table;

    /**
     * BaseModel constructor.
     */
    public function __construct(DatabaseHandler $db, ValidationService $validationService)
    {
        $this->db = $db;
        $this->validationService = $validationService;
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

    public function processCreateFieldDefinitions(array $fieldDefinitions): array
    {
        return $fieldDefinitions;
    }

    public function processEditFieldDefinitions(array $fieldDefinitions): array
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

        $query = "SELECT id, {$fieldName} FROM {$this->table}";
        $results = $this->db->executeQuery($query);

        $options = [];
        foreach ($results as $row) {
            if (isset($row['id'], $row[$fieldName])) {
                $options[$row['id']] = $row[$fieldName];
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
     * @return array The filtered data.
     */
    public function filterAllowedFields(array $data): array
    {
        $allowedFields = array_keys($this->getFieldDefinitions());
        return array_intersect_key($data, array_flip($allowedFields));
    }

    public function getById(int $id): ?array
    {
        return $this->db->getById($this->table, $id);
    }

    public function getByField(string $field, mixed $value): ?array
    {
        return $this->db->getByField($this->table, $field, $value);
    }

    /**
     * Create a new record in the table.
     *
     * @param array $data Key-value pairs of column names and values.
     * @return int|null The ID of the newly created record, or null if the operation failed.
     * @throws InvalidArgumentException If validation fails.
     */
    public function create(array $data): ?int
    {
        $filteredData = $this->filterAllowedFields($data);
        $success = $this->db->insert($this->table, $filteredData);
        return $success ? $this->db->getLastInsertId() : null;
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
        return $this->db->update($this->table, $id, $data);
    }

    public function trash($id)
    {
        $data = $this->getById($id);
        if (!$data) {
            return false;
        }
        $data['deleted_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $id, $data);
    }

    public function restore($id)
    {
        $data = $this->getById($id);
        if (!$data) {
            return false;
        }
        $data['deleted_at'] = null;
        return $this->db->update($this->table, $id, $data);
    }

    public function delete(int $id): bool
    {
        if (!$this->getById($id)) {
            return false;
        }
        return $this->db->delete($this->table, $id);
    }

    public function getAdditionalConditions(string $context = 'normal', string $deleteType = 'hardDelete', array $options = []): array
    {
        $additionalConditions = [];

        // conditions by context
        if ($context === 'normal' && $deleteType === 'softDelete') {
            // 通常記事（削除されておらず、公開中かつ有効期限内）
            $additionalConditions['deleted_at'] = null;
            $additionalConditions['published_at'] = ['operator' => '<=', 'value' => date('Y-m-d H:i:s')];
            $additionalConditions['expired_at'] = [
                'condition' => '(expired_at IS NULL OR expired_at > :current_time)',
                'params' => [':current_time' => date('Y-m-d H:i:s')],
            ];
        } elseif ($context === 'trash') {
            // trash
            $additionalConditions['deleted_at'] = 'NOT NULL';
        } elseif ($context === 'reserved') {
            // 予約記事（削除されておらず、公開予定）
            $additionalConditions['deleted_at'] = null;
            $additionalConditions['published_at'] = ['operator' => '>', 'value' => date('Y-m-d H:i:s')];
        } elseif ($context === 'expired') {
            // 期限切れ記事
            $additionalConditions['deleted_at'] = null;
            $additionalConditions['expired_at'] = ['operator' => '<=', 'value' => date('Y-m-d H:i:s')];
        }

        // 単記事取得用の条件
        if (!empty($options['id'])) {
            $additionalConditions['id'] = $options['id'];
        }
        if (!empty($options['slug'])) {
            $additionalConditions['slug'] = $options['slug'];
        }

        return $additionalConditions;
    }

    /**
     * Get searchable columns from the model's properties.
     *
     * @return array
     */
    protected function getSearchableColumns(): array
    {
        $searchableColumns = [];
        foreach ($this->getFieldDefinitions() as $column => $config) {
            if (isset($config['searchable']) && $config['searchable'] === true) {
                $searchableColumns[] = $column;
            }
        }
        return $searchableColumns;
    }

    public function buildSearchConditions(
        string $keyword = '',
        array $customSearchableColumns = [],
        array $additionalConditions = []
    ): array {
        $whereClauses = [];
        $params = [];

        // キーワード条件
        if (!empty($keyword)) {
            $searchableColumns = $customSearchableColumns ?: $this->getSearchableColumns();
            $keywordConditions = array_map(fn($col) => "{$col} LIKE :keyword", $searchableColumns);
            $whereClauses[] = '(' . implode(' OR ', $keywordConditions) . ')';
            $params[':keyword'] = "%{$keyword}%";
        }

        // 追加条件
        foreach ($additionalConditions as $field => $value) {
            if (is_array($value) && isset($value['condition'], $value['params'])) {
                // 複合条件
                $whereClauses[] = $value['condition'];
                $params = array_merge($params, $value['params']);
            } else if (is_string($field)) {
                // 単一フィールド条件
                if (is_null($value)) {
                    $whereClauses[] = "{$field} IS NULL";
                } elseif (is_array($value) && isset($value['operator'], $value['value'])) {
                    // 条件が配列形式（例: ['operator' => '>=', 'value' => '2024-12-29']）
                    $whereClauses[] = "{$field} {$value['operator']} :{$field}";
                    $params[":{$field}"] = $value['value'];
                } elseif (is_string($value) && strtoupper($value) === 'NOT NULL') {
                    // 特殊条件 'NOT NULL'
                    $whereClauses[] = "{$field} IS NOT NULL";
                } elseif (is_string($value) && strtoupper($value) === 'NULL') {
                    // 特殊条件 'NULL'
                    $whereClauses[] = "{$field} IS NULL";
                } else {
                    // 通常の '=' 条件
                    $whereClauses[] = "{$field} = :{$field}";
                    $params[":{$field}"] = $value;
                }
            }
        }

        // WHERE句の構築
        $where = $whereClauses ? ' WHERE ' . implode(' AND ', $whereClauses) : '';

        return ['where' => $where, 'params' => $params];
    }

    public function countByConditions(string $where, array $params): int
    {
        return $this->db->countAll($this->table, $where, $params);
    }

    public function searchByConditions(string $where, array $params, int $offset, int $limit): array
    {
        $query = "SELECT * FROM {$this->table} {$where} LIMIT :limit OFFSET :offset";
        $params = array_merge($params, [
            ':limit' => $limit,
            ':offset' => $offset,
        ]);

        return $this->db->executeQuery($query, $params);
    }
}
