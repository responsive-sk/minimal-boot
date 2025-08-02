<?php

declare(strict_types=1);

namespace Minimal\Core\Factory;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Paths\Paths;

/**
 * Factory for DatabaseConnectionFactory.
 */
class DatabaseConnectionFactoryFactory
{
    public function __invoke(ContainerInterface $container): DatabaseConnectionFactory
    {
        $paths = $container->get(Paths::class);
        $databasePath = $paths->getPath('db', 'var/db');

        return new DatabaseConnectionFactory($databasePath);
    }
}
