---
layout: page
title: "Database Architecture"
description: "Modular SQLite database architecture with native PDO and migrations"
nav_order: 8
---

# Database Architecture

Minimal Boot implements a modular database architecture using SQLite with native PDO. Each module/domain has its own database file for better separation of concerns and easier maintenance.

## Architecture Overview

### Modular Database Design

```
var/db/
├── page.sqlite      # Page management data
├── contact.sqlite   # Contact form submissions  
├── auth.sqlite      # User authentication data
└── session.sqlite   # Session storage
```

### Benefits

- **Separation of Concerns** - Each domain has isolated data
- **Easier Maintenance** - Module-specific database operations
- **Better Performance** - Smaller, focused databases
- **Simplified Backup** - Backup specific modules independently
- **Development Flexibility** - Test modules in isolation

## Database Components

### Connection Factory

The `DatabaseConnectionFactory` manages connections to module-specific databases:

```php
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;

$factory = new DatabaseConnectionFactory('var/db');

// Get connection for specific module
$pageDb = $factory->getConnection('page');
$contactDb = $factory->getConnection('contact');
```

### Query Builder

Simple, fluent query builder for common operations:

```php
use Minimal\Core\Database\Query\QueryBuilder;

$queryBuilder = new QueryBuilder($pdo);

// Select operations
$pages = $queryBuilder
    ->table('pages')
    ->where('is_published', '=', 1)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Insert operation
$queryBuilder
    ->table('pages')
    ->insert([
        'id' => 'page_123',
        'slug' => 'example',
        'title' => 'Example Page',
        'content' => 'Page content...',
        'is_published' => 1
    ]);

// Update operation
$queryBuilder
    ->table('pages')
    ->where('id', '=', 'page_123')
    ->update(['title' => 'Updated Title']);

// Delete operation
$queryBuilder
    ->table('pages')
    ->where('id', '=', 'page_123')
    ->delete();
```

### Migration System

Simple PHP-based migration system for schema management:

```php
use Minimal\Core\Database\Migration\MigrationRunner;

$migrationRunner = new MigrationRunner($connectionFactory, 'migrations');

// Run migrations for specific module
$newMigrations = $migrationRunner->runMigrations('page');

// Run migrations for all modules
$allResults = $migrationRunner->runAllMigrations();

// Create new migration
$migrationFile = $migrationRunner->createMigration('page', 'add_author_column');
```

## Repository Implementation

### PDO Repository Example

```php
<?php

namespace Minimal\Page\Infrastructure\Repository;

use Minimal\Core\Database\Connection\DatabaseConnectionFactory;
use Minimal\Core\Database\Query\QueryBuilder;
use Minimal\Page\Domain\Entity\Page;
use Minimal\Page\Domain\Repository\PageRepositoryInterface;

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
}
```

## Database Configuration

### Global Configuration

```php
// config/autoload/database.global.php
return [
    'database' => [
        'path' => 'var/db',
        'modules' => [
            'page' => 'page.sqlite',
            'contact' => 'contact.sqlite',
            'auth' => 'auth.sqlite',
            'session' => 'session.sqlite',
        ],
        'migrations' => [
            'path' => 'migrations',
            'auto_run' => false,
        ],
        'connection' => [
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
            'pragmas' => [
                'foreign_keys' => 'ON',
                'journal_mode' => 'WAL',
                'synchronous' => 'NORMAL',
            ],
        ],
    ],
];
```

## Migrations

### Migration Structure

```
migrations/
├── page/
│   ├── 2025_01_02_120000_create_pages_table.sql
│   └── 2025_01_02_130000_add_author_column.sql
├── contact/
│   └── 2025_01_02_120000_create_contacts_table.sql
└── auth/
    └── 2025_01_02_120000_create_users_table.sql
```

### Migration File Example

```sql
-- Migration: create_pages_table
-- Created: 2025-01-02 12:00:00

CREATE TABLE pages (
    id VARCHAR(255) PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    meta_description TEXT DEFAULT '',
    is_published BOOLEAN DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

CREATE INDEX idx_pages_slug ON pages(slug);
CREATE INDEX idx_pages_published ON pages(is_published, created_at);
```

### Running Migrations

```bash
# Create migration runner script
php bin/migrate.php page

# Or run all migrations
php bin/migrate.php --all
```

