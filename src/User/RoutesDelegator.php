<?php

declare(strict_types=1);

namespace Minimal\User;

use Minimal\User\Handler\DashboardHandler;
use Minimal\User\Handler\LoginHandler;
use Minimal\User\Handler\LogoutHandler;
use Minimal\User\Handler\RegisterHandler;
use Minimal\User\Middleware\AuthenticationMiddleware;
use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

/**
 * Routes delegator for User module.
 */
class RoutesDelegator
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $serviceName, callable $callback): Application
    {
        $app = $callback();
        assert($app instanceof Application);

        // Public routes (no authentication required)
        $app->route('/login', LoginHandler::class, ['GET', 'POST'], 'user.login');
        $app->route('/register', RegisterHandler::class, ['GET', 'POST'], 'user.register');

        // Protected routes (authentication required)
        $app->get('/logout', [AuthenticationMiddleware::class, LogoutHandler::class], 'user.logout');
        $app->get('/dashboard', [AuthenticationMiddleware::class, DashboardHandler::class], 'user.dashboard');

        return $app;
    }
}
