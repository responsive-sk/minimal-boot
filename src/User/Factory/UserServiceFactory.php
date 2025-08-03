<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Repository\UserRepositoryInterface;
use Minimal\User\Domain\Service\UserService;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for UserService.
 */
class UserServiceFactory
{
    public function __invoke(ContainerInterface $container): UserService
    {
        $userRepository = $container->get(UserRepositoryInterface::class);

        assert($userRepository instanceof UserRepositoryInterface);

        return new UserService($userRepository);
    }
}
