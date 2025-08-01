<?php

declare(strict_types=1);

namespace Minimal\Session;

use Mezzio\Application;
use Psr\Container\ContainerInterface;

/**
 * Session routes delegator.
 *
 * Registers routes for the Session module.
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

        // Add Session routes here
        // Example:
        // $app->get('/session', SessionHandler::class, 'session');

        return $app;
    }
}
