<?php

declare(strict_types=1);

namespace Minimal\Page\Infrastructure\Repository;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Query\QueryBuilder;
use Minimal\Page\Domain\Entity\Page;
use Minimal\Page\Domain\Repository\PageRepositoryInterface;
use PDO;

use function assert;

/**
 * PDO implementation of Page Repository.
 *
 * Uses SQLite database for page storage with query builder.
 */
class PdoPageRepository implements PageRepositoryInterface
{
    private PDO $pdo;

    public function __construct(DatabaseConnectionFactory $connectionFactory)
    {
        $this->pdo = $connectionFactory->getConnection('page');
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->pdo);
    }

    public function findBySlug(string $slug): ?Page
    {
        $data = $this->createQueryBuilder()
            ->table('pages')
            ->where('slug', '=', $slug)
            ->where('is_published', '=', 1)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findById(string $id): ?Page
    {
        $data = $this->createQueryBuilder()
            ->table('pages')
            ->where('id', '=', $id)
            ->first();

        return $data ? $this->mapToEntity($data) : null;
    }

    public function findAll(): array
    {
        $results = $this->createQueryBuilder()
            ->table('pages')
            ->where('is_published', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        return array_map([$this, 'mapToEntity'], $results);
    }

    public function findAllPublished(): array
    {
        return $this->findAll(); // Same as findAll since we filter by is_published
    }

    public function save(Page $page): void
    {
        $data = $this->mapToArray($page);

        if ($this->exists($page->getId())) {
            $this->createQueryBuilder()
                ->table('pages')
                ->where('id', '=', $page->getId())
                ->update($data);
        } else {
            $this->createQueryBuilder()
                ->table('pages')
                ->insert($data);
        }
    }

    public function delete(string $id): void
    {
        $this->createQueryBuilder()
            ->table('pages')
            ->where('id', '=', $id)
            ->delete();
    }

    public function deleteBySlug(string $slug): void
    {
        $this->createQueryBuilder()
            ->table('pages')
            ->where('slug', '=', $slug)
            ->delete();
    }

    public function exists(string $id): bool
    {
        $count = $this->createQueryBuilder()
            ->table('pages')
            ->where('id', '=', $id)
            ->count();

        return $count > 0;
    }

    public function existsBySlug(string $slug): bool
    {
        $count = $this->createQueryBuilder()
            ->table('pages')
            ->where('slug', '=', $slug)
            ->count();

        return $count > 0;
    }

    /**
     * Map database row to Page entity.
     */
    /**
     * @param array<string, mixed> $data
     */
    private function mapToEntity(array $data): Page
    {
        assert(is_string($data['id']));
        assert(is_string($data['slug']));
        assert(is_string($data['title']));
        assert(is_string($data['content']));
        assert(is_string($data['meta_description'] ?? ''));

        $createdAt = null;
        if ($data['created_at']) {
            assert(is_string($data['created_at']));
            $createdAt = new \DateTimeImmutable($data['created_at']);
        }

        $updatedAt = null;
        if ($data['updated_at']) {
            assert(is_string($data['updated_at']));
            $updatedAt = new \DateTimeImmutable($data['updated_at']);
        }

        return new Page(
            id: $data['id'],
            slug: $data['slug'],
            title: $data['title'],
            content: $data['content'],
            metaDescription: $data['meta_description'] ?? '',
            isPublished: (bool) $data['is_published'],
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    /**
     * Map Page entity to database array.
     */
    /**
     * @return array<string, mixed>
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
            'created_at' => $page->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $page->getUpdatedAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
