<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Handler\LogoutHandler;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for LogoutHandler.
 */
class LogoutHandlerFactory
{
    public function __invoke(ContainerInterface $container): LogoutHandler
    {
        $authService = $container->get(AuthenticationService::class);

        assert($authService instanceof AuthenticationService);

        return new LogoutHandler($authService);
    }
}
