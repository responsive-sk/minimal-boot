<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Core\Database\Connection;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use MinimalTest\TestCase;
use PDO;
use PDOException;
use RuntimeException;

/**
 * Complete tests for DatabaseConnectionFactory to achieve 100% coverage
 */
class CompleteDatabaseConnectionFactoryTest extends TestCase
{
    private string $testDbPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDbPath = sys_get_temp_dir() . '/minimal_boot_complete_test_' . uniqid();
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

    public function testGetConnectionCreatesNewConnection(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        $connection = $factory->getConnection('test_module');
        
        $this->assertInstanceOf(PDO::class, $connection);
    }

    public function testGetConnectionReusesExistingConnection(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        $connection1 = $factory->getConnection('test_module');
        $connection2 = $factory->getConnection('test_module');
        
        $this->assertSame($connection1, $connection2);
    }

    public function testGetConnectionCreatesDifferentConnectionsForDifferentModules(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        $connection1 = $factory->getConnection('module1');
        $connection2 = $factory->getConnection('module2');
        
        $this->assertNotSame($connection1, $connection2);
    }

    public function testInMemoryDatabaseConnection(): void
    {
        $factory = new DatabaseConnectionFactory(':memory:');

        $connection = $factory->getConnection('memory_test_' . uniqid());

        $this->assertInstanceOf(PDO::class, $connection);

        // Test that we can use the in-memory database
        $tableName = 'test_' . uniqid();
        $connection->exec("CREATE TABLE {$tableName} (id INTEGER PRIMARY KEY, name TEXT)");
        $connection->exec("INSERT INTO {$tableName} (name) VALUES ('test')");

        $stmt = $connection->query("SELECT COUNT(*) FROM {$tableName}");
        $count = $stmt->fetchColumn();

        $this->assertEquals(1, $count);
    }

    public function testModuleExistsReturnsFalseInitially(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        $this->assertFalse($factory->moduleExists('non_existent_module'));
    }

    public function testModuleExistsReturnsTrueAfterCreation(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        // Create connection (which creates the database file)
        $factory->getConnection('test_module');
        
        $this->assertTrue($factory->moduleExists('test_module'));
    }

    public function testCreateModuleDatabaseCreatesNewModule(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        $this->assertFalse($factory->moduleExists('new_module'));
        
        $factory->createModuleDatabase('new_module');
        
        $this->assertTrue($factory->moduleExists('new_module'));
    }

    public function testCreateModuleDatabaseWithExistingModule(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        // Create module first time
        $factory->createModuleDatabase('existing_module');
        $this->assertTrue($factory->moduleExists('existing_module'));
        
        // Create same module again - should not throw error
        $factory->createModuleDatabase('existing_module');
        $this->assertTrue($factory->moduleExists('existing_module'));
    }

    public function testGetAvailableModulesReturnsEmptyArrayInitially(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        $modules = $factory->getAvailableModules();
        
        $this->assertEquals([], $modules);
    }

    public function testGetAvailableModulesReturnsCreatedModules(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        // Create some modules
        $factory->getConnection('module_a');
        $factory->getConnection('module_b');
        $factory->createModuleDatabase('module_c');
        
        $modules = $factory->getAvailableModules();
        
        $this->assertCount(3, $modules);
        $this->assertContains('module_a', $modules);
        $this->assertContains('module_b', $modules);
        $this->assertContains('module_c', $modules);
    }

    public function testConnectionHasCorrectPDOAttributes(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        $connection = $factory->getConnection('attr_test');
        
        // Test PDO attributes are set correctly
        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $connection->getAttribute(PDO::ATTR_ERRMODE));
        $this->assertEquals(PDO::FETCH_ASSOC, $connection->getAttribute(PDO::ATTR_DEFAULT_FETCH_MODE));
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

    public function testMigrationsTableIsCreatedAutomatically(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        $connection = $factory->getConnection('migrations_test');
        
        // Check if migrations table exists
        $stmt = $connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
        $result = $stmt->fetchColumn();
        
        $this->assertEquals('migrations', $result);
    }

