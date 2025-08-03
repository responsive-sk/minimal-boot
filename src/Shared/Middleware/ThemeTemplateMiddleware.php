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
 * Theme template middleware - dynamically sets template paths based on active theme.
 */
class ThemeTemplateMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ThemeService $themeService,
        private TemplateRendererInterface $template
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Get current theme template path
        $currentTheme = $this->themeService->getCurrentTheme();
        $templatePath = $this->themeService->getThemeTemplatePath($currentTheme);

        // Update template paths dynamically using reflection to access private property
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        if (method_exists($this->template, 'addPath')) {
            try {
                $reflection = new \ReflectionClass($this->template);
                $pathsProperty = $reflection->getProperty('paths');
                $pathsProperty->setAccessible(true);

                $paths = $pathsProperty->getValue($this->template);

                // Replace layout paths with theme-specific path
                $paths['layout'] = [$templatePath . '/layout'];

                $pathsProperty->setValue($this->template, $paths);
            } catch (\ReflectionException $e) {
                // Fallback: just add the path (will be appended)
                $this->template->addPath($templatePath . '/layout', 'layout');
            }
        }

        return $handler->handle($request);
    }
}
