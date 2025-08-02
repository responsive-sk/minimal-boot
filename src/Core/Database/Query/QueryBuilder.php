<?php

declare(strict_types=1);

namespace Minimal\Core\Database\Query;

use PDO;
use PDOStatement;

/**
 * Simple Query Builder for common database operations.
 * 
 * Provides fluent interface for building SQL queries without full ORM complexity.
 */
class QueryBuilder
{
    private PDO $pdo;
    private string $table = '';
    private array $select = ['*'];
    private array $where = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
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
     * Add WHERE IN condition.
     */
    public function whereIn(string $column, array $values): self
    {
        $placeholders = [];
        foreach ($values as $i => $value) {
            $placeholder = ':wherein_' . $i;
            $placeholders[] = $placeholder;
            $this->bindings[$placeholder] = $value;
        }
        
        $this->where[] = "{$column} IN (" . implode(', ', $placeholders) . ")";
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
    public function get(): array
    {
        $sql = $this->buildSelectQuery();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll();
    }

    /**
     * Execute SELECT query and return first result.
     */
    public function first(): ?array
    {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    /**
     * Insert data into table.
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
     */
    public function update(array $data): bool
    {
        $setParts = [];
        foreach ($data as $column => $value) {
            $placeholder = ":update_{$column}";
            $setParts[] = "{$column} = {$placeholder}";
            $this->bindings[$placeholder] = $value;
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);
        
        if (!empty($this->where)) {
            $sql .= " WHERE " . implode(' AND ', $this->where);
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($this->bindings);
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
        
        return (int) $result['count'];
    }

    /**
     * Execute raw SQL query.
     */
    public function raw(string $sql, array $bindings = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
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

    /**
     * Reset query builder state.
     */
    public function reset(): self
    {
        $this->table = '';
        $this->select = ['*'];
        $this->where = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->bindings = [];
        
        return $this;
    }
}