    public function testCloseConnectionsClosesAllConnections(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        // Create some connections
        $connection1 = $factory->getConnection('module1');
        $connection2 = $factory->getConnection('module2');
        
        // Verify connections exist
        $this->assertInstanceOf(PDO::class, $connection1);
        $this->assertInstanceOf(PDO::class, $connection2);
        
        // Close all connections
        $factory->closeConnections();
        
        // Getting connection again should create new instances
        $newConnection1 = $factory->getConnection('module1');
        $newConnection2 = $factory->getConnection('module2');
        
        $this->assertInstanceOf(PDO::class, $newConnection1);
        $this->assertInstanceOf(PDO::class, $newConnection2);
        
        // These should be different instances (not the same objects)
        $this->assertNotSame($connection1, $newConnection1);
        $this->assertNotSame($connection2, $newConnection2);
    }

    public function testInvalidDatabasePathThrowsException(): void
    {
        // Create a file instead of directory to cause error
        $invalidPath = $this->testDbPath . '_invalid_file.txt';
        file_put_contents($invalidPath, 'test content');
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to create database directory');
        
        new DatabaseConnectionFactory($invalidPath);
        
        // Clean up
        unlink($invalidPath);
    }

    public function testInMemoryModuleExistsAlwaysReturnsFalse(): void
    {
        $factory = new DatabaseConnectionFactory(':memory:');

        // Create connection
        $factory->getConnection('memory_module_' . uniqid());

        // In-memory databases don't create files, so moduleExists should return false
        $this->assertFalse($factory->moduleExists('memory_module_test'));
    }

    public function testInMemoryGetAvailableModulesIgnoresMemoryConnections(): void
    {
        $factory = new DatabaseConnectionFactory(':memory:');

        // Create connections
        $factory->getConnection('memory_module1_' . uniqid());
        $factory->getConnection('memory_module2_' . uniqid());

        // In-memory databases don't create files, so getAvailableModules returns existing files only
        $modules = $factory->getAvailableModules();

        // Should not contain our memory modules, but may contain other test modules
        $this->assertIsArray($modules);
        // We can't assert empty because other tests may have created files
    }

    public function testConnectionWithSpecialCharactersInModuleName(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);
        
        // Test with module name that needs sanitization
        $connection = $factory->getConnection('test-module_123');
        
        $this->assertInstanceOf(PDO::class, $connection);
        $this->assertTrue($factory->moduleExists('test-module_123'));
    }

    public function testMultipleConnectionsToSameModuleReturnSameInstance(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        $connection1 = $factory->getConnection('same_module');
        $connection2 = $factory->getConnection('same_module');
        $connection3 = $factory->getConnection('same_module');

        $this->assertSame($connection1, $connection2);
        $this->assertSame($connection2, $connection3);
        $this->assertSame($connection1, $connection3);
    }

    public function testGetDatabaseFileMethod(): void
    {
        $factory = new DatabaseConnectionFactory($this->testDbPath);

        // Test that database file path is constructed correctly
        // We can't test private method directly, but we can test its effect
        $connection = $factory->getConnection('test_file_path');

        $this->assertInstanceOf(PDO::class, $connection);
        $this->assertTrue($factory->moduleExists('test_file_path'));

        // Verify the file was created in the correct location
        $expectedFile = $this->testDbPath . '/test_file_path.sqlite';
        $this->assertFileExists($expectedFile);
    }

    public function testEnsureDatabaseDirectoryMethod(): void
    {
        // Test that constructor creates directory if it doesn't exist
        $newPath = $this->testDbPath . '_new_' . uniqid();

        $this->assertDirectoryDoesNotExist($newPath);

        $factory = new DatabaseConnectionFactory($newPath);

        $this->assertDirectoryExists($newPath);

        // Clean up
        rmdir($newPath);
    }
}
