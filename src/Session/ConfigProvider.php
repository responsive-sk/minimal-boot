<?php

declare(strict_types=1);

namespace Minimal\Session;

use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Session\Middleware\SessionMiddleware;
use ResponsiveSk\Slim4Session\SessionFactory;
use ResponsiveSk\Slim4Session\SessionInterface;

use function assert;

/**
 * Session module configuration provider.
 *
 * Provides configuration for Session module functionality.
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
                // Session services
                SessionInterface::class => static function (): SessionInterface {
                    return SessionFactory::createForDevelopment([
                        'name' => 'minimal_boot_session',
                        'cache_expire' => 180,
                        'cookie_httponly' => true,
                        'cookie_secure' => isset($_SERVER['HTTPS']),
                        'cookie_samesite' => 'Lax',
                    ]);
                },
                SessionMiddleware::class => static function ($container): SessionMiddleware {
                    assert($container instanceof ContainerInterface);
                    $session = $container->get(SessionInterface::class);
                    assert($session instanceof SessionInterface);
                    return new SessionMiddleware($session, true);
                },
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
