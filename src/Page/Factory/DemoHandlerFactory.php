<?php

declare(strict_types=1);

namespace Minimal\Page\Factory;

use Minimal\Page\Handler\DemoHandler;
use Minimal\Shared\Service\ThemeService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class DemoHandlerFactory
{
    public function __invoke(ContainerInterface $container): DemoHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $themeService = $container->get(ThemeService::class);
        assert($themeService instanceof ThemeService);

        return new DemoHandler($template, $themeService);
    }
}
