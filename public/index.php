<?php

declare(strict_types=1);

use Mezzio\Application;
use Psr\Container\ContainerInterface;

// Delegate static file requests back to the PHP built-in webserver
if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

// Define the application root directory
$appRoot = dirname(__DIR__);
chdir($appRoot);

// Auto-create var/ structure for shared hosting compatibility
// Note: These paths should ideally use the Paths service, but since this runs
// before the container is initialized, we use hardcoded paths as fallback
$varDirs = ['var', 'var/data', 'var/db', 'var/cache', 'var/logs', 'var/tmp', 'var/sessions', 'var/storage', 'var/migrations'];
foreach ($varDirs as $dir) {
    $fullPath = $appRoot . '/' . $dir;
    if (! is_dir($fullPath)) {
        @mkdir($fullPath, 0755, true);
    }
}

require $appRoot . '/vendor/autoload.php';

// Load environment variables from .env file
$envFile = $appRoot . '/.env';
if (file_exists($envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable($appRoot);
    $dotenv->load();
}

/**
 * Self-called anonymous function that creates its own scope and keep the global namespace clean.
 */
(function () use ($appRoot) {
    /** @var ContainerInterface $container */
    $container = require $appRoot . '/config/container.php';

    /** @var Application $app */
    $app = $container->get(Application::class);

    // Execute programmatic/declarative middleware pipeline and routing configuration statements
    $pipeline = require $appRoot . '/config/pipeline.php';
    assert(is_callable($pipeline));
    $pipeline($app);

    $app->run();
})();
