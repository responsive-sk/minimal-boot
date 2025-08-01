<?php

declare(strict_types=1);

namespace Light\Page\Factory;

use Light\Page\Handler\DemoHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class DemoHandlerFactory
{
    public function __invoke(ContainerInterface $container): DemoHandler
    {
        $template = $container->get(TemplateRendererInterface::class);

        return new DemoHandler($template);
    }
}
