<?php

declare(strict_types=1);

namespace Light\Page\Domain\Service;

use DateTimeImmutable;
use Light\Page\Domain\Entity\Page;
use Light\Page\Domain\Repository\PageRepositoryInterface;

/**
 * Page domain service implementation.
 *
 * Contains business logic for page operations following DDD principles.
 */
class PageService implements PageServiceInterface
{
    public function __construct(
        private readonly PageRepositoryInterface $pageRepository
    ) {
    }

    public function getPageBySlug(string $slug): ?Page
    {
        return $this->pageRepository->findBySlug($slug);
    }

    public function getPublishedPages(): array
    {
        return $this->pageRepository->findAllPublished();
    }

    /**
     * @param array<string> $metaKeywords
     */
    public function createPage(
        string $slug,
        string $title,
        string $content,
        string $metaDescription = '',
        array $metaKeywords = []
    ): Page {
        // Business rule: slug must be unique
        if ($this->pageRepository->existsBySlug($slug)) {
            throw new \InvalidArgumentException("Page with slug '{$slug}' already exists");
        }

        // Business rule: slug must be valid (alphanumeric and dashes only)
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new \InvalidArgumentException(
                "Invalid slug format. Use only lowercase letters, numbers, and dashes."
            );
        }

        $now = new DateTimeImmutable();

        $page = new Page(
            slug: $slug,
            title: $title,
            content: $content,
            metaDescription: $metaDescription,
            metaKeywords: $metaKeywords,
            isPublished: false, // New pages are unpublished by default
            publishedAt: null,
            createdAt: $now,
            updatedAt: $now
        );

        $this->pageRepository->save($page);

        return $page;
    }

    public function updatePageContent(string $slug, string $content): ?Page
    {
        $page = $this->pageRepository->findBySlug($slug);

        if ($page === null) {
            return null;
        }

        $updatedPage = $page->withContent($content);
        $this->pageRepository->save($updatedPage);

        return $updatedPage;
    }

    public function publishPage(string $slug): ?Page
    {
        $page = $this->pageRepository->findBySlug($slug);

        if ($page === null) {
            return null;
        }

        $publishedPage = $page->publish();
        $this->pageRepository->save($publishedPage);

        return $publishedPage;
    }

    public function unpublishPage(string $slug): ?Page
    {
        $page = $this->pageRepository->findBySlug($slug);

        if ($page === null) {
            return null;
        }

        $unpublishedPage = $page->unpublish();
        $this->pageRepository->save($unpublishedPage);

        return $unpublishedPage;
    }

    public function deletePage(string $slug): bool
    {
        if (!$this->pageRepository->existsBySlug($slug)) {
            return false;
        }

        $this->pageRepository->deleteBySlug($slug);
        return true;
    }

    public function pageExists(string $slug): bool
    {
        return $this->pageRepository->existsBySlug($slug);
    }
}
