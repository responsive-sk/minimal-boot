<?php

declare(strict_types=1);

namespace Minimal\Shared;

use Mezzio\Application;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;

/**
 * Routes delegator for Shared module.
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

        // Theme switching routes
        $app->route('/theme/switch', Handler\ThemeSwitchHandler::class, ['GET', 'POST'], 'theme.switch');

        return $app;
    }
}
