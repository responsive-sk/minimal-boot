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

        // Debug: Force log to file
        @file_put_contents('var/logs/debug.log', "ThemeTemplateMiddleware: theme='{$currentTheme}', path='{$templatePath}'\n", FILE_APPEND);

        // Update template paths dynamically using reflection to access private property
        // @phpstan-ignore-next-line method.alreadyNarrowedType
        if (method_exists($this->template, 'addPath')) {
            try {
                $reflection = new \ReflectionClass($this->template);
                $pathsProperty = $reflection->getProperty('paths');

                $paths = $pathsProperty->getValue($this->template);

                // Define theme-specific namespaces
                $themeNamespace = $currentTheme . '_pages';
                $layoutNamespace = $currentTheme . '_layouts';
                $errorNamespace = $currentTheme . '_error';

                // Set theme-specific paths
                $paths[$themeNamespace] = [$templatePath . '/pages'];
                $paths[$layoutNamespace] = [$templatePath . '/layouts'];
                $paths[$errorNamespace] = [$templatePath . '/error'];

                // Also update layout paths for backward compatibility
                $paths['layout'] = [$templatePath . '/layouts'];

                $pathsProperty->setValue($this->template, $paths);
            } catch (\ReflectionException $e) {
                // Fallback: just add the paths (will be appended)
                $themeNamespace = $currentTheme . '_pages';
                $layoutNamespace = $currentTheme . '_layouts';
                $errorNamespace = $currentTheme . '_error';

                $this->template->addPath($templatePath . '/pages', $themeNamespace);
                $this->template->addPath($templatePath . '/layouts', $layoutNamespace);
                $this->template->addPath($templatePath . '/error', $errorNamespace);
                $this->template->addPath($templatePath . '/layouts', 'layout');
            }
        }

        return $handler->handle($request);
    }
}
