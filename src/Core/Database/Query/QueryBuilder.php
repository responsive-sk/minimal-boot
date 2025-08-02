<?php

declare(strict_types=1);

namespace Minimal\Core\Database\Query;

use PDO;
use PDOStatement;

use function assert;

/**
 * Simple Query Builder for common database operations.
 *
 * Provides fluent interface for building SQL queries without full ORM complexity.
 */
class QueryBuilder
{
    private PDO $pdo;
    private string $table = '';
    /** @var array<string> */
    private array $select = ['*'];
    /** @var array<string> */
    private array $where = [];
    /** @var array<string> */
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    /** @var array<mixed> */
    private array $bindings = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Set table name.
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Set SELECT columns.
     *
     * @param array<string> $columns
     */
    public function select(array $columns = ['*']): self
    {
        $this->select = $columns;
        return $this;
    }

    /**
     * Add WHERE condition.
     */
    public function where(string $column, string $operator, mixed $value): self
    {
        $placeholder = ':where_' . count($this->where);
        $this->where[] = "{$column} {$operator} {$placeholder}";
        $this->bindings[$placeholder] = $value;
        return $this;
    }



    /**
     * Add ORDER BY clause.
     */
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orderBy[] = "{$column} {$direction}";
        return $this;
    }

    /**
     * Set LIMIT.
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Set OFFSET.
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Execute SELECT query and return all results.
     */
    /**
     * @return array<array<string, mixed>>
     */
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        $result = $stmt->fetchAll();
        assert(is_array($result));
        /** @var array<array<string, mixed>> $result */
        return $result;
    }

    /**
     * Execute SELECT query and return first result.
     */
    /**
     * @return array<string, mixed>|null
     */
    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Insert data into table.
     *
     * @param array<string, mixed> $data
     */
    public function insert(array $data): bool
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":{$col}", $columns);

        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Update data in table.
     *
     * @param array<string, mixed> $data
     */
    public function update(array $data): bool
    {
        $setParts = [];
        $updateBindings = [];

        foreach ($data as $column => $value) {
            $placeholder = ":update_{$column}";
            $setParts[] = "{$column} = {$placeholder}";
            $updateBindings[$placeholder] = $value;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        // Merge update bindings with where bindings
        $allBindings = array_merge($updateBindings, $this->bindings);

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($allBindings);
    }

    /**
     * Delete from table.
     */
    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
    }

    /**
     * Get count of records.
     */
    public function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        $result = $stmt->fetch();

        assert(is_array($result) && isset($result['count']) && is_numeric($result['count']));
        return (int) $result['count'];
    }



    /**
     * Build SELECT query string.
     */
    private function buildSelectQuery(): string
    {
        $sql = "SELECT " . implode(', ', $this->select) . " FROM {$this->table}";

        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }
}
