<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Repository\UserRepositoryInterface;
use Minimal\User\Domain\Service\UserService;
use Psr\Container\ContainerInterface;

/**
 * Factory for UserService.
 */
class UserServiceFactory
{
    public function __invoke(ContainerInterface $container): UserService
    {
        $userRepository = $container->get(UserRepositoryInterface::class);

        return new UserService($userRepository);
    }
}
