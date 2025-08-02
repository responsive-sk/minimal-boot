<?php

declare(strict_types=1);

namespace Minimal\Page\Factory;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Page\Infrastructure\Repository\PdoPageRepository;
use Psr\Container\ContainerInterface;

class PdoPageRepositoryFactory
{
    public function __invoke(ContainerInterface $container): PdoPageRepository
    {
        $connectionFactory = new DatabaseConnectionFactory('var/db');

        return new PdoPageRepository($connectionFactory);
    }
}
