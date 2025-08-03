<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Domain\Service\UserService;
use Minimal\User\Handler\RegisterHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for RegisterHandler.
 */
class RegisterHandlerFactory
{
    public function __invoke(ContainerInterface $container): RegisterHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        $userService = $container->get(UserService::class);
        $authService = $container->get(AuthenticationService::class);

        assert($template instanceof TemplateRendererInterface);
        assert($userService instanceof UserService);
        assert($authService instanceof AuthenticationService);

        return new RegisterHandler($template, $userService, $authService);
    }
}
