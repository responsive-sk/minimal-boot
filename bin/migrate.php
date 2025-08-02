#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Database Migration CLI Tool for Minimal Boot
 * 
 * Usage:
 *   php bin/migrate.php [module]           # Run migrations for specific module
 *   php bin/migrate.php --all              # Run migrations for all modules
 *   php bin/migrate.php --status [module]  # Show migration status
 *   php bin/migrate.php --create module name # Create new migration
 */

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Migration\MigrationRunner;

// Bootstrap
require_once __DIR__ . '/../vendor/autoload.php';

// Colors for CLI output
const COLOR_GREEN = "\033[32m";
const COLOR_YELLOW = "\033[33m";
const COLOR_RED = "\033[31m";
const COLOR_BLUE = "\033[34m";
const COLOR_RESET = "\033[0m";

function printSuccess(string $message): void
{
    echo COLOR_GREEN . "[SUCCESS] " . COLOR_RESET . $message . PHP_EOL;
}

function printInfo(string $message): void
{
    echo COLOR_BLUE . "[INFO] " . COLOR_RESET . $message . PHP_EOL;
}

function printWarning(string $message): void
{
    echo COLOR_YELLOW . "[WARNING] " . COLOR_RESET . $message . PHP_EOL;
}

function printError(string $message): void
{
    echo COLOR_RED . "[ERROR] " . COLOR_RESET . $message . PHP_EOL;
}

function showUsage(): void
{
    echo "Database Migration Tool for Minimal Boot" . PHP_EOL;
    echo PHP_EOL;
    echo "Usage:" . PHP_EOL;
    echo "  php bin/migrate.php [module]           # Run migrations for specific module" . PHP_EOL;
    echo "  php bin/migrate.php --all              # Run migrations for all modules" . PHP_EOL;
    echo "  php bin/migrate.php --status [module]  # Show migration status" . PHP_EOL;
    echo "  php bin/migrate.php --create module name # Create new migration" . PHP_EOL;
    echo PHP_EOL;
    echo "Examples:" . PHP_EOL;
    echo "  php bin/migrate.php page               # Run page module migrations" . PHP_EOL;
    echo "  php bin/migrate.php --all              # Run all migrations" . PHP_EOL;
    echo "  php bin/migrate.php --status page      # Show page migration status" . PHP_EOL;
    echo "  php bin/migrate.php --create page add_author # Create new migration" . PHP_EOL;
    echo PHP_EOL;
}

// Parse command line arguments
$args = array_slice($argv, 1);

if (empty($args)) {
    showUsage();
    exit(1);
}

try {
    // Initialize database components
    $connectionFactory = new DatabaseConnectionFactory('var/db');
    $migrationRunner = new MigrationRunner($connectionFactory, 'migrations');

    $command = $args[0];

    switch ($command) {
        case '--all':
            printInfo("Running migrations for all modules...");
            $results = $migrationRunner->runAllMigrations();
            
            foreach ($results as $module => $migrations) {
                if (empty($migrations)) {
                    printInfo("Module '{$module}': No new migrations to run");
                } else {
                    printSuccess("Module '{$module}': " . count($migrations) . " migrations executed");
                    foreach ($migrations as $migration) {
                        echo "  - {$migration}" . PHP_EOL;
                    }
                }
            }
            break;

        case '--status':
            $module = $args[1] ?? null;
            if (!$module) {
                printError("Module name required for status command");
                showUsage();
                exit(1);
            }
            
            printInfo("Migration status for module '{$module}':");
            $status = $migrationRunner->getMigrationStatus($module);
            
            if (empty($status)) {
                printWarning("No migrations found for module '{$module}'");
            } else {
                foreach ($status as $migration) {
                    $statusText = $migration['executed'] ? COLOR_GREEN . "✓" . COLOR_RESET : COLOR_YELLOW . "✗" . COLOR_RESET;
                    echo "  {$statusText} {$migration['name']}" . PHP_EOL;
                }
            }
            break;

        case '--create':
            $module = $args[1] ?? null;
            $name = $args[2] ?? null;
            
            if (!$module || !$name) {
                printError("Module name and migration name required for create command");
                showUsage();
                exit(1);
            }
            
            $migrationFile = $migrationRunner->createMigration($module, $name);
            printSuccess("Created migration: {$migrationFile}");
            break;

        default:
            // Treat as module name
            $module = $command;
            printInfo("Running migrations for module '{$module}'...");
            
            $migrations = $migrationRunner->runMigrations($module);
            
            if (empty($migrations)) {
                printInfo("No new migrations to run for module '{$module}'");
            } else {
                printSuccess(count($migrations) . " migrations executed for module '{$module}':");
                foreach ($migrations as $migration) {
                    echo "  - {$migration}" . PHP_EOL;
                }
            }
            break;
    }

    printSuccess("Migration process completed!");

} catch (Exception $e) {
    printError("Migration failed: " . $e->getMessage());
    
    if (isset($_ENV['DEBUG']) && $_ENV['DEBUG']) {
        echo PHP_EOL . "Stack trace:" . PHP_EOL;
        echo $e->getTraceAsString() . PHP_EOL;
    }
    
    exit(1);
}
