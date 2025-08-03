<?php

declare(strict_types=1);

namespace Minimal\Core\Factory;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Paths\Paths;

use function assert;

/**
 * Factory for DatabaseConnectionFactory.
 */
class DatabaseConnectionFactoryFactory
{
    public function __invoke(ContainerInterface $container): DatabaseConnectionFactory
    {
        $paths = $container->get(Paths::class);
        assert($paths instanceof Paths);

        $databasePath = $paths->getPath('db', 'var/db');

        // Ensure we have a string path
        // @phpstan-ignore-next-line function.alreadyNarrowedType
        if (!is_string($databasePath)) {
            $databasePath = 'var/db';
        }

        return new DatabaseConnectionFactory($databasePath);
    }
}
