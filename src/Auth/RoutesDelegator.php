<?php

declare(strict_types=1);

namespace Minimal\Auth;

use Minimal\Auth\Handler\AuthHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Session\Middleware\SessionMiddleware;

/**
 * Auth routes delegator.
 *
 * Registers routes for the Auth module.
 */
class RoutesDelegator
{
    /**
     * @param ContainerInterface $container
     * @param string $serviceName
     * @param callable $callback
     * @return Application
     */
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): Application {
        /** @var Application $app */
        $app = $callback();

        // Add Auth routes with session middleware
        $app->get('/login', [
            SessionMiddleware::class,
            AuthHandler::class,
        ], 'login');

        $app->post('/login', [
            SessionMiddleware::class,
            AuthHandler::class,
        ], 'login.post');

        $app->get('/register', [
            SessionMiddleware::class,
            AuthHandler::class,
        ], 'register');

        $app->post('/register', [
            SessionMiddleware::class,
            AuthHandler::class,
        ], 'register.post');

        $app->get('/logout', [
            SessionMiddleware::class,
            AuthHandler::class,
        ], 'logout');

        return $app;
    }
}
