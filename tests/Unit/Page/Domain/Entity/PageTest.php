<?php

declare(strict_types=1);

namespace MinimalTest\Unit\Page\Domain\Entity;

use DateTimeImmutable;
use Minimal\Page\Domain\Entity\Page;
use MinimalTest\TestCase;

/**
 * Unit tests for Page Entity
 */
class PageTest extends TestCase
{
    public function testPageCanBeCreatedWithAllProperties(): void
    {
        $id = 'page_123';
        $slug = 'test-page';
        $title = 'Test Page Title';
        $content = '<h1>Test Content</h1>';
        $metaDescription = 'Test meta description';
        $metaKeywords = ['test', 'page', 'keywords'];
        $isPublished = true;
        $publishedAt = new DateTimeImmutable('2025-01-01 12:00:00');
        $createdAt = new DateTimeImmutable('2025-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2025-01-01 11:00:00');

        $page = new Page(
            id: $id,
            slug: $slug,
            title: $title,
            content: $content,
            metaDescription: $metaDescription,
            metaKeywords: $metaKeywords,
            isPublished: $isPublished,
            publishedAt: $publishedAt,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );

        $this->assertEquals($id, $page->getId());
        $this->assertEquals($slug, $page->getSlug());
        $this->assertEquals($title, $page->getTitle());
        $this->assertEquals($content, $page->getContent());
        $this->assertEquals($metaDescription, $page->getMetaDescription());
        $this->assertEquals($metaKeywords, $page->getMetaKeywords());
        $this->assertTrue($page->isPublished());
        $this->assertEquals($publishedAt, $page->getPublishedAt());
        $this->assertEquals($createdAt, $page->getCreatedAt());
        $this->assertEquals($updatedAt, $page->getUpdatedAt());
    }

    public function testPageCanBeCreatedWithMinimalProperties(): void
    {
        $id = 'page_456';
        $slug = 'minimal-page';
        $title = 'Minimal Page';
        $content = 'Simple content';

        $page = new Page(
            id: $id,
            slug: $slug,
            title: $title,
            content: $content
        );

        $this->assertEquals($id, $page->getId());
        $this->assertEquals($slug, $page->getSlug());
        $this->assertEquals($title, $page->getTitle());
        $this->assertEquals($content, $page->getContent());
        $this->assertEquals('', $page->getMetaDescription());
        $this->assertEquals([], $page->getMetaKeywords());
        $this->assertTrue($page->isPublished()); // Default is true
        $this->assertNull($page->getPublishedAt());
        $this->assertNull($page->getCreatedAt());
        $this->assertNull($page->getUpdatedAt());
    }

    public function testPageCanBeCreatedUsingFactoryMethod(): void
    {
        $slug = 'factory-page';
        $title = 'Factory Created Page';
        $content = 'Content from factory';
        $metaDescription = 'Factory meta description';

        $page = Page::create($slug, $title, $content, $metaDescription);

        $this->assertStringStartsWith('page_', $page->getId());
        $this->assertEquals($slug, $page->getSlug());
        $this->assertEquals($title, $page->getTitle());
        $this->assertEquals($content, $page->getContent());
        $this->assertEquals($metaDescription, $page->getMetaDescription());
        $this->assertEquals([], $page->getMetaKeywords());
        $this->assertFalse($page->isPublished()); // Factory creates unpublished pages
        $this->assertNull($page->getPublishedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $page->getCreatedAt());
        $this->assertNull($page->getUpdatedAt());
    }

    public function testPageFactoryGeneratesUniqueIds(): void
    {
        $page1 = Page::create('page-1', 'Page 1', 'Content 1');
        $page2 = Page::create('page-2', 'Page 2', 'Content 2');

        $this->assertNotEquals($page1->getId(), $page2->getId());
        $this->assertStringStartsWith('page_', $page1->getId());
        $this->assertStringStartsWith('page_', $page2->getId());
    }

