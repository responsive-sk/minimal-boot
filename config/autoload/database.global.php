<?php

/**
 * Database configuration for Minimal Boot.
 *
 * Uses modular SQLite databases for better separation of concerns.
 */

declare(strict_types=1);

return [
    'database' => [
        // Database path for SQLite files
        'path' => 'var/db',

        // Module database mapping
        'modules' => [
            'page' => 'page.sqlite',
            'contact' => 'contact.sqlite',
            'auth' => 'auth.sqlite',
            'session' => 'session.sqlite',
        ],

        // Migration settings
        'migrations' => [
            'path' => 'var/migrations',
            'auto_run' => false, // Set to true for automatic migrations in development
        ],

        // Connection settings
        'connection' => [
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
            'pragmas' => [
                'foreign_keys' => 'ON',
                'journal_mode' => 'WAL',
                'synchronous' => 'NORMAL',
                'cache_size' => '10000',
                'temp_store' => 'MEMORY',
            ],
        ],
    ],
];
