<?php

namespace jidaikobo\kontiki\Database;

use PDO;
use PDOException;
use RuntimeException;

class DatabaseHandler
{
    protected PDO $pdo;

    /**
     * DatabaseHandler constructor.
     *
     * @param PDO $pdo PDO instance for database connection.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve all records from a given table.
     *
     * @param string $table Table name.
     * @return array List of all records.
     */
    public function getAll(string $table): array
    {
        $stmt = $this->pdo->query("SELECT * FROM {$table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve a specific record by its ID.
     *
     * @param string $table Table name.
     * @param int $id Record ID.
     * @return array|null The record if found, or null if not.
     */
    public function getById(string $table, int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Retrieve a specific record by a given field.
     *
     * @param string $table Table name.
     * @param string $field Field name to filter by.
     * @param mixed $value Value to filter by.
     * @return array|null The record if found, or null if not.
     */
    public function getByField(string $table, string $field, mixed $value): ?array
    {
        $query = "SELECT * FROM {$table} WHERE {$field} = :value LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['value' => $value]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Execute an insert query.
     *
     * @param string $table Table name.
     * @param array $data Key-value pairs of column names and values.
     * @return bool True if the insert was successful, false otherwise.
     */
    public function insert(string $table, array $data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

        $query = "INSERT INTO {$table} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($query);

        return $stmt->execute($data);
    }

    /**
     * Execute an update query.
     *
     * @param string $table Table name.
     * @param int $id ID of the record to update.
     * @param array $data Key-value pairs of column names and values to update.
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(string $table, int $id, array $data): bool
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $query = "UPDATE {$table} SET $setClause WHERE id = :id";

        $stmt = $this->pdo->prepare($query);
        $data['id'] = $id;

        return $stmt->execute($data);
    }

    /**
     * Delete a record by its ID.
     *
     * @param string $table Table name.
     * @param int $id ID of the record to delete.
     * @return bool True if the delete was successful, false otherwise.
     */
    public function delete(string $table, int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get the last inserted ID.
     *
     * @return int|null The ID of the last inserted record, or null if no record was inserted.
     */
    public function getLastInsertId(): ?int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Execute a custom query with parameters.
     *
     * @param string $query The SQL query to execute.
     * @param array $params Parameters for the query.
     * @return array Resulting rows as an associative array.
     */
    public function executeQuery(string $query, array $params = []): array
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count all records in a table with optional search conditions.
     *
     * @param string $table Table name.
     * @param string|null $whereClause Optional WHERE clause.
     * @param array $params Parameters for the WHERE clause.
     * @return int The total count of matching records.
     */
    public function countAll(string $table, ?string $whereClause = '', array $params = []): int
    {
        $query = "SELECT COUNT(*) as total FROM {$table} {$whereClause}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public function commit(): void
    {
        $this->pdo->commit();
    }

    /**
     * Rollback the current transaction.
     */
    public function rollback(): void
    {
        $this->pdo->rollBack();
    }
}
