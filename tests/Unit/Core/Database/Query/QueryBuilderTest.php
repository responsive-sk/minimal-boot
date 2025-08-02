<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Core\Database\Query;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Query\QueryBuilder;
use MinimalTest\TestCase;
use PDO;

/**
 * Unit tests for QueryBuilder
 */
class QueryBuilderTest extends TestCase
{
    private PDO $pdo;
    private QueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        
        $connectionFactory = new DatabaseConnectionFactory(':memory:');
        $this->pdo = $connectionFactory->getConnection('test_query_builder');
        $this->queryBuilder = new QueryBuilder($this->pdo);
        
        // Create test table with unique name
        $tableName = 'test_users_' . uniqid();
        $this->pdo->exec("
            CREATE TABLE {$tableName} (
                id INTEGER PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE,
                age INTEGER,
                active BOOLEAN DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Insert test data
        $this->pdo->exec("
            INSERT INTO test_users (name, email, age, active) VALUES 
            ('John Doe', 'john@example.com', 30, 1),
            ('Jane Smith', 'jane@example.com', 25, 1),
            ('Bob Wilson', 'bob@example.com', 35, 0),
            ('Alice Brown', 'alice@example.com', 28, 1)
        ");
    }

    public function testCanCreateQueryBuilder(): void
    {
        $builder = new QueryBuilder($this->pdo);
        
        $this->assertInstanceOf(QueryBuilder::class, $builder);
    }

    public function testSelectAll(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->select();
        
        $this->assertCount(4, $results);
        $this->assertEquals('John Doe', $results[0]['name']);
    }

    public function testSelectWithSpecificColumns(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->select(['name', 'email']);
        
        $this->assertCount(4, $results);
        $this->assertArrayHasKey('name', $results[0]);
        $this->assertArrayHasKey('email', $results[0]);
        $this->assertArrayNotHasKey('age', $results[0]);
    }

    public function testSelectWithWhere(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->where('active', '=', 1)
            ->select();
        
        $this->assertCount(3, $results);
        
        foreach ($results as $user) {
            $this->assertEquals(1, $user['active']);
        }
    }

    public function testSelectWithMultipleWhere(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->where('active', '=', 1)
            ->where('age', '>', 27)
            ->select();
        
        $this->assertCount(2, $results);
    }

    public function testSelectWithOrWhere(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->where('name', '=', 'John Doe')
            ->orWhere('name', '=', 'Jane Smith')
            ->select();
        
        $this->assertCount(2, $results);
    }

    public function testSelectWithOrderBy(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->orderBy('age', 'DESC')
            ->select();
        
        $this->assertEquals('Bob Wilson', $results[0]['name']); // age 35
        $this->assertEquals('Jane Smith', $results[3]['name']); // age 25
    }

    public function testSelectWithLimit(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->limit(2)
            ->select();
        
        $this->assertCount(2, $results);
    }

    public function testSelectWithOffset(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->orderBy('id', 'ASC')
            ->limit(2)
            ->offset(1)
            ->select();
        
        $this->assertCount(2, $results);
        $this->assertEquals('Jane Smith', $results[0]['name']);
    }

    public function testSelectFirst(): void
    {
        $result = $this->queryBuilder
            ->table('test_users')
            ->where('name', '=', 'John Doe')
            ->first();
        
        $this->assertNotNull($result);
        $this->assertEquals('John Doe', $result['name']);
        $this->assertEquals('john@example.com', $result['email']);
    }

    public function testSelectFirstReturnsNullWhenNotFound(): void
    {
        $result = $this->queryBuilder
            ->table('test_users')
            ->where('name', '=', 'Non Existent')
            ->first();
        
        $this->assertNull($result);
    }

    public function testInsert(): void
    {
        $data = [
            'name' => 'New User',
            'email' => 'new@example.com',
            'age' => 22,
            'active' => 1
        ];
        
        $result = $this->queryBuilder
            ->table('test_users')
            ->insert($data);
        
        $this->assertTrue($result);
        
        // Verify insertion
        $inserted = $this->queryBuilder
            ->table('test_users')
            ->where('email', '=', 'new@example.com')
            ->first();
        
        $this->assertNotNull($inserted);
        $this->assertEquals('New User', $inserted['name']);
    }

    public function testUpdate(): void
    {
        $result = $this->queryBuilder
            ->table('test_users')
            ->where('name', '=', 'John Doe')
            ->update(['age' => 31]);
        
        $this->assertTrue($result);
        
        // Verify update
        $updated = $this->queryBuilder
            ->table('test_users')
            ->where('name', '=', 'John Doe')
            ->first();
        
        $this->assertEquals(31, $updated['age']);
    }

    public function testDelete(): void
    {
        $result = $this->queryBuilder
            ->table('test_users')
            ->where('name', '=', 'Bob Wilson')
            ->delete();
        
        $this->assertTrue($result);
        
        // Verify deletion
        $deleted = $this->queryBuilder
            ->table('test_users')
            ->where('name', '=', 'Bob Wilson')
            ->first();
        
        $this->assertNull($deleted);
    }

    public function testCount(): void
    {
        $count = $this->queryBuilder
            ->table('test_users')
            ->where('active', '=', 1)
            ->count();
        
        $this->assertEquals(3, $count);
    }

    public function testExists(): void
    {
        $exists = $this->queryBuilder
            ->table('test_users')
            ->where('email', '=', 'john@example.com')
            ->exists();
        
        $this->assertTrue($exists);
        
        $notExists = $this->queryBuilder
            ->table('test_users')
            ->where('email', '=', 'nonexistent@example.com')
            ->exists();
        
        $this->assertFalse($notExists);
    }

    public function testWhereIn(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->whereIn('name', ['John Doe', 'Jane Smith'])
            ->select();
        
        $this->assertCount(2, $results);
    }

    public function testWhereNotIn(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->whereNotIn('name', ['John Doe', 'Jane Smith'])
            ->select();
        
        $this->assertCount(2, $results);
        $this->assertEquals('Bob Wilson', $results[0]['name']);
        $this->assertEquals('Alice Brown', $results[1]['name']);
    }

    public function testWhereBetween(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->whereBetween('age', 25, 30)
            ->select();
        
        $this->assertCount(3, $results); // John (30), Jane (25), Alice (28)
    }

    public function testWhereNull(): void
    {
        // Insert user with null email
        $this->pdo->exec("INSERT INTO test_users (name, email, age) VALUES ('Null Email', NULL, 40)");
        
        $results = $this->queryBuilder
            ->table('test_users')
            ->whereNull('email')
            ->select();
        
        $this->assertCount(1, $results);
        $this->assertEquals('Null Email', $results[0]['name']);
    }

    public function testWhereNotNull(): void
    {
        // Insert user with null email
        $this->pdo->exec("INSERT INTO test_users (name, email, age) VALUES ('Null Email', NULL, 40)");
        
        $results = $this->queryBuilder
            ->table('test_users')
            ->whereNotNull('email')
            ->select();
        
        $this->assertCount(4, $results); // Original 4 users with emails
    }

    public function testComplexQuery(): void
    {
        $results = $this->queryBuilder
            ->table('test_users')
            ->where('active', '=', 1)
            ->where('age', '>=', 25)
            ->orderBy('age', 'ASC')
            ->limit(2)
            ->select(['name', 'age']);
        
        $this->assertCount(2, $results);
        $this->assertEquals('Jane Smith', $results[0]['name']);
        $this->assertEquals('Alice Brown', $results[1]['name']);
    }
}
