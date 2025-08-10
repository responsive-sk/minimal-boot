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
        $config = $container->get('config');
        
        return new SecurityMiddleware($config);
    }
}
