<?php

declare(strict_types=1);

namespace Minimal\Page\Factory;

use Minimal\Page\Handler\IndexHandler;
use Minimal\Shared\Service\ThemeService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Paths\Paths;

use function assert;

class IndexHandlerFactory
{
    public function __invoke(ContainerInterface $container): IndexHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        $paths = $container->get(Paths::class);
        assert($paths instanceof Paths);

        $themeService = $container->get(ThemeService::class);
        assert($themeService instanceof ThemeService);

        return new IndexHandler($template, $paths, $themeService);
    }
}
