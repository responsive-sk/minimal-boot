<?php

declare(strict_types=1);

/**
 * Production Configuration Override
 *
 * This file contains production-specific configuration that overrides
 * development settings for optimal performance and security.
 */

return [
    // Disable debugging in production
    'debug' => false,

    // Disable configuration cache for debugging
    'config_cache_enabled' => false,

    // Mezzio configuration
    'mezzio' => [
        // Production error handling
        'error_handler' => [
            'template_404'   => 'error::404',
            'template_error' => 'error::error',
            'layout'         => 'error::layout',
        ],

        // Disable development middleware
        'programmatic_pipeline' => false,
    ],

    // Database configuration for production
    'db' => [
        'driver' => 'pdo_mysql',
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? '3306',
        'dbname' => $_ENV['DB_NAME'] ?? '',
        'user' => $_ENV['DB_USER'] ?? '',
        'password' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        ],
    ],

    // Logging configuration
    'log' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'error',
        'path' => $_ENV['LOG_PATH'] ?? 'var/log',
        'filename' => 'application.log',
        'max_files' => 30,
        'format' => '[%datetime%] %level_name%: %message% %context% %extra%' . PHP_EOL,
    ],

    // Session configuration for production
    'session' => [
        'cookie_name' => $_ENV['SESSION_NAME'] ?? 'minimal_boot_session',
        'cookie_lifetime' => (int) (is_numeric($_ENV['SESSION_LIFETIME'] ?? null) ? $_ENV['SESSION_LIFETIME'] : 3600),
        'cookie_secure' => filter_var($_ENV['SESSION_COOKIE_SECURE'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'cookie_httponly' => filter_var($_ENV['SESSION_COOKIE_HTTPONLY'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'cookie_samesite' => $_ENV['SESSION_COOKIE_SAMESITE'] ?? 'Strict',
        'gc_maxlifetime' => (int) (is_numeric($_ENV['SESSION_LIFETIME'] ?? null) ? $_ENV['SESSION_LIFETIME'] : 3600),
        'gc_probability' => 1,
        'gc_divisor' => 100,
    ],

    // CSRF protection
    'csrf' => [
        'token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? 'csrf_token',
        'header_name' => $_ENV['CSRF_HEADER_NAME'] ?? 'X-CSRF-Token',
        'timeout' => 3600,
    ],

    // Security headers
    'security' => [
        'headers' => [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ],

        // HSTS header (only if HTTPS is properly configured)
        'hsts' => [
            'max_age' => (int) (is_numeric($_ENV['HSTS_MAX_AGE'] ?? null) ? $_ENV['HSTS_MAX_AGE'] : 31536000),
            'include_subdomains' => true,
            'preload' => false,
        ],

        // Content Security Policy
        'csp' => [
            'enabled' => filter_var($_ENV['SECURITY_HEADERS_ENABLED'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            'policy' => $_ENV['CONTENT_SECURITY_POLICY'] ?? "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'",
        ],
    ],

    // Performance optimizations
    'performance' => [
        'opcache' => [
            'enabled' => filter_var($_ENV['OPCACHE_ENABLE'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            'memory_consumption' => 128,
            'max_accelerated_files' => 4000,
            'revalidate_freq' => 60,
            'fast_shutdown' => true,
            'enable_cli' => false,
        ],

        'cache' => [
            'enabled' => filter_var($_ENV['CACHE_ENABLE'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
            'ttl' => 3600,
            'adapter' => 'filesystem',
            'options' => [
                'cache_dir' => $_ENV['CACHE_PATH'] ?? 'var/cache',
                'dir_level' => 2,
            ],
        ],
    ],

    // File upload settings
    'upload' => [
        'max_size' => $_ENV['UPLOAD_MAX_SIZE'] ?? '10M',
        'allowed_types' => explode(',', is_string($_ENV['UPLOAD_ALLOWED_TYPES'] ?? null) ? $_ENV['UPLOAD_ALLOWED_TYPES'] : 'jpg,jpeg,png,gif,pdf'),
        'upload_path' => $_ENV['UPLOAD_PATH'] ?? 'var/uploads',
    ],

    // Email configuration (if using email features)
    'mail' => [
        'transport' => [
            'type' => 'smtp',
            'options' => [
                'host' => $_ENV['MAIL_HOST'] ?? '',
                'port' => (int) (is_numeric($_ENV['MAIL_PORT'] ?? null) ? $_ENV['MAIL_PORT'] : 587),
                'connection_class' => 'login',
                'connection_config' => [
                    'username' => $_ENV['MAIL_USERNAME'] ?? '',
                    'password' => $_ENV['MAIL_PASSWORD'] ?? '',
                    'ssl' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
                ],
            ],
        ],
        'message' => [
            'from' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@localhost',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Minimal Boot',
        ],
    ],

    // Rate limiting
    'rate_limit' => [
        'enabled' => filter_var($_ENV['RATE_LIMIT_ENABLED'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'max_requests' => (int) (is_numeric($_ENV['RATE_LIMIT_MAX_REQUESTS'] ?? null) ? $_ENV['RATE_LIMIT_MAX_REQUESTS'] : 60),
        'window' => (int) (is_numeric($_ENV['RATE_LIMIT_WINDOW'] ?? null) ? $_ENV['RATE_LIMIT_WINDOW'] : 60),
        'storage' => 'filesystem',
    ],

    // Maintenance mode
    'maintenance' => [
        'enabled' => filter_var($_ENV['MAINTENANCE_MODE'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
        'message' => $_ENV['MAINTENANCE_MESSAGE'] ?? 'We are currently performing scheduled maintenance.',
        'allowed_ips' => [], // Add IPs that should bypass maintenance mode
    ],

    // Feature flags
    'features' => [
        'contact_form' => filter_var($_ENV['FEATURE_CONTACT_FORM'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'user_registration' => filter_var($_ENV['FEATURE_USER_REGISTRATION'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'theme_switching' => filter_var($_ENV['FEATURE_THEME_SWITCHING'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
        'analytics' => filter_var($_ENV['FEATURE_ANALYTICS'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
    ],

    // Asset configuration
    'assets' => [
        'base_url' => $_ENV['ASSET_URL'] ?? $_ENV['APP_URL'] ?? '',
        'version' => '1.0.0', // Update this when assets change
        'cdn' => [
            'enabled' => filter_var($_ENV['CDN_ENABLED'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
            'url' => $_ENV['CDN_URL'] ?? '',
        ],
    ],

    // Third-party integrations
    'integrations' => [
        'google_analytics' => [
            'enabled' => !empty($_ENV['GOOGLE_ANALYTICS_ID'] ?? ''),
            'tracking_id' => $_ENV['GOOGLE_ANALYTICS_ID'] ?? '',
        ],

        'recaptcha' => [
            'enabled' => !empty($_ENV['RECAPTCHA_SITE_KEY'] ?? ''),
            'site_key' => $_ENV['RECAPTCHA_SITE_KEY'] ?? '',
            'secret_key' => $_ENV['RECAPTCHA_SECRET_KEY'] ?? '',
        ],

        'sentry' => [
            'enabled' => !empty($_ENV['SENTRY_DSN'] ?? ''),
            'dsn' => $_ENV['SENTRY_DSN'] ?? '',
        ],
    ],

    // Production logging configuration - disable file logging for shared hosting
    'dot_log' => [
        'loggers' => [
            'default_logger' => [
                'writers' => [],
            ],
        ],
    ],
];
