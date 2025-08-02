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
use MinimalTest\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Complete unit tests for GetPageViewHandler
 */
class CompleteGetPageViewHandlerTest extends TestCase
{
    private GetPageViewHandler $handler;
    private PageServiceInterface|MockObject $pageService;
    private TemplateRendererInterface|MockObject $template;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageService = $this->createMock(PageServiceInterface::class);
        $this->template = $this->createMock(TemplateRendererInterface::class);

        $this->handler = new GetPageViewHandler($this->template, $this->pageService);
    }

    public function testCanCreateHandler(): void
    {
        $this->assertInstanceOf(GetPageViewHandler::class, $this->handler);
    }

    public function testHandleReturnsPageWhenFoundAndPublished(): void
    {
        $slug = 'test-page';
        $page = new Page(
            id: 'page_123',
            slug: $slug,
            title: 'Test Page',
            content: '<h1>Test Content</h1>',
            metaDescription: 'Test description',
            metaKeywords: ['test', 'page'],
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

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('page::view', [
                'page' => $page,
                'title' => 'Test Page',
                'metaDescription' => 'Test description',
                'metaKeywords' => ['test', 'page'],
                'content' => '<h1>Test Content</h1>',
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

    public function testHandleReturns404WhenPageFoundButNotPublished(): void
    {
        $slug = 'unpublished-page';
        $page = new Page(
            id: 'page_456',
            slug: $slug,
            title: 'Unpublished Page',
            content: 'Draft content',
            isPublished: false
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

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('error::404', ['slug' => $slug])
            ->willReturn('<html>404 Not Found</html>');

        $response = $this->handler->handle($request);

        $this->assertInstanceOf(HtmlResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testHandleWithPageWithEmptyMetaKeywords(): void
    {
        $slug = 'page-no-keywords';
        $page = new Page(
            id: 'page_789',
            slug: $slug,
            title: 'Page without Keywords',
            content: '<h1>Content</h1>',
            metaDescription: 'Description',
            metaKeywords: [],
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

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('page::view', [
                'page' => $page,
                'title' => 'Page without Keywords',
                'metaDescription' => 'Description',
                'metaKeywords' => [],
                'content' => '<h1>Content</h1>',
            ])
            ->willReturn('<html>Page without keywords</html>');

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

    public function testHandleWithMissingSlugInRouteParams(): void
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

    public function testHandleWithComplexPageContent(): void
    {
        $slug = 'complex-page';
        $complexContent = '<h1>Complex Content</h1><p>With <strong>HTML</strong> and <em>formatting</em>.</p><ul><li>List item 1</li><li>List item 2</li></ul>';
        $complexDescription = 'Description with "quotes" and special chars: <>&';
        $complexKeywords = ['php', 'framework', 'mezzio', 'testing', 'minimal-boot'];

        $page = new Page(
            id: 'page_complex',
            slug: $slug,
            title: 'Complex Page Title',
            content: $complexContent,
            metaDescription: $complexDescription,
            metaKeywords: $complexKeywords,
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

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('page::view', [
                'page' => $page,
                'title' => 'Complex Page Title',
                'metaDescription' => $complexDescription,
                'metaKeywords' => $complexKeywords,
                'content' => $complexContent,
            ])
            ->willReturn('<html>Complex page rendered</html>');

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('<html>Complex page rendered</html>', (string) $response->getBody());
    }

    public function testHandleWithNullSlugInRouteParams(): void
    {
        $routeResult = $this->createMock(RouteResult::class);
        $routeResult
            ->expects($this->once())
            ->method('getMatchedParams')
            ->willReturn(['slug' => null]);

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
