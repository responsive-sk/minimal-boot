<?php

declare(strict_types=1);

namespace Minimal\Shared\Factory;

use Minimal\Shared\Middleware\ThemeTemplateMiddleware;
use Minimal\Shared\Service\ThemeService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for ThemeTemplateMiddleware.
 */
class ThemeTemplateMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ThemeTemplateMiddleware
    {
        $themeService = $container->get(ThemeService::class);
        $template = $container->get(TemplateRendererInterface::class);

        assert($themeService instanceof ThemeService);
        assert($template instanceof TemplateRendererInterface);

        return new ThemeTemplateMiddleware($themeService, $template);
    }
}
