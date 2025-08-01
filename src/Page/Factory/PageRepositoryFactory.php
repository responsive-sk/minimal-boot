<?php

declare(strict_types=1);

namespace Minimal\Page\Factory;

use Minimal\Page\Domain\Repository\InMemoryPageRepository;
use Minimal\Page\Domain\Repository\PageRepositoryInterface;
use Psr\Container\ContainerInterface;

/**
 * Factory for PageRepository.
 *
 * Creates repository implementation for dependency injection.
 */
class PageRepositoryFactory
{
    public function __invoke(ContainerInterface $container): PageRepositoryInterface
    {
        // For now, use in-memory implementation
        // In production, this would create database repository
        return new InMemoryPageRepository();
    }
}
