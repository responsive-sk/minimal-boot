<?php

declare(strict_types=1);

namespace Minimal\Core\Database\Connection;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Database Connection Factory for modular SQLite databases.
 *
 * Each module/domain has its own SQLite database file for better separation of concerns.
 */
class DatabaseConnectionFactory
{
    /** @var array<string, PDO> */
    private array $connections = [];
    private string $databasePath;

    public function __construct(string $databasePath = 'var/db')
    {
        $this->databasePath = $databasePath;

        // Only ensure directory for file-based databases
        if ($databasePath !== ':memory:') {
            $this->ensureDatabaseDirectory();
        }
    }

    /**
     * Get database connection for specific module/domain.
     */
    public function getConnection(string $module): PDO
    {
        if (!isset($this->connections[$module])) {
            $this->connections[$module] = $this->createConnection($module);
            $this->initializeModuleDatabase($this->connections[$module], $module);
        }

        return $this->connections[$module];
    }

    /**
     * Create new PDO connection for module.
     */
    private function createConnection(string $module): PDO
    {
        $databaseFile = $this->getDatabaseFile($module);
        $dsn = "sqlite:{$databaseFile}";

        try {
            $pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            // Enable foreign key constraints
            $pdo->exec('PRAGMA foreign_keys = ON');

            return $pdo;
        } catch (PDOException $e) {
            throw new RuntimeException(
                "Failed to connect to database for module '{$module}': " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Get database file path for module.
     */
    private function getDatabaseFile(string $module): string
    {
        // For in-memory databases, return :memory: directly
        if ($this->databasePath === ':memory:') {
            return ':memory:';
        }

        return $this->databasePath . '/' . strtolower($module) . '.sqlite';
    }

    /**
     * Ensure database directory exists.
     */
    private function ensureDatabaseDirectory(): void
    {
        if (!is_dir($this->databasePath)) {
            if (!mkdir($this->databasePath, 0755, true)) {
                throw new RuntimeException("Failed to create database directory: {$this->databasePath}");
            }
        }
    }

    /**
     * Get all available module databases.
     *
     * @return array<string>
     */
    public function getAvailableModules(): array
    {
        $modules = [];
        $files = glob($this->databasePath . '/*.sqlite');

        if ($files === false) {
            return [];
        }

        foreach ($files as $file) {
            $modules[] = basename($file, '.sqlite');
        }

        return $modules;
    }

    /**
     * Check if module database exists.
     */
    public function moduleExists(string $module): bool
    {
        // In-memory databases don't create files
        if ($this->databasePath === ':memory:') {
            return false;
        }

        return file_exists($this->getDatabaseFile($module));
    }

    /**
     * Create database file for module if it doesn't exist.
     */
    public function createModuleDatabase(string $module): void
    {
        // Skip file creation for in-memory databases
        if ($this->databasePath === ':memory:') {
            return;
        }

        $databaseFile = $this->getDatabaseFile($module);

        if (!file_exists($databaseFile)) {
            // Create empty database file
            touch($databaseFile);

            // Initialize with basic structure
            $pdo = $this->getConnection($module);
            $this->initializeModuleDatabase($pdo, $module);
        }
    }

    /**
     * Initialize module database with basic structure.
     */
    private function initializeModuleDatabase(PDO $pdo, string $module): void
    {
        // Create migrations table for tracking schema changes
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /**
     * Close all connections.
     */
    public function closeConnections(): void
    {
        $this->connections = [];
    }
}
