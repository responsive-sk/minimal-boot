<?php

declare(strict_types=1);

namespace Minimal\Page\Domain\Service;

use Minimal\Page\Domain\Entity\Page;

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
     *
     * @param array<string> $metaKeywords
     */
    public function createPage(
        string $slug,
        string $title,
        string $content,
        string $metaDescription = '',
        array $metaKeywords = []
    ): Page;

    /**
     * Publish page.
     */
    public function publishPage(string $slug): ?Page;
}
