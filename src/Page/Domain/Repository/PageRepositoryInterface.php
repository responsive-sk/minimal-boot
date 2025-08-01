<?php

declare(strict_types=1);

namespace Light\Page\Domain\Repository;

use Light\Page\Domain\Entity\Page;

/**
 * Page repository interface.
 * 
 * Defines contract for page data access following DDD principles.
 */
interface PageRepositoryInterface
{
    /**
     * Find page by slug.
     */
    public function findBySlug(string $slug): ?Page;

    /**
     * Find all published pages.
     * 
     * @return array<Page>
     */
    public function findAllPublished(): array;

    /**
     * Find all pages.
     * 
     * @return array<Page>
     */
    public function findAll(): array;

    /**
     * Save page.
     */
    public function save(Page $page): void;

    /**
     * Delete page by slug.
     */
    public function deleteBySlug(string $slug): void;

    /**
     * Check if page exists by slug.
     */
    public function existsBySlug(string $slug): bool;
}
