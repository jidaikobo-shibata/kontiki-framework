<?php

namespace jidaikobo\kontiki\Models;

use jidaikobo\kontiki\Database\DatabaseHandler;
use jidaikobo\kontiki\Utils\Env;
use Valitron\Validator;

/**
 * BaseModel provides common CRUD operations for database interactions.
 * Extend this class to create specific models for different database tables.
 */
abstract class BaseModel implements ModelInterface
{
    protected DatabaseHandler $db;
    protected string $table;

    /**
     * BaseModel constructor.
     */
    public function __construct(DatabaseHandler $db)
    {
        $this->db = $db;
    }

    // 削除タイプを取得（Hard DeleteまたはSoft Delete）
    public function getDeleteType(): string
    {
        return 'hard';
    }

    // アクション定義を取得
    public function getActions(string $context): array
    {
        if ($this->getDeleteType() === 'hard') {
            return ['edit', 'delete'];
        }

        if ($context === 'trash') {
            return ['restore', 'delete'];
        }

        return ['edit', 'trash'];
    }

    public function getTableName(): string
    {
        return $this->table;
    }
    abstract public function getDisplayFields(): array;

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

        return [
            'valid' => $isValid,
            'errors' => $isValid ? [] : $validator->errors(),
        ];
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
        // バリデーション: フィールド名が空でないことを確認
        if (empty($fieldName)) {
            throw new \InvalidArgumentException('Field name cannot be empty.');
        }

        // SQLクエリの実行: id と 指定フィールド名を取得
        $query = "SELECT id, {$fieldName} FROM {$this->table}";
        $results = $this->db->executeQuery($query);

        // id => 指定フィールド名の配列に加工
        $options = [];
        foreach ($results as $row) {
            if (isset($row['id'], $row[$fieldName])) {
                $options[$row['id']] = $row[$fieldName];
            }
        }

        // 空の要素を先頭に追加
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
        return $this->db->insert($this->table, $data);
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

    public function countByKeyword(string $keyword): int
    {
        $conditions = $this->buildSearchConditions($keyword);
        return $this->db->countAll($this->table, $conditions['where'], $conditions['params']);
    }

    /**
     * Build search conditions and parameters for SQL queries.
     *
     * @param string $keyword Search keyword for filtering.
     * @param array $customSearchableColumns Optional custom searchable columns.
     * @return array An associative array with 'where' (SQL WHERE clause) and 'params' (parameters).
     */
    public function buildSearchConditions(string $keyword = '', array $customSearchableColumns = []): array
    {
        // 検索対象のカラムを取得
        $searchableColumns = !empty($customSearchableColumns)
            ? $customSearchableColumns
            : $this->getSearchableColumns();

        $whereClause = '';
        $params = [];

        if (!empty($keyword) && !empty($searchableColumns)) {
            $conditions = [];
            foreach ($searchableColumns as $column) {
                $conditions[] = "{$column} LIKE :keyword";
            }
            $whereClause = " WHERE " . implode(' OR ', $conditions);
            $params[':keyword'] = "%{$keyword}%";
        }

        return ['where' => $whereClause, 'params' => $params];
    }

    /**
     * Get paginated data with optional keyword filtering.
     *
     * @param string $keyword Search keyword.
     * @param int $offset SQL offset.
     * @param int $limit SQL limit.
     * @param array $customSearchableColumns Custom searchable columns.
     * @return array Fetched data.
     */
    public function search(string $keyword = '', int $offset = 0, int $limit = 10, array $customSearchableColumns = []): array
    {
        $searchConditions = $this->buildSearchConditions($keyword, $customSearchableColumns);

        $query = "SELECT * FROM {$this->table} {$searchConditions['where']} LIMIT :limit OFFSET :offset";
        $params = array_merge($searchConditions['params'], [
            ':limit' => $limit,
            ':offset' => $offset,
        ]);

        return $this->db->executeQuery($query, $params);
    }
}
