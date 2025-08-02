<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Handler\LoginHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

/**
 * Factory for LoginHandler.
 */
class LoginHandlerFactory
{
    public function __invoke(ContainerInterface $container): LoginHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        $authService = $container->get(AuthenticationService::class);

        return new LoginHandler($template, $authService);
    }
}
