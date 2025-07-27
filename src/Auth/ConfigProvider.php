<?php

declare(strict_types=1);

namespace Light\Auth;

/**
 * Auth module configuration provider.
 *
 * Provides configuration for Auth module functionality.
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
                // Route delegators will be added here
            ],
            'factories' => [
                // Handlers and services will be added here
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
