<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequest;
use Mezzio\Template\TemplateRendererInterface;
use Minimal\Page\Domain\Entity\Page;
use Minimal\Page\Domain\Repository\PageRepositoryInterface;
use Minimal\Page\Handler\GetPageViewHandler;
use MinimalTest\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit tests for GetPageViewHandler
 */
class GetPageViewHandlerTest extends TestCase
{
    private GetPageViewHandler $handler;
    private PageRepositoryInterface|MockObject $repository;
    private TemplateRendererInterface|MockObject $template;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = $this->createMock(PageRepositoryInterface::class);
        $this->template = $this->createMock(TemplateRendererInterface::class);
        
        $this->handler = new GetPageViewHandler($this->template, $this->repository);
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

        $request = new ServerRequest();
        $request = $request->withAttribute('slug', $slug);

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($page);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('page::view', [
                'page' => $page,
                'title' => 'Test Page',
                'metaDescription' => 'Test description',
                'metaKeywords' => [],
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
        $request = new ServerRequest();
        $request = $request->withAttribute('slug', $slug);

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn(null);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('error::404', [
                'title' => 'Page Not Found',
                'message' => 'The requested page could not be found.',
            ])
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

        $request = new ServerRequest();
        $request = $request->withAttribute('slug', $slug);

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($page);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('page::view', [
                'page' => $page,
                'title' => 'Page with Keywords',
                'metaDescription' => 'Description',
                'metaKeywords' => ['php', 'framework', 'testing'],
            ])
            ->willReturn('<html>Page with keywords</html>');

        $response = $this->handler->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testHandleWithEmptySlug(): void
    {
        $request = new ServerRequest();
        $request = $request->withAttribute('slug', '');

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with('')
            ->willReturn(null);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('error::404', [
                'title' => 'Page Not Found',
                'message' => 'The requested page could not be found.',
            ])
            ->willReturn('<html>404 Not Found</html>');

        $response = $this->handler->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testHandleWithMissingSlugAttribute(): void
    {
        $request = new ServerRequest();
        // No slug attribute set

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with('')
            ->willReturn(null);

        $this->template
            ->expects($this->once())
            ->method('render')
            ->with('error::404', [
                'title' => 'Page Not Found',
                'message' => 'The requested page could not be found.',
            ])
            ->willReturn('<html>404 Not Found</html>');

        $response = $this->handler->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }
}