    public function testPageWithEmptyMetaKeywords(): void
    {
        $page = new Page(
            id: 'page_empty_keywords',
            slug: 'empty-keywords',
            title: 'Page with Empty Keywords',
            content: 'Content',
            metaKeywords: []
        );

        $this->assertEquals([], $page->getMetaKeywords());
    }

    public function testPageWithMultipleMetaKeywords(): void
    {
        $keywords = ['php', 'framework', 'mezzio', 'testing', 'minimal-boot'];

        $page = new Page(
            id: 'page_many_keywords',
            slug: 'many-keywords',
            title: 'Page with Many Keywords',
            content: 'Content',
            metaKeywords: $keywords
        );

        $this->assertEquals($keywords, $page->getMetaKeywords());
        $this->assertCount(5, $page->getMetaKeywords());
    }

    public function testUnpublishedPageDefaults(): void
    {
        $page = new Page(
            id: 'unpublished_page',
            slug: 'unpublished',
            title: 'Unpublished Page',
            content: 'Draft content',
            isPublished: false
        );

        $this->assertFalse($page->isPublished());
        $this->assertNull($page->getPublishedAt());
    }

    public function testPageCreatedAtCurrentTime(): void
    {
        $beforeCreation = new DateTimeImmutable();
        $page = Page::create('time-test', 'Time Test', 'Content');
        $afterCreation = new DateTimeImmutable();

        $this->assertGreaterThanOrEqual($beforeCreation, $page->getCreatedAt());
        $this->assertLessThanOrEqual($afterCreation, $page->getCreatedAt());
    }

    public function testPageWithPublishedAt(): void
    {
        $publishedAt = new DateTimeImmutable('2025-01-01 12:00:00');

        $page = new Page(
            id: 'published_page',
            slug: 'published-page',
            title: 'Published Page',
            content: 'Published content',
            isPublished: true,
            publishedAt: $publishedAt
        );

        $this->assertTrue($page->isPublished());
        $this->assertEquals($publishedAt, $page->getPublishedAt());
    }

    public function testPageWithCreatedAndUpdatedAt(): void
    {
        $createdAt = new DateTimeImmutable('2025-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2025-01-01 11:00:00');

        $page = new Page(
            id: 'timestamped_page',
            slug: 'timestamped-page',
            title: 'Timestamped Page',
            content: 'Content with timestamps',
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );

        $this->assertEquals($createdAt, $page->getCreatedAt());
        $this->assertEquals($updatedAt, $page->getUpdatedAt());
    }

    public function testPageFactoryWithMetaDescription(): void
    {
        $page = Page::create(
            slug: 'factory-full',
            title: 'Factory Full Page',
            content: 'Full factory content',
            metaDescription: 'Factory description'
        );

        $this->assertStringStartsWith('page_', $page->getId());
        $this->assertEquals('factory-full', $page->getSlug());
        $this->assertEquals('Factory Full Page', $page->getTitle());
        $this->assertEquals('Full factory content', $page->getContent());
        $this->assertEquals('Factory description', $page->getMetaDescription());
        $this->assertEquals([], $page->getMetaKeywords());
        $this->assertFalse($page->isPublished());
        $this->assertNull($page->getPublishedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $page->getCreatedAt());
        $this->assertNull($page->getUpdatedAt());
    }

    public function testPagePublishMethod(): void
    {
        $page = Page::create('test-publish', 'Test Publish', 'Content');

        $this->assertFalse($page->isPublished());
        $this->assertNull($page->getPublishedAt());

        $publishedPage = $page->publish();

        $this->assertTrue($publishedPage->isPublished());
        $this->assertInstanceOf(DateTimeImmutable::class, $publishedPage->getPublishedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $publishedPage->getUpdatedAt());

        // Original page should remain unchanged
        $this->assertFalse($page->isPublished());
        $this->assertNull($page->getPublishedAt());
    }
}
