<?php

declare(strict_types=1);

use Dot\ErrorHandler\ErrorHandlerInterface;
use Dot\ErrorHandler\LogErrorHandler;

return [
    // Provides application-wide services.
    // We recommend using fully-qualified class names whenever possible as
    // service names.
    'dependencies' => [
        // Use 'aliases' to alias a service name to another service. The
        // key is the alias name, the value is the service to which it points.
        'aliases' => [
            ErrorHandlerInterface::class => LogErrorHandler::class,
        ],
        // Use 'invokables' for constructor-less services, or services that do
        // not require arguments to the constructor. Map a service name to the
        // class name.
        'invokables' => [
            // Fully\Qualified\InterfaceName::class => Fully\Qualified\ClassName::class,
        ],
        // Use 'factories' for services provided by callbacks/factory classes.
        'factories' => [
            // Override default Twig Environment factory with our Paths-aware version
            // Environment::class => PathsAwareTwigEnvironmentFactory::class,

            // User module
            \Minimal\User\Domain\Repository\UserRepositoryInterface::class => \Minimal\User\Factory\PdoUserRepositoryFactory::class,
            \Minimal\User\Domain\Service\UserService::class => \Minimal\User\Factory\UserServiceFactory::class,
            \Minimal\User\Domain\Service\AuthenticationService::class => \Minimal\User\Factory\AuthenticationServiceFactory::class,

            // User handlers
            \Minimal\User\Handler\LoginHandler::class => \Minimal\User\Factory\LoginHandlerFactory::class,
            \Minimal\User\Handler\RegisterHandler::class => \Minimal\User\Factory\RegisterHandlerFactory::class,
            \Minimal\User\Handler\LogoutHandler::class => \Minimal\User\Factory\LogoutHandlerFactory::class,
            \Minimal\User\Handler\DashboardHandler::class => \Minimal\User\Factory\DashboardHandlerFactory::class,

            // User middleware
            \Minimal\User\Middleware\AuthenticationMiddleware::class => \Minimal\User\Factory\AuthenticationMiddlewareFactory::class,
        ],
    ],
];
