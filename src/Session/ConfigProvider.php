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
                SessionInterface::class => static function (ContainerInterface $container): SessionInterface {
                    $config = $container->get('config');
                    $sessionConfig = $config['session'] ?? [];

                    // Set session save path before creating session
                    $sessionPath = realpath('.') . '/var/sessions';
                    if (!is_dir($sessionPath)) {
                        mkdir($sessionPath, 0755, true);
                    }

                    // Configure session settings for built-in server compatibility
                    ini_set('session.save_path', $sessionPath);
                    ini_set('session.save_handler', 'files');
                    ini_set('session.use_cookies', '1');
                    ini_set('session.use_only_cookies', '1');
                    ini_set('session.cookie_path', '/');
                    ini_set('session.cookie_domain', '');

                    return SessionFactory::createForDevelopment([
                        'name' => $sessionConfig['cookie_name'] ?? 'minimal_boot_session',
                        'cache_expire' => $sessionConfig['cookie_lifetime'] ?? 3600,
                        'cookie_httponly' => $sessionConfig['cookie_httponly'] ?? true,
                        'cookie_secure' => false, // Always false for development
                        'cookie_samesite' => 'Lax',
                        'gc_maxlifetime' => $sessionConfig['gc_maxlifetime'] ?? 3600,
                        'gc_probability' => $sessionConfig['gc_probability'] ?? 1,
                        'gc_divisor' => $sessionConfig['gc_divisor'] ?? 100,
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
