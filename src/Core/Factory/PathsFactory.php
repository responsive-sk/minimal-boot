<?php

declare(strict_types=1);

namespace Minimal\Core\Factory;

use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Paths\Paths;

use function assert;
use function is_array;

/**
 * Factory for Paths service.
 *
 * Creates Paths instance with proper configuration.
 * Follows PSR-11 container interface and Zend4Boot protocol.
 */
class PathsFactory
{
    /**
     * Create Paths instance.
     *
     * @param ContainerInterface $container DI container
     */
    public function __invoke(ContainerInterface $container): Paths
    {
        // Get application configuration
        /** @var array<string, mixed> $config */
        $config = $container->get('config');
        assert(is_array($config));

        // Get paths configuration
        /** @var array<string, mixed> $pathsConfig */
        $pathsConfig = $config['paths'] ?? [];
        assert(is_array($pathsConfig));

        // Extract base path and custom paths
        $basePath = $pathsConfig['base_path'] ?? getcwd();
        assert(is_string($basePath));

        /** @var array<string, string> $customPaths */
        $customPaths = $pathsConfig['custom_paths'] ?? [];
        assert(is_array($customPaths));

        return new Paths($basePath, $customPaths);
    }
}
