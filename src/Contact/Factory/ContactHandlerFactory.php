<?php

declare(strict_types=1);

namespace Minimal\Contact\Factory;

use Minimal\Contact\Handler\ContactHandler;
use Minimal\Shared\Service\ThemeService;
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

        $themeService = $container->get(ThemeService::class);
        assert($themeService instanceof ThemeService);

        return new ContactHandler($template, $themeService);
    }
}
