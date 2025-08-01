<?php

declare(strict_types=1);

namespace Minimal\Page\Factory;

use Minimal\Page\Handler\BootstrapDemoHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class BootstrapDemoHandlerFactory
{
    public function __invoke(ContainerInterface $container): BootstrapDemoHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new BootstrapDemoHandler($template);
    }
}
