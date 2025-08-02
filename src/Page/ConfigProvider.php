<?php

declare(strict_types=1);

namespace Minimal\Page;

use Minimal\Page\Domain\Repository\PageRepositoryInterface;
use Minimal\Page\Domain\Service\PageServiceInterface;
use Minimal\Page\Factory\BootstrapDemoHandlerFactory;
use Minimal\Page\Factory\DemoHandlerFactory;
use Minimal\Page\Factory\GetPageViewHandlerFactory;
use Minimal\Page\Factory\IndexHandlerFactory;
use Minimal\Page\Factory\PageRepositoryFactory;
use Minimal\Page\Factory\PdoPageRepositoryFactory;
use Minimal\Page\Factory\PageServiceFactory;
use Minimal\Page\Handler\BootstrapDemoHandler;
use Minimal\Page\Handler\DemoHandler;
use Minimal\Page\Handler\GetPageViewHandler;
use Minimal\Page\Handler\IndexHandler;
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
                IndexHandler::class           => IndexHandlerFactory::class,
                DemoHandler::class            => DemoHandlerFactory::class,
                BootstrapDemoHandler::class   => BootstrapDemoHandlerFactory::class,
                GetPageViewHandler::class     => GetPageViewHandlerFactory::class,

                // Domain services
                PageServiceInterface::class => PageServiceFactory::class,
                PageRepositoryInterface::class => PdoPageRepositoryFactory::class,
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
