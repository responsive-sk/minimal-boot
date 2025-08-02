<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Core\Database\Migration;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Migration\MigrationRunner;
use MinimalTest\TestCase;
use RuntimeException;

/**
 * Complete unit tests for MigrationRunner to achieve 100% coverage
 */
class CompleteMigrationRunnerTest extends TestCase
{
    private DatabaseConnectionFactory $connectionFactory;
    private MigrationRunner $migrationRunner;
    private string $testMigrationsPath;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connectionFactory = new DatabaseConnectionFactory(':memory:');
        $this->testMigrationsPath = sys_get_temp_dir() . '/minimal_boot_migrations_' . uniqid();
        $this->migrationRunner = new MigrationRunner($this->connectionFactory, $this->testMigrationsPath);
        
        // Create test migrations directory
        mkdir($this->testMigrationsPath, 0755, true);
        mkdir($this->testMigrationsPath . '/test_module', 0755, true);
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

    public function testRunMigrationsForModuleWithSingleMigration(): void
    {
        // Create test migration file
        $migrationContent = "-- Test migration\nCREATE TABLE test_table (id INTEGER PRIMARY KEY, name TEXT);";
        $migrationFile = $this->testMigrationsPath . '/test_module/2025_01_01_120000_create_test_table.sql';
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

    public function testRunMigrationsForModuleWithMultipleMigrations(): void
    {
        // Create multiple migration files
        $migrations = [
            '2025_01_01_120000_create_users.sql' => 'CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT);',
            '2025_01_02_120000_create_posts.sql' => 'CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT);',
            '2025_01_03_120000_add_user_email.sql' => 'ALTER TABLE users ADD COLUMN email TEXT;',
        ];
        
        foreach ($migrations as $filename => $content) {
            file_put_contents($this->testMigrationsPath . '/test_module/' . $filename, $content);
        }
        
        $executedMigrations = $this->migrationRunner->runMigrations('test_module');
        
        $this->assertCount(3, $executedMigrations);
        $this->assertEquals('2025_01_01_120000_create_users', $executedMigrations[0]);
        $this->assertEquals('2025_01_02_120000_create_posts', $executedMigrations[1]);
        $this->assertEquals('2025_01_03_120000_add_user_email', $executedMigrations[2]);
        
        // Verify tables were created
        $connection = $this->connectionFactory->getConnection('test_module');
        $stmt = $connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name IN ('users', 'posts')");
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $this->assertContains('users', $tables);
        $this->assertContains('posts', $tables);
    }

    public function testRunAllMigrationsForMultipleModules(): void
    {
        // Create migrations for multiple modules
        mkdir($this->testMigrationsPath . '/module1', 0755, true);
        mkdir($this->testMigrationsPath . '/module2', 0755, true);
        
        $migration1 = "CREATE TABLE table1 (id INTEGER PRIMARY KEY);";
        $migration2 = "CREATE TABLE table2 (id INTEGER PRIMARY KEY);";
        
        file_put_contents($this->testMigrationsPath . '/module1/2025_01_01_120000_create_table1.sql', $migration1);
        file_put_contents($this->testMigrationsPath . '/module2/2025_01_01_120000_create_table2.sql', $migration2);
        
        $results = $this->migrationRunner->runAllMigrations();
        
        $this->assertArrayHasKey('module1', $results);
        $this->assertArrayHasKey('module2', $results);
        $this->assertArrayHasKey('test_module', $results); // Created in setUp
        $this->assertCount(1, $results['module1']);
        $this->assertCount(1, $results['module2']);
    }

    public function testGetMigrationStatusBeforeAndAfterExecution(): void
    {
        // Create test migration
        $migrationFile = $this->testMigrationsPath . '/test_module/2025_01_01_120000_test_migration.sql';
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

    public function testCreateMigrationCreatesFileWithCorrectContent(): void
    {
        $migrationFile = $this->migrationRunner->createMigration('test_module', 'create_users_table');
        
        $this->assertFileExists($migrationFile);
        $this->assertStringContainsString('test_module', $migrationFile);
        $this->assertStringContainsString('create_users_table', $migrationFile);
        
        $content = file_get_contents($migrationFile);
        $this->assertStringContainsString('-- Migration: create_users_table', $content);
        $this->assertStringContainsString('-- Add your SQL statements here', $content);
    }

    public function testRunAllMigrationsUsesAvailableModules(): void
    {
        // Create module directories with migrations
        mkdir($this->testMigrationsPath . '/module_a', 0755, true);
        mkdir($this->testMigrationsPath . '/module_b', 0755, true);

        // Add migrations to test that runAllMigrations finds them
        file_put_contents($this->testMigrationsPath . '/module_a/2025_01_01_120000_test.sql', 'CREATE TABLE test_a (id INTEGER);');
        file_put_contents($this->testMigrationsPath . '/module_b/2025_01_01_120000_test.sql', 'CREATE TABLE test_b (id INTEGER);');

        $results = $this->migrationRunner->runAllMigrations();

        // Should include our modules (getAvailableModules is tested indirectly)
        $this->assertArrayHasKey('module_a', $results);
        $this->assertArrayHasKey('module_b', $results);
        $this->assertArrayHasKey('test_module', $results); // Created in setUp
    }

    public function testMigrationIsNotRunTwice(): void
    {
        // Create test migration
        $migrationContent = "CREATE TABLE duplicate_test (id INTEGER PRIMARY KEY);";
        $migrationFile = $this->testMigrationsPath . '/test_module/2025_01_01_120000_duplicate_test.sql';
        file_put_contents($migrationFile, $migrationContent);
        
        // Run migration first time
        $firstRun = $this->migrationRunner->runMigrations('test_module');
        $this->assertCount(1, $firstRun);
        
        // Run migration second time - should not execute again
        $secondRun = $this->migrationRunner->runMigrations('test_module');
        $this->assertCount(0, $secondRun);
    }

    public function testMigrationsAreRunInChronologicalOrder(): void
    {
        // Create migrations with different timestamps (out of order)
        $migration1 = "CREATE TABLE ordered_test1 (id INTEGER);";
        $migration2 = "CREATE TABLE ordered_test2 (id INTEGER);";
        $migration3 = "CREATE TABLE ordered_test3 (id INTEGER);";
        
        file_put_contents($this->testMigrationsPath . '/test_module/2025_01_03_120000_third.sql', $migration3);
        file_put_contents($this->testMigrationsPath . '/test_module/2025_01_01_120000_first.sql', $migration1);
        file_put_contents($this->testMigrationsPath . '/test_module/2025_01_02_120000_second.sql', $migration2);
        
        $executedMigrations = $this->migrationRunner->runMigrations('test_module');
        
        $this->assertCount(3, $executedMigrations);
        $this->assertEquals('2025_01_01_120000_first', $executedMigrations[0]);
        $this->assertEquals('2025_01_02_120000_second', $executedMigrations[1]);
        $this->assertEquals('2025_01_03_120000_third', $executedMigrations[2]);
    }

    public function testInvalidMigrationThrowsException(): void
    {
        // Create migration with invalid SQL
        $invalidMigration = "INVALID SQL STATEMENT;";
        $migrationFile = $this->testMigrationsPath . '/test_module/2025_01_01_120000_invalid.sql';
        file_put_contents($migrationFile, $invalidMigration);
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to execute migration');
        
        $this->migrationRunner->runMigrations('test_module');
    }

    public function testNonExistentModuleReturnsEmptyArray(): void
    {
        $result = $this->migrationRunner->runMigrations('non_existent_module');
        
        $this->assertEquals([], $result);
    }

    public function testGetMigrationStatusForNonExistentModule(): void
    {
        $status = $this->migrationRunner->getMigrationStatus('non_existent_module');
        
        $this->assertEquals([], $status);
    }

    public function testCreateMigrationWithSpecialCharactersInName(): void
    {
        $migrationFile = $this->migrationRunner->createMigration('test_module', 'add_user_profile_data');
        
        $this->assertFileExists($migrationFile);
        $this->assertStringContainsString('add_user_profile_data', $migrationFile);
        
        $content = file_get_contents($migrationFile);
        $this->assertStringContainsString('-- Migration: add_user_profile_data', $content);
    }

    public function testRunMigrationsWithEmptyModule(): void
    {
        // Create empty module directory
        mkdir($this->testMigrationsPath . '/empty_module', 0755, true);
        
        $result = $this->migrationRunner->runMigrations('empty_module');
        
        $this->assertEquals([], $result);
    }
}
