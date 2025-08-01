<?php

declare(strict_types=1);

namespace Light\Page\Domain\Service;

use Light\Page\Domain\Entity\Page;

/**
 * Page domain service interface.
 * 
 * Defines business logic operations for pages following DDD principles.
 */
interface PageServiceInterface
{
    /**
     * Get page by slug.
     */
    public function getPageBySlug(string $slug): ?Page;

    /**
     * Get all published pages.
     * 
     * @return array<Page>
     */
    public function getPublishedPages(): array;

    /**
     * Create a new page.
     */
    public function createPage(
        string $slug,
        string $title,
        string $content,
        string $metaDescription = '',
        array $metaKeywords = []
    ): Page;

    /**
     * Update page content.
     */
    public function updatePageContent(string $slug, string $content): ?Page;

    /**
     * Publish page.
     */
    public function publishPage(string $slug): ?Page;

    /**
     * Unpublish page.
     */
    public function unpublishPage(string $slug): ?Page;

    /**
     * Delete page.
     */
    public function deletePage(string $slug): bool;

    /**
     * Check if page exists.
     */
    public function pageExists(string $slug): bool;
}
