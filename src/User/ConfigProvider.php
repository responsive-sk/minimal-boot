<?php

declare(strict_types=1);

namespace Minimal\User;

/**
 * Configuration provider for User module.
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'delegators' => [
                \Mezzio\Application::class => [
                    RoutesDelegator::class,
                ],
            ],
            'factories' => [
                // Domain services
                Domain\Repository\UserRepositoryInterface::class => Factory\PdoUserRepositoryFactory::class,
                Domain\Service\UserService::class => Factory\UserServiceFactory::class,
                Domain\Service\AuthenticationService::class => Factory\AuthenticationServiceFactory::class,

                // Handlers
                Handler\LoginHandler::class => Factory\LoginHandlerFactory::class,
                Handler\RegisterHandler::class => Factory\RegisterHandlerFactory::class,
                Handler\LogoutHandler::class => Factory\LogoutHandlerFactory::class,
                Handler\DashboardHandler::class => Factory\DashboardHandlerFactory::class,

                // Middleware
                Middleware\AuthenticationMiddleware::class => Factory\AuthenticationMiddlewareFactory::class,
            ],
        ];
    }


}
