<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\Shared\Service\ThemeService;
use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Handler\LoginHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for LoginHandler.
 */
class LoginHandlerFactory
{
    public function __invoke(ContainerInterface $container): LoginHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        $authService = $container->get(AuthenticationService::class);
        $themeService = $container->get(ThemeService::class);

        assert($template instanceof TemplateRendererInterface);
        assert($authService instanceof AuthenticationService);
        assert($themeService instanceof ThemeService);

        return new LoginHandler($template, $authService, $themeService);
    }
}
