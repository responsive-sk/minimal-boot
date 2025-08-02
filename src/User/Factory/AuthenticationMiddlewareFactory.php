<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Middleware\AuthenticationMiddleware;
use Psr\Container\ContainerInterface;

/**
 * Factory for AuthenticationMiddleware.
 */
class AuthenticationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): AuthenticationMiddleware
    {
        $authService = $container->get(AuthenticationService::class);

        return new AuthenticationMiddleware($authService);
    }
}
