<?php

declare(strict_types=1);

namespace Minimal\Shared\Factory;

use Minimal\Shared\Middleware\SecurityMiddleware;
use Psr\Container\ContainerInterface;

/**
 * Factory for SecurityMiddleware.
 */
class SecurityMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): SecurityMiddleware
    {
        try {
            @file_put_contents('var/logs/debug.log', "SecurityMiddlewareFactory: Creating SecurityMiddleware\n", FILE_APPEND);
            $config = $container->get('config');
            @file_put_contents('var/logs/debug.log', "SecurityMiddlewareFactory: Config loaded successfully\n", FILE_APPEND);

            // Ensure config is array with proper typing
            if (!is_array($config)) {
                $config = [];
            }
            /** @var array<string, mixed> $config */

            $middleware = new SecurityMiddleware($config);
            @file_put_contents('var/logs/debug.log', "SecurityMiddlewareFactory: SecurityMiddleware created successfully\n", FILE_APPEND);

            return $middleware;
        } catch (\Throwable $e) {
            @file_put_contents('var/logs/debug.log', "SecurityMiddlewareFactory: ERROR - " . $e->getMessage() . "\n", FILE_APPEND);
            throw $e;
        }
    }
}
