<?php

declare(strict_types=1);

namespace Minimal\Auth\Factory;

use Minimal\Auth\Handler\AuthHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for AuthHandler.
 */
class AuthHandlerFactory
{
    /**
     * Create AuthHandler instance.
     */
    public function __invoke(ContainerInterface $container): AuthHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new AuthHandler($template);
    }
}
