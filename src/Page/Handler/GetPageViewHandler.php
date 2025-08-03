<?php

declare(strict_types=1);

namespace Minimal\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Minimal\Page\Domain\Service\PageServiceInterface;
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
        protected PageServiceInterface $pageService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $routeResult = $request->getAttribute(RouteResult::class);
        assert($routeResult instanceof RouteResult);

        // Get slug from route parameters
        $slug = $routeResult->getMatchedParams()['slug'] ?? '';
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

        // Render page with data from domain entity using new template structure
        return new HtmlResponse(
            $this->template->render('page::view', [
                'page' => $page,
                'title' => $page->getTitle(),
                'content' => $page->getContent(),
                'metaDescription' => $page->getMetaDescription(),
                'metaKeywords' => $page->getMetaKeywords(),
            ])
        );
    }
}
