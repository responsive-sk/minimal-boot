<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Domain\Service\UserService;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Session\SessionInterface;

/**
 * Factory for AuthenticationService.
 */
class AuthenticationServiceFactory
{
    public function __invoke(ContainerInterface $container): AuthenticationService
    {
        $session = $container->get(SessionInterface::class);
        $userService = $container->get(UserService::class);

        return new AuthenticationService($session, $userService);
    }
}
