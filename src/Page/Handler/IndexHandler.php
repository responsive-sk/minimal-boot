<?php

declare(strict_types=1);

namespace Minimal\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Minimal\Shared\Service\ThemeService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ResponsiveSk\Slim4Paths\Paths;

class IndexHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected Paths $paths,
        protected ThemeService $themeService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get current theme to determine template
        $currentTheme = $this->themeService->getCurrentTheme();

        // Debug: Log IndexHandler execution
        @file_put_contents('var/logs/debug.log', "IndexHandler: currentTheme='{$currentTheme}' for path=" . $request->getUri()->getPath() . "\n", FILE_APPEND);

        // Use theme-specific template from new structure
        $templateName = $currentTheme . '_pages::home';

        $templateData = [
            'title' => 'Home - Mezzio Light Application',
            'description' => 'Welcome to Mezzio Light - A modern, fast, and secure PHP application framework',
            'author' => 'Dotkernel Team',
            'cssUrl' => $this->themeService->getThemeCssUrl(),
            'jsUrl' => $this->themeService->getThemeJsUrl(),
            'debug_theme' => $currentTheme,
            'debug_template' => $templateName
        ];

        return new HtmlResponse(
            $this->template->render($templateName, $templateData)
        );
    }
}
