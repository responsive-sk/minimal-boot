<?php

declare(strict_types=1);

namespace Minimal\Shared\Factory;

use Minimal\Shared\Middleware\ThemeMiddleware;
use Minimal\Shared\Service\ThemeService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for ThemeMiddleware.
 */
class ThemeMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ThemeMiddleware
    {
        $themeService = $container->get(ThemeService::class);
        $template = $container->get(TemplateRendererInterface::class);

        // Ensure proper types
        assert($themeService instanceof ThemeService);
        assert($template instanceof TemplateRendererInterface);

        return new ThemeMiddleware($themeService, $template);
    }
}
