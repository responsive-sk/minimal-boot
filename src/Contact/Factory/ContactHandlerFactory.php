<?php

declare(strict_types=1);

namespace Light\Contact\Factory;

use Light\Contact\Handler\ContactHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for ContactHandler.
 */
class ContactHandlerFactory
{
    /**
     * Create ContactHandler instance.
     */
    public function __invoke(ContainerInterface $container): ContactHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new ContactHandler($template);
    }
}
