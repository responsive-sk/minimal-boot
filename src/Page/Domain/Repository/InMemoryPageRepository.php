<?php

declare(strict_types=1);

namespace Minimal\Page\Domain\Repository;

use DateTimeImmutable;
use Minimal\Page\Domain\Entity\Page;

/**
 * In-memory page repository implementation.
 *
 * Simple implementation for demonstration and testing.
 * In production, this would be replaced with database implementation.
 */
class InMemoryPageRepository implements PageRepositoryInterface
{
    /** @var array<string, Page> */
    private array $pages = [];

    public function __construct()
    {
        $this->initializeDefaultPages();
    }

    public function findBySlug(string $slug): ?Page
    {
        return $this->pages[$slug] ?? null;
    }

    public function findAllPublished(): array
    {
        return array_filter($this->pages, fn(Page $page) => $page->isPublished());
    }

    public function findAll(): array
    {
        return array_values($this->pages);
    }

    public function save(Page $page): void
    {
        $this->pages[$page->getSlug()] = $page;
    }

    public function deleteBySlug(string $slug): void
    {
        unset($this->pages[$slug]);
    }

    public function existsBySlug(string $slug): bool
    {
        return isset($this->pages[$slug]);
    }

    /**
     * Initialize some default pages for demonstration.
     */
    private function initializeDefaultPages(): void
    {
        $now = new DateTimeImmutable();

        $this->pages['about'] = new Page(
            slug: 'about',
            title: 'About Us',
            content: '<h1>About Us</h1><p>Welcome to our company. We are dedicated to providing excellent service.</p>',
            metaDescription: 'Learn more about our company and our mission.',
            metaKeywords: ['about', 'company', 'mission'],
            isPublished: true,
            publishedAt: $now,
            createdAt: $now,
            updatedAt: $now
        );

        $this->pages['privacy'] = new Page(
            slug: 'privacy',
            title: 'Privacy Policy',
            content: '<h1>Privacy Policy</h1><p>Your privacy is important to us. ' .
                'This policy explains how we handle your data.</p>',
            metaDescription: 'Our privacy policy and data handling practices.',
            metaKeywords: ['privacy', 'policy', 'data'],
            isPublished: true,
            publishedAt: $now,
            createdAt: $now,
            updatedAt: $now
        );

        $this->pages['terms'] = new Page(
            slug: 'terms',
            title: 'Terms of Service',
            content: '<h1>Terms of Service</h1><p>These terms govern your use of our service.</p>',
            metaDescription: 'Terms and conditions for using our service.',
            metaKeywords: ['terms', 'service', 'conditions'],
            isPublished: true,
            publishedAt: $now,
            createdAt: $now,
            updatedAt: $now
        );
    }
}
