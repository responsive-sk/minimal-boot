<?php

declare(strict_types=1);

namespace Light\Page\Factory;

use Light\Page\Handler\IndexHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;
use ResponsiveSk\Slim4Paths\Paths;

class IndexHandlerFactory
{
    public function __invoke(ContainerInterface $container): IndexHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        $paths = $container->get(Paths::class);

        return new IndexHandler($template, $paths);
    }
}
