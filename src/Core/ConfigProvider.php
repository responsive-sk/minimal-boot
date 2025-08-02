<?php

declare(strict_types=1);

namespace Minimal\Core;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Factory\DatabaseConnectionFactoryFactory;
use Minimal\Core\Factory\NativePhpRendererFactory;
use Minimal\Core\Factory\PathsFactory;
use Minimal\Core\Factory\TemplatePathProviderFactory;
use Minimal\Core\Service\TemplatePathProviderInterface;
use Mezzio\Template\TemplateRendererInterface;
use ResponsiveSk\Slim4Paths\Paths;

/**
 * Core module configuration provider.
 *
 * Provides core infrastructure services following Zend4Boot protocol
 * and PSR-15 compliance. This module contains shared services used
 * across the entire application.
 */
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
            'factories' => [
                // Core services
                Paths::class => PathsFactory::class,
                DatabaseConnectionFactory::class => DatabaseConnectionFactoryFactory::class,

                // Template path provider
                TemplatePathProviderInterface::class => TemplatePathProviderFactory::class,

                // Core template renderer (Native PHP) - register for Mezzio interface
                TemplateRendererInterface::class => NativePhpRendererFactory::class,
            ],
        ];
    }
}
