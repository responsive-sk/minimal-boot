<?php

declare(strict_types=1);

namespace Light\Page\Factory;

use Light\Page\Domain\Service\PageServiceInterface;
use Light\Page\Handler\GetPageViewHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

class GetPageViewHandlerFactory
{
    /**
     * @param class-string $requestedName
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName): GetPageViewHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $pageService = $container->get(PageServiceInterface::class);
        assert($pageService instanceof PageServiceInterface);

        return new GetPageViewHandler($template, $pageService);
    }
}
