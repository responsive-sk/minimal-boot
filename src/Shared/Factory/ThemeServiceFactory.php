<?php

declare(strict_types=1);

namespace Minimal\Shared\Factory;

use Minimal\Shared\Service\ThemeService;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Session\SessionInterface;

use function assert;

/**
 * Factory for ThemeService.
 */
class ThemeServiceFactory
{
    public function __invoke(ContainerInterface $container): ThemeService
    {
        $session = $container->get(SessionInterface::class);
        assert($session instanceof SessionInterface);

        return new ThemeService($session);
    }
}
