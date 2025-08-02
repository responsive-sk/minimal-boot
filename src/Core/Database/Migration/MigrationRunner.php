<?php

declare(strict_types=1);

namespace Minimal\Core\Database\Migration;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use PDO;
use RuntimeException;

/**
 * Simple Migration Runner for database schema changes.
 * 
 * Manages database migrations per module with tracking.
 */
class MigrationRunner
{
    private DatabaseConnectionFactory $connectionFactory;
    private string $migrationsPath;

    public function __construct(
        DatabaseConnectionFactory $connectionFactory,
        string $migrationsPath = 'migrations'
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->migrationsPath = $migrationsPath;
    }

    /**
     * Run migrations for specific module.
     */
    public function runMigrations(string $module): array
    {
        $pdo = $this->connectionFactory->getConnection($module);
        $migrationFiles = $this->getMigrationFiles($module);
        $executedMigrations = $this->getExecutedMigrations($pdo);
        
        $newMigrations = [];
        
        foreach ($migrationFiles as $migrationFile) {
            $migrationName = basename($migrationFile, '.sql');
            
            if (!in_array($migrationName, $executedMigrations)) {
                $this->executeMigration($pdo, $migrationFile, $migrationName);
                $newMigrations[] = $migrationName;
            }
        }
        
        return $newMigrations;
    }

    /**
     * Run migrations for all modules.
     */
    public function runAllMigrations(): array
    {
        $results = [];
        $modules = $this->getAvailableModules();
        
        foreach ($modules as $module) {
            $results[$module] = $this->runMigrations($module);
        }
        
        return $results;
    }

    /**
     * Get migration files for module.
     */
    private function getMigrationFiles(string $module): array
    {
        $moduleMigrationsPath = $this->migrationsPath . '/' . $module;
        
        if (!is_dir($moduleMigrationsPath)) {
            return [];
        }
        
        $files = glob($moduleMigrationsPath . '/*.sql');
        sort($files); // Execute in alphabetical order
        
        return $files;
    }

    /**
     * Get executed migrations from database.
     */
    private function getExecutedMigrations(PDO $pdo): array
    {
        try {
            $stmt = $pdo->query("SELECT migration FROM migrations ORDER BY executed_at");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            // Migrations table doesn't exist yet
            return [];
        }
    }

    /**
     * Execute single migration file.
     */
    private function executeMigration(PDO $pdo, string $migrationFile, string $migrationName): void
    {
        $sql = file_get_contents($migrationFile);
        
        if ($sql === false) {
            throw new RuntimeException("Failed to read migration file: {$migrationFile}");
        }
        
        try {
            $pdo->beginTransaction();
            
            // Execute migration SQL
            $pdo->exec($sql);
            
            // Record migration as executed
            $stmt = $pdo->prepare("INSERT INTO migrations (migration) VALUES (?)");
            $stmt->execute([$migrationName]);
            
            $pdo->commit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            throw new RuntimeException(
                "Failed to execute migration '{$migrationName}': " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get available modules with migrations.
     */
    private function getAvailableModules(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }
        
        $modules = [];
        $directories = glob($this->migrationsPath . '/*', GLOB_ONLYDIR);
        
        foreach ($directories as $directory) {
            $modules[] = basename($directory);
        }
        
        return $modules;
    }

    /**
     * Create migration file for module.
     */
    public function createMigration(string $module, string $name): string
    {
        $moduleMigrationsPath = $this->migrationsPath . '/' . $module;
        
        if (!is_dir($moduleMigrationsPath)) {
            mkdir($moduleMigrationsPath, 0755, true);
        }
        
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.sql";
        $filepath = $moduleMigrationsPath . '/' . $filename;
        
        $template = $this->getMigrationTemplate($name);
        file_put_contents($filepath, $template);
        
        return $filepath;
    }

    /**
     * Get migration template.
     */
    private function getMigrationTemplate(string $name): string
    {
        return "-- Migration: {$name}\n-- Created: " . date('Y-m-d H:i:s') . "\n\n-- Add your SQL statements here\n\n";
    }

    /**
     * Get migration status for module.
     */
    public function getMigrationStatus(string $module): array
    {
        $pdo = $this->connectionFactory->getConnection($module);
        $migrationFiles = $this->getMigrationFiles($module);
        $executedMigrations = $this->getExecutedMigrations($pdo);
        
        $status = [];
        
        foreach ($migrationFiles as $migrationFile) {
            $migrationName = basename($migrationFile, '.sql');
            $status[] = [
                'name' => $migrationName,
                'file' => $migrationFile,
                'executed' => in_array($migrationName, $executedMigrations),
            ];
        }
        
        return $status;
    }
}
