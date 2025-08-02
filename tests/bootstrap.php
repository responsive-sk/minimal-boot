<?php

/**
 * PHPUnit bootstrap file for test initialization.
 */

declare(strict_types=1);

/**
 * Test Bootstrap for Minimal Boot Framework
 *
 * Sets up testing environment with proper autoloading and configuration.
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Set timezone for consistent test results
date_default_timezone_set('UTC');

// Ensure var directories exist for test outputs
$varDirs = [
    __DIR__ . '/../var/coverage',
    __DIR__ . '/../var/logs',
    __DIR__ . '/../var/cache',
    __DIR__ . '/../var/db',
];

foreach ($varDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Set testing environment variables
$_ENV['APP_ENV'] = 'testing';
$_ENV['DATABASE_PATH'] = ':memory:';

// Clean up any existing test databases
$testDbFiles = glob(__DIR__ . '/../var/db/test_*.sqlite');
foreach ($testDbFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
    }
}

echo "Test environment initialized.\n";
