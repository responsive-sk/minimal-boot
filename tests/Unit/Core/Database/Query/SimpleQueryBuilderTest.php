<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Core\Database\Query;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Query\QueryBuilder;
use MinimalTest\TestCase;
use PDO;

/**
 * Simplified tests for optimized QueryBuilder
 */
class SimpleQueryBuilderTest extends TestCase
{
    private PDO $pdo;
    private QueryBuilder $queryBuilder;
    private string $tableName;

    protected function setUp(): void
    {
        parent::setUp();

        $connectionFactory = new DatabaseConnectionFactory(':memory:');
        $this->pdo = $connectionFactory->getConnection('test_simple_qb_' . uniqid());
        $this->queryBuilder = new QueryBuilder($this->pdo);
        $this->tableName = 'test_table_' . uniqid();

        // Create test table
        $this->pdo->exec("
            CREATE TABLE {$this->tableName} (
                id INTEGER PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255),
                active BOOLEAN DEFAULT 1
            )
        ");

        // Insert test data
        $this->pdo->exec("
            INSERT INTO {$this->tableName} (name, email, active) VALUES 
            ('John Doe', 'john@example.com', 1),
            ('Jane Smith', 'jane@example.com', 1),
            ('Bob Wilson', 'bob@example.com', 0)
        ");
    }

    public function testBasicSelect(): void
    {
        $results = $this->queryBuilder
            ->table($this->tableName)
            ->get();

        $this->assertCount(3, $results);
        $this->assertEquals('John Doe', $results[0]['name']);
    }

    public function testSelectWithWhere(): void
    {
        $results = $this->queryBuilder
            ->table($this->tableName)
            ->where('active', '=', 1)
            ->get();

        $this->assertCount(2, $results);
    }

    public function testSelectFirst(): void
    {
        $result = $this->queryBuilder
            ->table($this->tableName)
            ->where('name', '=', 'John Doe')
            ->first();

        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result['name']);
    }

    public function testInsert(): void
    {
        $result = $this->queryBuilder
            ->table($this->tableName)
            ->insert([
                'name' => 'New User',
                'email' => 'new@example.com',
                'active' => 1
            ]);

        $this->assertTrue($result);

        // Verify insertion
        $inserted = $this->queryBuilder
            ->table($this->tableName)
            ->where('email', '=', 'new@example.com')
            ->first();

        $this->assertNotNull($inserted);
        $this->assertEquals('New User', $inserted['name']);
    }

    public function testUpdate(): void
    {
        $result = $this->queryBuilder
            ->table($this->tableName)
            ->where('name', '=', 'John Doe')
            ->update(['email' => 'john.updated@example.com']);

        $this->assertTrue($result);

        // Verify update
        $updated = $this->queryBuilder
            ->table($this->tableName)
            ->where('name', '=', 'John Doe')
            ->first();

        $this->assertEquals('john.updated@example.com', $updated['email']);
    }

    public function testDelete(): void
    {
        $result = $this->queryBuilder
            ->table($this->tableName)
            ->where('name', '=', 'Bob Wilson')
            ->delete();

        $this->assertTrue($result);

        // Verify deletion
        $deleted = $this->queryBuilder
            ->table($this->tableName)
            ->where('name', '=', 'Bob Wilson')
            ->first();

        $this->assertNull($deleted);
    }

    public function testCount(): void
    {
        $count = $this->queryBuilder
            ->table($this->tableName)
            ->where('active', '=', 1)
            ->count();

        $this->assertEquals(2, $count);
    }

    public function testOrderBy(): void
    {
        $results = $this->queryBuilder
            ->table($this->tableName)
            ->orderBy('name', 'DESC')
            ->get();

        $this->assertEquals('John Doe', $results[0]['name']);
        $this->assertEquals('Jane Smith', $results[1]['name']);
        $this->assertEquals('Bob Wilson', $results[2]['name']);
    }

    public function testLimit(): void
    {
        $results = $this->queryBuilder
            ->table($this->tableName)
            ->limit(2)
            ->get();

        $this->assertCount(2, $results);
    }

    public function testOffset(): void
    {
        $results = $this->queryBuilder
            ->table($this->tableName)
            ->orderBy('id', 'ASC')
            ->limit(1)
            ->offset(1)
            ->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Jane Smith', $results[0]['name']);
    }

    public function testSelectSpecificColumns(): void
    {
        $results = $this->queryBuilder
            ->table($this->tableName)
            ->select(['name', 'email'])
            ->get();

        $this->assertCount(3, $results);
        $this->assertArrayHasKey('name', $results[0]);
        $this->assertArrayHasKey('email', $results[0]);
        $this->assertArrayNotHasKey('active', $results[0]);
    }
}
