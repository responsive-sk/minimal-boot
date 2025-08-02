<?php

declare(strict_types=1);

namespace Minimal\User\Factory;

use Minimal\User\Domain\Service\AuthenticationService;
use Minimal\User\Handler\DashboardHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

/**
 * Factory for DashboardHandler.
 */
class DashboardHandlerFactory
{
    public function __invoke(ContainerInterface $container): DashboardHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        $authService = $container->get(AuthenticationService::class);

        return new DashboardHandler($template, $authService);
    }
}
