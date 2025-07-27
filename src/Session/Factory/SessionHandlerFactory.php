<?php

declare(strict_types=1);

namespace Light\Session\Factory;

use Light\Session\Handler\SessionHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for SessionHandler.
 */
class SessionHandlerFactory
{
    /**
     * Create SessionHandler instance.
     */
    public function __invoke(ContainerInterface $container): SessionHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new SessionHandler($template);
    }
}
