<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Core\Database\Migration;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Migration\MigrationRunner;
use MinimalTest\TestCase;
use RuntimeException;

/**
 * Simple tests for MigrationRunner to achieve coverage
 */
class SimpleMigrationRunnerTest extends TestCase
{
    private DatabaseConnectionFactory $connectionFactory;
    private MigrationRunner $migrationRunner;
    private string $testMigrationsPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connectionFactory = new DatabaseConnectionFactory(':memory:');
        $this->testMigrationsPath = sys_get_temp_dir() . '/minimal_migrations_' . uniqid();
        $this->migrationRunner = new MigrationRunner($this->connectionFactory, $this->testMigrationsPath);
        
        // Create test migrations directory
        mkdir($this->testMigrationsPath, 0755, true);
    }

    protected function tearDown(): void
    {
        // Clean up test migrations directory
        if (is_dir($this->testMigrationsPath)) {
            $this->removeDirectory($this->testMigrationsPath);
        }
        
        parent::tearDown();
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    public function testCanCreateMigrationRunner(): void
    {
        $runner = new MigrationRunner($this->connectionFactory, $this->testMigrationsPath);
        
        $this->assertInstanceOf(MigrationRunner::class, $runner);
    }

    public function testRunMigrationsForNonExistentModule(): void
    {
        $result = $this->migrationRunner->runMigrations('non_existent_module');
        
        $this->assertEquals([], $result);
    }

    public function testGetMigrationStatusForNonExistentModule(): void
    {
        $status = $this->migrationRunner->getMigrationStatus('non_existent_module');
        
        $this->assertEquals([], $status);
    }

    public function testCreateMigrationCreatesFile(): void
    {
        $migrationFile = $this->migrationRunner->createMigration('test_module', 'create_users_table');
        
        $this->assertFileExists($migrationFile);
        $this->assertStringContainsString('test_module', $migrationFile);
        $this->assertStringContainsString('create_users_table', $migrationFile);
        
        $content = file_get_contents($migrationFile);
        $this->assertStringContainsString('-- Migration: create_users_table', $content);
        $this->assertStringContainsString('-- Add your SQL statements here', $content);
    }

    public function testRunMigrationsWithSingleMigration(): void
    {
        // Create module directory
        $moduleDir = $this->testMigrationsPath . '/test_module';
        mkdir($moduleDir, 0755, true);
        
        // Create migration file
        $migrationContent = "CREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT);";
        $migrationFile = $moduleDir . '/2025_01_01_120000_create_test_table.sql';
        file_put_contents($migrationFile, $migrationContent);
        
        $executedMigrations = $this->migrationRunner->runMigrations('test_module');
        
        $this->assertCount(1, $executedMigrations);
        $this->assertEquals('2025_01_01_120000_create_test_table', $executedMigrations[0]);
        
        // Verify table was created
        $connection = $this->connectionFactory->getConnection('test_module');
        $stmt = $connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name='test_table'");
        $result = $stmt->fetchColumn();
        
        $this->assertEquals('test_table', $result);
    }

    public function testRunMigrationsWithMultipleMigrations(): void
    {
        // Create module directory
        $moduleDir = $this->testMigrationsPath . '/test_module';
        mkdir($moduleDir, 0755, true);
        
        // Create multiple migration files
        $migrations = [
            '2025_01_01_120000_create_users.sql' => 'CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT);',
            '2025_01_02_120000_create_posts.sql' => 'CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT);',
        ];
        
        foreach ($migrations as $filename => $content) {
            file_put_contents($moduleDir . '/' . $filename, $content);
        }
        
        $executedMigrations = $this->migrationRunner->runMigrations('test_module');
        
        $this->assertCount(2, $executedMigrations);
        $this->assertEquals('2025_01_01_120000_create_users', $executedMigrations[0]);
        $this->assertEquals('2025_01_02_120000_create_posts', $executedMigrations[1]);
    }

    public function testMigrationIsNotRunTwice(): void
    {
        // Create module directory
        $moduleDir = $this->testMigrationsPath . '/test_module';
        mkdir($moduleDir, 0755, true);
        
        // Create migration file
        $migrationContent = "CREATE TABLE duplicate_test (id INTEGER PRIMARY KEY);";
        $migrationFile = $moduleDir . '/2025_01_01_120000_duplicate_test.sql';
        file_put_contents($migrationFile, $migrationContent);
        
        // Run migration first time
        $firstRun = $this->migrationRunner->runMigrations('test_module');
        $this->assertCount(1, $firstRun);
        
        // Run migration second time - should not execute again
        $secondRun = $this->migrationRunner->runMigrations('test_module');
        $this->assertCount(0, $secondRun);
    }

    public function testGetMigrationStatus(): void
    {
        // Create module directory
        $moduleDir = $this->testMigrationsPath . '/test_module';
        mkdir($moduleDir, 0755, true);
        
        // Create migration file
        $migrationFile = $moduleDir . '/2025_01_01_120000_test_migration.sql';
        file_put_contents($migrationFile, "CREATE TABLE status_test (id INTEGER);");
        
        // Get status before running
        $statusBefore = $this->migrationRunner->getMigrationStatus('test_module');
        $this->assertCount(1, $statusBefore);
        $this->assertFalse($statusBefore[0]['executed']);
        $this->assertEquals('2025_01_01_120000_test_migration', $statusBefore[0]['name']);
        
        // Run migration
        $this->migrationRunner->runMigrations('test_module');
        
        // Get status after running
        $statusAfter = $this->migrationRunner->getMigrationStatus('test_module');
        $this->assertCount(1, $statusAfter);
        $this->assertTrue($statusAfter[0]['executed']);
    }

    public function testRunAllMigrations(): void
    {
        // Create multiple modules
        $module1Dir = $this->testMigrationsPath . '/module1';
        $module2Dir = $this->testMigrationsPath . '/module2';
        mkdir($module1Dir, 0755, true);
        mkdir($module2Dir, 0755, true);
        
        // Add migrations
        file_put_contents($module1Dir . '/2025_01_01_120000_create_table1.sql', 'CREATE TABLE table1 (id INTEGER);');
        file_put_contents($module2Dir . '/2025_01_01_120000_create_table2.sql', 'CREATE TABLE table2 (id INTEGER);');
        
        $results = $this->migrationRunner->runAllMigrations();
        
        $this->assertArrayHasKey('module1', $results);
        $this->assertArrayHasKey('module2', $results);
        $this->assertCount(1, $results['module1']);
        $this->assertCount(1, $results['module2']);
    }

    public function testInvalidMigrationThrowsException(): void
    {
        // Create module directory
        $moduleDir = $this->testMigrationsPath . '/test_module';
        mkdir($moduleDir, 0755, true);
        
        // Create migration with invalid SQL
        $invalidMigration = "INVALID SQL STATEMENT;";
        $migrationFile = $moduleDir . '/2025_01_01_120000_invalid.sql';
        file_put_contents($migrationFile, $invalidMigration);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to execute migration');
        
        $this->migrationRunner->runMigrations('test_module');
    }
}
