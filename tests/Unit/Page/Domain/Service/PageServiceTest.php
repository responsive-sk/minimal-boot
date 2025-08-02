<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Page\Domain\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use Minimal\Page\Domain\Entity\Page;
use Minimal\Page\Domain\Repository\PageRepositoryInterface;
use Minimal\Page\Domain\Service\PageService;
use MinimalTest\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Unit tests for PageService
 */
class PageServiceTest extends TestCase
{
    private PageService $pageService;
    private PageRepositoryInterface|MockObject $repository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->repository = $this->createMock(PageRepositoryInterface::class);
        $this->pageService = new PageService($this->repository);
    }

    public function testCanCreatePageService(): void
    {
        $this->assertInstanceOf(PageService::class, $this->pageService);
    }

    public function testGetPageBySlugReturnsPageWhenFound(): void
    {
        $slug = 'test-page';
        $page = new Page(
            id: 'page_123',
            slug: $slug,
            title: 'Test Page',
            content: 'Test content',
            isPublished: true
        );

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($page);

        $result = $this->pageService->getPageBySlug($slug);

        $this->assertSame($page, $result);
    }

    public function testGetPageBySlugReturnsNullWhenNotFound(): void
    {
        $slug = 'non-existent-page';

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn(null);

        $result = $this->pageService->getPageBySlug($slug);

        $this->assertNull($result);
    }

    public function testGetPublishedPagesReturnsAllPublishedPages(): void
    {
        $pages = [
            new Page(id: 'page_1', slug: 'page-1', title: 'Page 1', content: 'Content 1', isPublished: true),
            new Page(id: 'page_2', slug: 'page-2', title: 'Page 2', content: 'Content 2', isPublished: true),
        ];

        $this->repository
            ->expects($this->once())
            ->method('findAllPublished')
            ->willReturn($pages);

        $result = $this->pageService->getPublishedPages();

        $this->assertSame($pages, $result);
        $this->assertCount(2, $result);
    }

    public function testCreatePageWithValidDataCreatesPage(): void
    {
        $slug = 'new-page';
        $title = 'New Page';
        $content = 'New page content';
        $metaDescription = 'Meta description';
        $metaKeywords = ['php', 'framework'];

        $this->repository
            ->expects($this->once())
            ->method('existsBySlug')
            ->with($slug)
            ->willReturn(false);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Page $page) use ($slug, $title, $content, $metaDescription) {
                return $page->getSlug() === $slug
                    && $page->getTitle() === $title
                    && $page->getContent() === $content
                    && $page->getMetaDescription() === $metaDescription
                    && $page->getMetaKeywords() === [] // Page::create sets empty array
                    && !$page->isPublished()
                    && $page->getCreatedAt() instanceof DateTimeImmutable
                    && $page->getUpdatedAt() === null; // Page::create sets null for updatedAt
            }));

        $result = $this->pageService->createPage($slug, $title, $content, $metaDescription);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($slug, $result->getSlug());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($content, $result->getContent());
        $this->assertEquals($metaDescription, $result->getMetaDescription());
        $this->assertEquals([], $result->getMetaKeywords()); // Page::create sets empty array
        $this->assertFalse($result->isPublished());
    }

    public function testCreatePageWithMinimalDataCreatesPage(): void
    {
        $slug = 'minimal-page';
        $title = 'Minimal Page';
        $content = 'Minimal content';

        $this->repository
            ->expects($this->once())
            ->method('existsBySlug')
            ->with($slug)
            ->willReturn(false);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Page::class));

        $result = $this->pageService->createPage($slug, $title, $content);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertEquals($slug, $result->getSlug());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($content, $result->getContent());
        $this->assertEquals('', $result->getMetaDescription());
        $this->assertEquals([], $result->getMetaKeywords());
        $this->assertFalse($result->isPublished());
    }

    public function testCreatePageThrowsExceptionWhenSlugExists(): void
    {
        $slug = 'existing-page';

        $this->repository
            ->expects($this->once())
            ->method('existsBySlug')
            ->with($slug)
            ->willReturn(true);

        $this->repository
            ->expects($this->never())
            ->method('save');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Page with slug 'existing-page' already exists");

        $this->pageService->createPage($slug, 'Title', 'Content');
    }

    public function testCreatePageThrowsExceptionForInvalidSlugFormat(): void
    {
        $invalidSlugs = [
            'Invalid Slug',      // spaces
            'invalid_slug',      // underscores
            'INVALID-SLUG',      // uppercase
            'invalid-slug!',     // special characters
            'invalid.slug',      // dots
            '',                  // empty
        ];

        foreach ($invalidSlugs as $slug) {
            $this->repository
                ->expects($this->once())
                ->method('existsBySlug')
                ->with($slug)
                ->willReturn(false);

            $this->repository
                ->expects($this->never())
                ->method('save');

            try {
                $this->pageService->createPage($slug, 'Title', 'Content');
                $this->fail("Expected InvalidArgumentException for slug: {$slug}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('Invalid slug format', $e->getMessage());
            }

            // Reset mock for next iteration
            $this->setUp();
        }
    }

    public function testCreatePageWithValidSlugFormats(): void
    {
        $validSlugs = [
            'valid-slug',
            'valid123',
            'valid-slug-123',
            'a',
            '123',
            'very-long-slug-with-many-dashes-and-numbers-123',
        ];

        foreach ($validSlugs as $slug) {
            $this->repository
                ->expects($this->once())
                ->method('existsBySlug')
                ->with($slug)
                ->willReturn(false);

            $this->repository
                ->expects($this->once())
                ->method('save')
                ->with($this->isInstanceOf(Page::class));

            $result = $this->pageService->createPage($slug, 'Title', 'Content');
            $this->assertEquals($slug, $result->getSlug());

            // Reset mock for next iteration
            $this->setUp();
        }
    }

    public function testPublishPageReturnsPublishedPageWhenFound(): void
    {
        $slug = 'test-page';
        $originalPage = new Page(
            id: 'page_123',
            slug: $slug,
            title: 'Test Page',
            content: 'Test content',
            isPublished: false
        );

        $publishedPage = $originalPage->publish();

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($originalPage);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Page $page) {
                return $page->isPublished() 
                    && $page->getPublishedAt() instanceof DateTimeImmutable
                    && $page->getUpdatedAt() instanceof DateTimeImmutable;
            }));

        $result = $this->pageService->publishPage($slug);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertTrue($result->isPublished());
        $this->assertInstanceOf(DateTimeImmutable::class, $result->getPublishedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $result->getUpdatedAt());
    }

    public function testPublishPageReturnsNullWhenPageNotFound(): void
    {
        $slug = 'non-existent-page';

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn(null);

        $this->repository
            ->expects($this->never())
            ->method('save');

        $result = $this->pageService->publishPage($slug);

        $this->assertNull($result);
    }

    public function testPublishPageWorksWithAlreadyPublishedPage(): void
    {
        $slug = 'already-published';
        $originalPage = new Page(
            id: 'page_456',
            slug: $slug,
            title: 'Already Published',
            content: 'Content',
            isPublished: true,
            publishedAt: new DateTimeImmutable('2025-01-01 10:00:00')
        );

        $this->repository
            ->expects($this->once())
            ->method('findBySlug')
            ->with($slug)
            ->willReturn($originalPage);

        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Page::class));

        $result = $this->pageService->publishPage($slug);

        $this->assertInstanceOf(Page::class, $result);
        $this->assertTrue($result->isPublished());
    }
}
