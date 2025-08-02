<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Core\Database\Connection;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use MinimalTest\TestCase;
use PDO;
use RuntimeException;

/**
 * Unit tests for DatabaseConnectionFactory
 */
class DatabaseConnectionFactoryTest extends TestCase
{
    private string $testDbPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDbPath = sys_get_temp_dir() . '/minimal_boot_test_' . uniqid();
        mkdir($this->testDbPath, 0755, true);
    }

    protected function tearDown(): void
    {
        // Clean up test database files
        if (is_dir($this->testDbPath)) {
            $files = glob($this->testDbPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($this->testDbPath);
        }

        parent::tearDown();
    }

    public function testCanCreateConnectionFactory(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        $this->assertInstanceOf(DatabaseConnectionFactory::class, $factory);
    }

    public function testCanGetConnectionForModule(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        $connection = $factory->getConnection('test_module');

        $this->assertInstanceOf(PDO::class, $connection);
    }

    public function testConnectionsAreReused(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        $connection1 = $factory->getConnection('test_module');
        $connection2 = $factory->getConnection('test_module');

        $this->assertSame($connection1, $connection2);
    }

    public function testDifferentModulesGetDifferentConnections(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        $connection1 = $factory->getConnection('module1');
        $connection2 = $factory->getConnection('module2');

        $this->assertNotSame($connection1, $connection2);
    }

    public function testInMemoryDatabase(): void
    {
        $factory = new DatabaseConnectionFactory(':memory:');

        $connection = $factory->getConnection('memory_test_' . uniqid());

        $this->assertInstanceOf(PDO::class, $connection);

        // Test that we can create a table in memory
        $tableName = 'test_table_' . uniqid();
        $connection->exec("CREATE TABLE {$tableName} (id INTEGER PRIMARY KEY, name TEXT)");
        $connection->exec("INSERT INTO {$tableName} (name) VALUES ('test')");

        $stmt = $connection->query("SELECT COUNT(*) FROM {$tableName}");
        $count = $stmt->fetchColumn();

        $this->assertEquals(1, $count);
    }

    public function testModuleExists(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        // Module doesn't exist initially
        $this->assertFalse($factory->moduleExists('test_module'));

        // Create connection (which creates the database file)
        $factory->getConnection('test_module');

        // Now module should exist
        $this->assertTrue($factory->moduleExists('test_module'));
    }

    public function testCreateModuleDatabase(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        $this->assertFalse($factory->moduleExists('new_module'));

        $factory->createModuleDatabase('new_module');

        $this->assertTrue($factory->moduleExists('new_module'));
    }

    public function testGetAvailableModules(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        // Initially no modules
        $this->assertEquals([], $factory->getAvailableModules());

        // Create some modules
        $factory->getConnection('module1');
        $factory->getConnection('module2');

        $modules = $factory->getAvailableModules();
        $this->assertCount(2, $modules);
        $this->assertContains('module1', $modules);
        $this->assertContains('module2', $modules);
    }

    public function testConnectionHasCorrectAttributes(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        $connection = $factory->getConnection('attr_test');

        // Test PDO attributes are set correctly
        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $connection->getAttribute(PDO::ATTR_ERRMODE));
        $this->assertEquals(PDO::FETCH_ASSOC, $connection->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE));

        // SQLite driver does not support ATTR_EMULATE_PREPARES attribute, so skip this test
        $this->markTestSkipped('SQLite driver does not support ATTR_EMULATE_PREPARES attribute');
    }

    public function testForeignKeysAreEnabled(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        $connection = $factory->getConnection('fk_test');

        // Check if foreign keys are enabled
        $stmt = $connection->query("PRAGMA foreign_keys");
        $result = $stmt->fetchColumn();

        $this->assertEquals('1', $result);
    }

    public function testMigrationsTableIsCreated(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        $connection = $factory->getConnection('migrations_test');

        // Check if migrations table exists
        $stmt = $connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
        $result = $stmt->fetchColumn();

        $this->assertEquals('migrations', $result);
    }

    public function testCloseConnections(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        // Create some connections
        $factory->getConnection('module1');
        $factory->getConnection('module2');

        // Close all connections
        $factory->closeConnections();

        // Getting connection again should create new instances
        $newConnection = $factory->getConnection('module1');
        $this->assertInstanceOf(PDO::class, $newConnection);
    }

    public function testInvalidDatabasePathThrowsException(): void
    {
        // Try to create factory with invalid path (file instead of directory)
        $invalidPath = $this->testDbPath . '/invalid_file.txt';
        file_put_contents($invalidPath, 'test');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to create database directory');

        new DatabaseConnectionFactory($invalidPath);
    }
}
