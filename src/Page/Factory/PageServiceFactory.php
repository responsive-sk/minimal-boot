<?php

declare(strict_types=1);

namespace Light\Page\Factory;

use Light\Page\Domain\Repository\PageRepositoryInterface;
use Light\Page\Domain\Service\PageService;
use Light\Page\Domain\Service\PageServiceInterface;
use Psr\Container\ContainerInterface;

/**
 * Factory for PageService.
 *
 * Creates domain service with proper dependencies.
 */
class PageServiceFactory
{
    public function __invoke(ContainerInterface $container): PageServiceInterface
    {
        $pageRepository = $container->get(PageRepositoryInterface::class);

        return new PageService($pageRepository);
    }
}
