<?php

declare(strict_types=1);

namespace Minimal\Contact;

use Minimal\Contact\Factory\ContactHandlerFactory;
use Minimal\Contact\Handler\ContactHandler;
use Minimal\Contact\Handler\TestHandler;
use Mezzio\Application;

/**
 * Contact module configuration provider.
 *
 * Provides configuration for contact form functionality.
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
            'factories' => [
                // Contact handlers and services
                ContactHandler::class => ContactHandlerFactory::class,
                TestHandler::class => ContactHandlerFactory::class,
            ],
        ];
    }

    /**
     * Returns the templates configuration.
     *
     * @return array<string, mixed>
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                // Template paths are managed centrally via TemplatePathProvider
                // See config/autoload/templates.global.php for configuration
            ],
        ];
    }
}
