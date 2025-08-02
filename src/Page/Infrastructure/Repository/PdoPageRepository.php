<?php

declare(strict_types=1);

namespace Minimal\Page\Infrastructure\Repository;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Query\QueryBuilder;
use Minimal\Page\Domain\Entity\Page;
use Minimal\Page\Domain\Repository\PageRepositoryInterface;
use PDO;

/**
 * PDO implementation of Page Repository.
 * 
 * Uses SQLite database for page storage with query builder.
 */
class PdoPageRepository implements PageRepositoryInterface
{
    private PDO $pdo;
    private QueryBuilder $queryBuilder;

    public function __construct(DatabaseConnectionFactory $connectionFactory)
    {
        $this->pdo = $connectionFactory->getConnection('page');
        $this->queryBuilder = new QueryBuilder($this->pdo);
    }

    public function findBySlug(string $slug): ?Page
    {
        $data = $this->queryBuilder
            ->table('pages')
            ->where('slug', '=', $slug)
            ->where('is_published', '=', 1)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findById(string $id): ?Page
    {
        $data = $this->queryBuilder
            ->table('pages')
            ->where('id', '=', $id)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findAll(): array
    {
        $results = $this->queryBuilder
            ->table('pages')
            ->where('is_published', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        return array_map([$this, 'mapToEntity'], $results);
    }

    public function findPublished(): array
    {
        return $this->findAll(); // Same as findAll since we filter by is_published
    }

    public function findAllPublished(): array
    {
        return $this->findPublished(); // Alias for findPublished
    }

    public function save(Page $page): void
    {
        $data = $this->mapToArray($page);
        
        if ($this->exists($page->getId())) {
            $this->queryBuilder
                ->table('pages')
                ->where('id', '=', $page->getId())
                ->update($data);
        } else {
            $this->queryBuilder
                ->table('pages')
                ->insert($data);
        }
    }

    public function delete(string $id): void
    {
        $this->queryBuilder
            ->table('pages')
            ->where('id', '=', $id)
            ->delete();
    }

    public function deleteBySlug(string $slug): void
    {
        $this->queryBuilder
            ->table('pages')
            ->where('slug', '=', $slug)
            ->delete();
    }

    public function exists(string $id): bool
    {
        $count = $this->queryBuilder
            ->table('pages')
            ->where('id', '=', $id)
            ->count();

        return $count > 0;
    }

    public function existsBySlug(string $slug): bool
    {
        $count = $this->queryBuilder
            ->table('pages')
            ->where('slug', '=', $slug)
            ->count();

        return $count > 0;
    }

    /**
     * Map database row to Page entity.
     */
    private function mapToEntity(array $data): Page
    {
        return new Page(
            id: $data['id'],
            slug: $data['slug'],
            title: $data['title'],
            content: $data['content'],
            metaDescription: $data['meta_description'] ?? '',
            isPublished: (bool) $data['is_published'],
            createdAt: new \DateTimeImmutable($data['created_at']),
            updatedAt: $data['updated_at'] ? new \DateTimeImmutable($data['updated_at']) : null
        );
    }

    /**
     * Map Page entity to database array.
     */
    private function mapToArray(Page $page): array
    {
        return [
            'id' => $page->getId(),
            'slug' => $page->getSlug(),
            'title' => $page->getTitle(),
            'content' => $page->getContent(),
            'meta_description' => $page->getMetaDescription(),
            'is_published' => $page->isPublished() ? 1 : 0,
            'created_at' => $page->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $page->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
