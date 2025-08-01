<?php

declare(strict_types=1);

namespace Light\Page;

use Light\Page\Domain\Repository\PageRepositoryInterface;
use Light\Page\Domain\Service\PageServiceInterface;
use Light\Page\Factory\DemoHandlerFactory;
use Light\Page\Factory\GetPageViewHandlerFactory;
use Light\Page\Factory\IndexHandlerFactory;
use Light\Page\Factory\PageRepositoryFactory;
use Light\Page\Factory\PageServiceFactory;
use Light\Page\Handler\DemoHandler;
use Light\Page\Handler\GetPageViewHandler;
use Light\Page\Handler\IndexHandler;
use Mezzio\Application;

class ConfigProvider
{
    /**
     * Return configuration for this module.
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
        ];
    }

    /**
     * Return dependency configuration.
     *
     * @return array<string, mixed>
     */
    public function getDependencies(): array
    {
        return [
            'delegators' => [
                Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories'  => [
                // Page handlers
                IndexHandler::class       => IndexHandlerFactory::class,
                DemoHandler::class        => DemoHandlerFactory::class,
                GetPageViewHandler::class => GetPageViewHandlerFactory::class,

                // Domain services
                PageServiceInterface::class => PageServiceFactory::class,
                PageRepositoryInterface::class => PageRepositoryFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration.
     *
     * NOTE: Template paths are now managed centrally via TemplatePathProvider
     * and configured in config/autoload/paths.global.php.
     *
     * @return array<string, mixed>
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                // Template paths are now managed by TemplatePathProvider
                // See config/autoload/paths.global.php for configuration
            ],
        ];
    }
}
