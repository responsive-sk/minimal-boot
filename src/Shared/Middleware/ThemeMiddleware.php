<?php

declare(strict_types=1);

namespace Minimal\Shared\Middleware;

use Minimal\Shared\Service\ThemeService;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Theme middleware - adds ThemeService to template variables.
 */
class ThemeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ThemeService $themeService,
        private TemplateRendererInterface $template
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Add ThemeService to template default parameters
        if (method_exists($this->template, 'addDefaultParam')) {
            $this->template->addDefaultParam(TemplateRendererInterface::TEMPLATE_ALL, 'themeService', $this->themeService);
        }

        return $handler->handle($request);
    }
}
