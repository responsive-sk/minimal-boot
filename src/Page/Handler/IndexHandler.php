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

        // Use theme-specific template from new structure
        $templateName = $currentTheme . '_pages::home';

        $templateData = [];

        return new HtmlResponse(
            $this->template->render($templateName, $templateData)
        );
    }

    private function templateExists(string $templateName): bool
    {
        try {
            // Try to render with empty data to check if template exists
            $this->template->render($templateName, []);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
