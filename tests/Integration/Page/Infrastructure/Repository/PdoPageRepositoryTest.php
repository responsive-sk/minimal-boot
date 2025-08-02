<?php

declare(strict_types=1);

namespace MinimalTest\Integration\Page\Infrastructure\Repository;

use DateTimeImmutable;
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Page\Domain\Entity\Page;
use Minimal\Page\Infrastructure\Repository\PdoPageRepository;
use MinimalTest\TestCase;

/**
 * Integration tests for PdoPageRepository
 */
class PdoPageRepositoryTest extends TestCase
{
    private PdoPageRepository $repository;
    private DatabaseConnectionFactory $connectionFactory;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connectionFactory = new DatabaseConnectionFactory(':memory:');
        $this->repository = new PdoPageRepository($this->connectionFactory);
        
        // Create pages table
        $this->createPagesTable();
    }

    private function createPagesTable(): void
    {
        $pdo = $this->connectionFactory->getConnection('page');
        $pdo->exec("
            CREATE TABLE pages (
                id VARCHAR(255) PRIMARY KEY,
                slug VARCHAR(255) NOT NULL UNIQUE,
                title VARCHAR(500) NOT NULL,
                content TEXT NOT NULL,
                meta_description TEXT DEFAULT '',
                meta_keywords TEXT DEFAULT '',
                is_published BOOLEAN DEFAULT 0,
                published_at DATETIME NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL,
                author VARCHAR(255) DEFAULT 'System'
            )
        ");
    }

    public function testCanSaveAndFindPageById(): void
    {
        $page = Page::create('test-page', 'Test Page', 'Test content', 'Test description');
        
        $this->repository->save($page);
        
        $foundPage = $this->repository->findById($page->getId());
        
        $this->assertNotNull($foundPage);
        $this->assertEquals($page->getId(), $foundPage->getId());
        $this->assertEquals($page->getSlug(), $foundPage->getSlug());
        $this->assertEquals($page->getTitle(), $foundPage->getTitle());
        $this->assertEquals($page->getContent(), $foundPage->getContent());
    }

    public function testCanFindPageBySlug(): void
    {
        $page = new Page(
            id: 'test_page_slug',
            slug: 'find-by-slug',
            title: 'Find By Slug Test',
            content: 'Content for slug test',
            isPublished: true
        );
        
        $this->repository->save($page);
        
        $foundPage = $this->repository->findBySlug('find-by-slug');
        
        $this->assertNotNull($foundPage);
        $this->assertEquals('find-by-slug', $foundPage->getSlug());
        $this->assertEquals('Find By Slug Test', $foundPage->getTitle());
    }

    public function testCannotFindUnpublishedPageBySlug(): void
    {
        $page = new Page(
            id: 'unpublished_page',
            slug: 'unpublished-page',
            title: 'Unpublished Page',
            content: 'Draft content',
            isPublished: false
        );
        
        $this->repository->save($page);
        
        $foundPage = $this->repository->findBySlug('unpublished-page');
        
        $this->assertNull($foundPage);
    }

    public function testCanFindAllPublishedPages(): void
    {
        // Create published pages
        $page1 = new Page(
            id: 'published_1',
            slug: 'published-1',
            title: 'Published Page 1',
            content: 'Content 1',
            isPublished: true
        );
        
        $page2 = new Page(
            id: 'published_2',
            slug: 'published-2',
            title: 'Published Page 2',
            content: 'Content 2',
            isPublished: true
        );
        
        // Create unpublished page
        $page3 = new Page(
            id: 'unpublished_1',
            slug: 'unpublished-1',
            title: 'Unpublished Page',
            content: 'Draft content',
            isPublished: false
        );
        
        $this->repository->save($page1);
        $this->repository->save($page2);
        $this->repository->save($page3);
        
        $publishedPages = $this->repository->findAll();
        
        $this->assertCount(2, $publishedPages);
        
        $slugs = array_map(fn($page) => $page->getSlug(), $publishedPages);
        $this->assertContains('published-1', $slugs);
        $this->assertContains('published-2', $slugs);
        $this->assertNotContains('unpublished-1', $slugs);
    }

    public function testCanUpdateExistingPage(): void
    {
        $page = Page::create('update-test', 'Original Title', 'Original content');
        $this->repository->save($page);
        
        // Create updated page with same ID
        $updatedPage = new Page(
            id: $page->getId(),
            slug: 'update-test',
            title: 'Updated Title',
            content: 'Updated content',
            metaDescription: 'Updated description',
            isPublished: true,
            createdAt: $page->getCreatedAt(),
            updatedAt: new DateTimeImmutable()
        );
        
        $this->repository->save($updatedPage);
        
        $foundPage = $this->repository->findById($page->getId());
        
        $this->assertNotNull($foundPage);
        $this->assertEquals('Updated Title', $foundPage->getTitle());
        $this->assertEquals('Updated content', $foundPage->getContent());
        $this->assertEquals('Updated description', $foundPage->getMetaDescription());
        $this->assertTrue($foundPage->isPublished());
    }

    public function testCanDeletePageById(): void
    {
        $page = Page::create('delete-test', 'Delete Test', 'Content to delete');
        $this->repository->save($page);
        
        // Verify page exists
        $this->assertNotNull($this->repository->findById($page->getId()));
        
        // Delete page
        $this->repository->delete($page->getId());
        
        // Verify page is deleted
        $this->assertNull($this->repository->findById($page->getId()));
    }

    public function testCanDeletePageBySlug(): void
    {
        $page = new Page(
            id: 'delete_by_slug',
            slug: 'delete-by-slug',
            title: 'Delete By Slug Test',
            content: 'Content to delete',
            isPublished: true
        );
        
        $this->repository->save($page);
        
        // Verify page exists
        $this->assertNotNull($this->repository->findBySlug('delete-by-slug'));
        
        // Delete page by slug
        $this->repository->deleteBySlug('delete-by-slug');
        
        // Verify page is deleted
        $this->assertNull($this->repository->findBySlug('delete-by-slug'));
    }

    public function testExistsReturnsTrueForExistingPage(): void
    {
        $page = Page::create('exists-test', 'Exists Test', 'Test content');
        $this->repository->save($page);
        
        $this->assertTrue($this->repository->exists($page->getId()));
    }

    public function testExistsReturnsFalseForNonExistingPage(): void
    {
        $this->assertFalse($this->repository->exists('non-existing-id'));
    }

    public function testExistsBySlugReturnsTrueForExistingSlug(): void
    {
        $page = new Page(
            id: 'slug_exists_test',
            slug: 'existing-slug',
            title: 'Slug Exists Test',
            content: 'Test content',
            isPublished: true
        );
        
        $this->repository->save($page);
        
        $this->assertTrue($this->repository->existsBySlug('existing-slug'));
    }

    public function testExistsBySlugReturnsFalseForNonExistingSlug(): void
    {
        $this->assertFalse($this->repository->existsBySlug('non-existing-slug'));
    }

    public function testFindAllPublishedIsSameAsFindPublished(): void
    {
        $page = new Page(
            id: 'published_test',
            slug: 'published-test',
            title: 'Published Test',
            content: 'Published content',
            isPublished: true
        );
        
        $this->repository->save($page);
        
        $findAll = $this->repository->findAll();
        $findPublished = $this->repository->findPublished();
        $findAllPublished = $this->repository->findAllPublished();
        
        $this->assertEquals($findAll, $findPublished);
        $this->assertEquals($findAll, $findAllPublished);
    }

    public function testCanHandlePageWithComplexContent(): void
    {
        $complexContent = '<h1>Complex Content</h1><p>With <strong>HTML</strong> and <em>formatting</em>.</p><ul><li>List item 1</li><li>List item 2</li></ul>';
        
        $page = new Page(
            id: 'complex_content',
            slug: 'complex-content',
            title: 'Page with Complex Content',
            content: $complexContent,
            metaDescription: 'Description with "quotes" and special chars: <>&',
            isPublished: true
        );
        
        $this->repository->save($page);
        
        $foundPage = $this->repository->findBySlug('complex-content');
        
        $this->assertNotNull($foundPage);
        $this->assertEquals($complexContent, $foundPage->getContent());
        $this->assertEquals('Description with "quotes" and special chars: <>&', $foundPage->getMetaDescription());
    }
}