## Database Setup

### 1. Create Database Directory

```bash
mkdir -p var/db
chmod 755 var/db
```

### 2. Run Initial Migrations

```php
// In your bootstrap or setup script
$connectionFactory = new DatabaseConnectionFactory('var/db');
$migrationRunner = new MigrationRunner($connectionFactory, 'migrations');

// Run migrations for all modules
$results = $migrationRunner->runAllMigrations();

foreach ($results as $module => $migrations) {
    echo "Module {$module}: " . count($migrations) . " migrations executed\n";
}
```

### 3. Verify Database Creation

```bash
ls -la var/db/
# Should show: page.sqlite, contact.sqlite, etc.
```

## Performance Optimization

### SQLite Pragmas

```sql
PRAGMA foreign_keys = ON;        -- Enable foreign key constraints
PRAGMA journal_mode = WAL;       -- Write-Ahead Logging for better concurrency
PRAGMA synchronous = NORMAL;     -- Balance between safety and performance
PRAGMA cache_size = 10000;       -- Increase cache size
PRAGMA temp_store = MEMORY;      -- Store temporary tables in memory
```

### Indexing Strategy

```sql
-- Primary keys (automatic)
-- Unique constraints for lookups
-- Composite indexes for common queries
CREATE INDEX idx_pages_published_date ON pages(is_published, created_at);
CREATE INDEX idx_contacts_status_date ON contacts(status, created_at);
```

### Connection Pooling

The connection factory reuses connections per module:

```php
// Same connection instance returned for same module
$conn1 = $factory->getConnection('page');
$conn2 = $factory->getConnection('page'); // Same instance
```

## Testing

### Repository Testing

```php
class PdoPageRepositoryTest extends TestCase
{
    private DatabaseConnectionFactory $connectionFactory;
    private PdoPageRepository $repository;

    protected function setUp(): void
    {
        // Use in-memory SQLite for testing
        $this->connectionFactory = new DatabaseConnectionFactory(':memory:');
        $this->repository = new PdoPageRepository($this->connectionFactory);
        
        // Run migrations
        $migrationRunner = new MigrationRunner($this->connectionFactory, 'migrations');
        $migrationRunner->runMigrations('page');
    }

    public function testSaveAndFindPage(): void
    {
        $page = Page::create('test', 'Test Page', 'Content');
        $this->repository->save($page);
        
        $found = $this->repository->findBySlug('test');
        
        $this->assertNotNull($found);
        $this->assertEquals('Test Page', $found->getTitle());
    }
}
```

## Best Practices

### 1. Module Separation

- Keep each module's data in separate database
- Use clear naming conventions for tables
- Avoid cross-module database queries

### 2. Migration Management

- Use descriptive migration names with timestamps
- Keep migrations small and focused
- Test migrations on copy of production data

### 3. Repository Pattern

- Implement repository interfaces in domain layer
- Keep database logic in infrastructure layer
- Use query builder for complex queries

### 4. Error Handling

```php
try {
    $this->repository->save($page);
} catch (PDOException $e) {
    // Log error and handle gracefully
    $this->logger->error('Database error: ' . $e->getMessage());
    throw new RepositoryException('Failed to save page', 0, $e);
}
```

### 5. Transaction Management

```php
$this->pdo->beginTransaction();
try {
    $this->repository->save($page);
    $this->auditRepository->logChange($page);
    $this->pdo->commit();
} catch (Exception $e) {
    $this->pdo->rollBack();
    throw $e;
}
```

## Troubleshooting

### Common Issues

**Database file permissions:**
```bash
chmod 644 var/db/*.sqlite
chmod 755 var/db/
```

**Migration errors:**
```bash
# Check migration status
php bin/migrate.php --status page

# Reset migrations (development only)
rm var/db/page.sqlite
php bin/migrate.php page
```

**Connection issues:**
```php
// Check if database file exists
if (!file_exists('var/db/page.sqlite')) {
    $factory->createModuleDatabase('page');
}
```

## Next Steps

- [Domain Layer](domain.md) - Domain-Driven Design patterns
- [Development](development.md) - Development workflow
- [Testing](testing.md) - Testing strategies
- [Deployment](deployment.md) - Production deployment
