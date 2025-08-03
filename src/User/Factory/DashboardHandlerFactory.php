<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Handler\DashboardHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

/**
 * Factory for DashboardHandler.
 */
class DashboardHandlerFactory
{
    public function __invoke(ContainerInterface $container): DashboardHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        $authService = $container->get(AuthenticationService::class);

        assert($template instanceof TemplateRendererInterface);
        assert($authService instanceof AuthenticationService);

        return new DashboardHandler($template, $authService);
    }
}
