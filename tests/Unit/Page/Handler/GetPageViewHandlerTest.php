<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Router\RouteResult;
use Mezzio\Template\TemplateRendererInterface;
use Minimal\Page\Domain\Entity\Page;
use Minimal\Page\Domain\Service\PageServiceInterface;
use Minimal\Page\Handler\GetPageViewHandler;
use Minimal\Shared\Service\ThemeService;
use MinimalTest\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit tests for GetPageViewHandler
 */
class GetPageViewHandlerTest extends TestCase
{
    private GetPageViewHandler $handler;
    private PageServiceInterface|MockObject $pageService;
    private TemplateRendererInterface|MockObject $template;
    private ThemeService|MockObject $themeService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageService = $this->createMock(PageServiceInterface::class);
        $this->template = $this->createMock(TemplateRendererInterface::class);
        $this->themeService = $this->createMock(ThemeService::class);

        $this->handler = new GetPageViewHandler($this->template, $this->pageService, $this->themeService);
    }

    public function testCanCreateHandler(): void
    {
        $this->assertInstanceOf(GetPageViewHandler::class, $this->handler);
    }

    public function testHandleReturnsPageWhenFound(): void
    {
        $slug = 'test-page';
        $page = new Page(
            id: 'page_123',
            slug: $slug,
            title: 'Test Page',
            content: '<h1>Test Content</h1>',
            metaDescription: 'Test description',
            isPublished: true
        );

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn(['slug' => $slug]);

        $request = new ServerRequest();
        $request = $request->withAttribute(RouteResult::class, $routeResult);

        $this->pageService
            ->expects($this->once())
            ->method('getPageBySlug')
            ->with($slug)
            ->willReturn($page);

        $this->themeService
            ->expects($this->once())
            ->method('getThemeCssUrl')
            ->willReturn('/css/theme.css');

        $this->themeService
            ->expects($this->once())
            ->method('getThemeJsUrl')
            ->willReturn('/js/theme.js');

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('page::view', [
                'page' => $page,
                'title' => 'Test Page',
                'content' => '<h1>Test Content</h1>',
                'metaDescription' => 'Test description',
                'metaKeywords' => [],
                'cssUrl' => '/css/theme.css',
                'jsUrl' => '/js/theme.js',
            ])
            ->willReturn('<html>Rendered page</html>');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<html>Rendered page</html>', (string) $response->getBody());
    }

    public function testHandleReturns404WhenPageNotFound(): void
    {
        $slug = 'non-existent-page';

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn(['slug' => $slug]);

        $request = new ServerRequest();
        $request = $request->withAttribute(RouteResult::class, $routeResult);

        $this->pageService
            ->expects($this->once())
            ->method('getPageBySlug')
            ->with($slug)
            ->willReturn(null);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('error::404', ['slug' => $slug])
            ->willReturn('<html>404 Not Found</html>');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('<html>404 Not Found</html>', (string) $response->getBody());
    }

    public function testHandleWithPageWithMetaKeywords(): void
    {
        $slug = 'page-with-keywords';
        $page = new Page(
            id: 'page_456',
            slug: $slug,
            title: 'Page with Keywords',
            content: '<h1>Content</h1>',
            metaDescription: 'Description',
            metaKeywords: ['php', 'framework', 'testing'],
            isPublished: true
        );

        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn(['slug' => $slug]);

        $request = new ServerRequest();
        $request = $request->withAttribute(RouteResult::class, $routeResult);

        $this->pageService
            ->expects($this->once())
            ->method('getPageBySlug')
            ->with($slug)
            ->willReturn($page);

        $this->themeService
            ->expects($this->once())
            ->method('getThemeCssUrl')
            ->willReturn('/css/theme.css');

        $this->themeService
            ->expects($this->once())
            ->method('getThemeJsUrl')
            ->willReturn('/js/theme.js');

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('page::view', [
                'page' => $page,
                'title' => 'Page with Keywords',
                'content' => '<h1>Content</h1>',
                'metaDescription' => 'Description',
                'metaKeywords' => ['php', 'framework', 'testing'],
                'cssUrl' => '/css/theme.css',
                'jsUrl' => '/js/theme.js',
            ])
            ->willReturn('<html>Page with keywords</html>');

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHandleWithEmptySlug(): void
    {
        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn(['slug' => '']);

        $request = new ServerRequest();
        $request = $request->withAttribute(RouteResult::class, $routeResult);

        $this->pageService
            ->expects($this->once())
            ->method('getPageBySlug')
            ->with('')
            ->willReturn(null);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('error::404', ['slug' => ''])
            ->willReturn('<html>404 Not Found</html>');

        $response = $this->handler->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testHandleWithMissingSlugAttribute(): void
    {
        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn([]); // No slug parameter

        $request = new ServerRequest();
        $request = $request->withAttribute(RouteResult::class, $routeResult);

        $this->pageService
            ->expects($this->once())
            ->method('getPageBySlug')
            ->with('')
            ->willReturn(null);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('error::404', ['slug' => ''])
            ->willReturn('<html>404 Not Found</html>');

        $response = $this->handler->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
