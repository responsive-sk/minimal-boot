<?php

declare(strict_types=1);

namespace Minimal\Shared\Factory;

use Minimal\Shared\Handler\ThemeSwitchHandler;
use Minimal\Shared\Service\ThemeService;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for ThemeSwitchHandler.
 */
class ThemeSwitchHandlerFactory
{
    public function __invoke(ContainerInterface $container): ThemeSwitchHandler
    {
        $themeService = $container->get(ThemeService::class);
        assert($themeService instanceof ThemeService);

        return new ThemeSwitchHandler($themeService);
    }
}
