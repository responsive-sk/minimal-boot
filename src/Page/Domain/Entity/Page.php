<?php

declare(strict_types=1);

namespace Minimal\Page\Domain\Entity;

use DateTimeImmutable;

/**
 * Page entity representing a static page in the system.
 *
 * Domain entity following DDD principles.
 */
class Page
{
    public function __construct(
        private readonly string $id,
        private readonly string $slug,
        private readonly string $title,
        private readonly string $content,
        private readonly string $metaDescription = '',
        /** @var array<string> */
        private readonly array $metaKeywords = [],
        private readonly bool $isPublished = true,
        private readonly ?DateTimeImmutable $publishedAt = null,
        private readonly ?DateTimeImmutable $createdAt = null,
        private readonly ?DateTimeImmutable $updatedAt = null
    ) {
    }

    public static function create(
        string $slug,
        string $title,
        string $content,
        string $metaDescription = ''
    ): self {
        return new self(
            id: uniqid('page_', true),
            slug: $slug,
            title: $title,
            content: $content,
            metaDescription: $metaDescription,
            metaKeywords: [],
            isPublished: false,
            publishedAt: null,
            createdAt: new DateTimeImmutable(),
            updatedAt: null
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    /**
     * @return array<string>
     */
    public function getMetaKeywords(): array
    {
        return $this->metaKeywords;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Create a new page with updated content.
     */
    public function withContent(string $content): self
    {
        return new self(
            $this->slug,
            $this->title,
            $content,
            $this->metaDescription,
            $this->metaKeywords,
            $this->isPublished,
            $this->publishedAt,
            $this->createdAt,
            new DateTimeImmutable()
        );
    }

    /**
     * Publish the page.
     */
    public function publish(): self
    {
        return new self(
            $this->slug,
            $this->title,
            $this->content,
            $this->metaDescription,
            $this->metaKeywords,
            true,
            new DateTimeImmutable(),
            $this->createdAt,
            new DateTimeImmutable()
        );
    }

    /**
     * Unpublish the page.
     */
    public function unpublish(): self
    {
        return new self(
            $this->slug,
            $this->title,
            $this->content,
            $this->metaDescription,
            $this->metaKeywords,
            false,
            null,
            $this->createdAt,
            new DateTimeImmutable()
        );
    }
}
