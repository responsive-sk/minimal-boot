<?php

/**
 * Development environment configuration
 *
 * This file overrides production settings for local development.
 * Uses SQLite instead of MySQL and relaxed security settings.
 */

declare(strict_types=1);

return [
    // Enable debug mode
    'debug' => true,

    // Disable config caching for development
    \Laminas\ConfigAggregator\ConfigAggregator::ENABLE_CACHE => false,

    // Database configuration - SQLite for development
    'db' => [
        'driver' => 'pdo_sqlite',
        'database' => 'var/db/main.sqlite', // Main database for development
        'driver_options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],

    // Session configuration - development friendly
    'session' => [
        'cookie_name' => 'minimal_boot_dev_session',
        'cookie_lifetime' => 7200, // 2 hours
        'cookie_secure' => false, // HTTP allowed for development
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax', // Relaxed for development
        'gc_maxlifetime' => 7200,
        'gc_probability' => 1,
        'gc_divisor' => 100,
    ],

    // Logging - verbose for development
    'log' => [
        'level' => 'debug',
        'path' => 'var/logs',
    ],

    // Cache - disabled for development
    'cache' => [
        'enabled' => false,
    ],

    // Security headers - relaxed for development
    'security' => [
        'headers' => [
            'X-Frame-Options' => 'SAMEORIGIN', // Relaxed for development tools
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
        ],
        'hsts' => [
            'max_age' => 0, // Disabled for HTTP development
            'include_subdomains' => false,
            'preload' => false,
        ],
    ],

    // File uploads - more permissive for development
    'upload' => [
        'max_size' => '50M',
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt', 'zip'],
        'path' => 'var/uploads',
    ],

    // Rate limiting - disabled for development
    'rate_limit' => [
        'enabled' => false,
        'max_requests' => 1000,
        'window' => 60,
    ],

    // Feature flags - all enabled for development
    'features' => [
        'contact_form' => true,
        'user_registration' => true,
        'theme_switching' => true,
        'analytics' => false, // Disabled for development
        'debug_toolbar' => true,
    ],

    // Development specific
    'development' => [
        'error_reporting' => E_ALL,
        'display_errors' => true,
        'log_errors' => true,
        'xdebug_enabled' => extension_loaded('xdebug'),
    ],
];
