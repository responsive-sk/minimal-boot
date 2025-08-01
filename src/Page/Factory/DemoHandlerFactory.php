<?php

declare(strict_types=1);

namespace Minimal\Page\Factory;

use Minimal\Page\Handler\DemoHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class DemoHandlerFactory
{
    public function __invoke(ContainerInterface $container): DemoHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new DemoHandler($template);
    }
}
