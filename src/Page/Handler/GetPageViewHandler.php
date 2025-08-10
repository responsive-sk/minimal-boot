<?php

declare(strict_types=1);

namespace Minimal\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Minimal\Page\Domain\Service\PageServiceInterface;
use Minimal\Shared\Service\ThemeService;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function is_string;

class GetPageViewHandler implements RequestHandlerInterface
{
    public function __construct(
        protected TemplateRendererInterface $template,
        protected PageServiceInterface $pageService,
        protected ThemeService $themeService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        assert($routeResult instanceof RouteResult);

        // Get slug from route parameters or route name
        $slug = $routeResult->getMatchedParams()['slug'] ?? '';

        // Handle direct routes like /about
        if (empty($slug)) {
            $routeName = $routeResult->getMatchedRouteName();
            if ($routeName === 'page::about') {
                $slug = 'about';
            }
        }

        assert(is_string($slug));

        // Get page from domain service
        $page = $this->pageService->getPageBySlug($slug);

        if ($page === null || !$page->isPublished()) {
            // Return 404 response
            return new HtmlResponse(
                $this->template->render('error::404', ['slug' => $slug]),
                404
            );
        }

        // Get current theme to determine template
        $currentTheme = $this->themeService->getCurrentTheme();

        // Check if theme-specific page template exists, otherwise use generic view
        $templateName = $this->getPageTemplate($currentTheme, $slug);

        // Render page with data from domain entity using theme-aware template
        return new HtmlResponse(
            $this->template->render($templateName, [
                'page' => $page,
                'title' => $page->getTitle(),
                'content' => $page->getContent(),
                'metaDescription' => $page->getMetaDescription(),
                'metaKeywords' => $page->getMetaKeywords(),
                'cssUrl' => $this->themeService->getThemeCssUrl(),
                'jsUrl' => $this->themeService->getThemeJsUrl(),
            ])
        );
    }

    /**
     * Get appropriate template for page based on theme and slug.
     */
    private function getPageTemplate(string $theme, string $slug): string
    {
        // Special pages that have theme-specific templates
        $specialPages = ['about'];
        $supportedThemes = ['tailwind', 'bootstrap', 'svelte', 'vue', 'react'];

        if (in_array($slug, $specialPages, true) && in_array($theme, $supportedThemes, true)) {
            return $theme . '_pages::' . $slug;
        }

        // For other pages, use generic view template
        return 'page::view';
    }
}
