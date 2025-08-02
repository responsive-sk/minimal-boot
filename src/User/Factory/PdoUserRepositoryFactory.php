<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\User\Infrastructure\Repository\PdoUserRepository;
use Psr\Container\ContainerInterface;

/**
 * Factory for PdoUserRepository.
 */
class PdoUserRepositoryFactory
{
    public function __invoke(ContainerInterface $container): PdoUserRepository
    {
        $connectionFactory = $container->get(DatabaseConnectionFactory::class);

        return new PdoUserRepository($connectionFactory);
    }
}
