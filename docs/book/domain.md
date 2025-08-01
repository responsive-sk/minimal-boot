# Domain Layer

The Domain Layer is the heart of your application, containing the core business logic, entities, and rules. Minimal Boot implements Domain-Driven Design (DDD) principles to create maintainable and testable applications.

## Domain-Driven Design Principles

### Core Concepts

- **Entities** - Objects with identity that persist over time
- **Value Objects** - Immutable objects defined by their attributes
- **Domain Services** - Business logic that doesn't belong to entities
- **Repositories** - Abstractions for data access
- **Aggregates** - Clusters of related entities and value objects

### Benefits

- **Business Logic Isolation** - Core logic separated from infrastructure
- **Testability** - Easy to unit test business rules
- **Maintainability** - Clear separation of concerns
- **Flexibility** - Easy to change infrastructure without affecting business logic

## Domain Structure

```
Domain/
├── Entity/           # Domain entities with identity
├── ValueObject/      # Immutable value objects
├── Service/          # Domain services with business logic
├── Repository/       # Repository interfaces
├── Exception/        # Domain-specific exceptions
└── Event/           # Domain events (optional)
```

## Entities

Entities are objects with identity that persist over time.

### Creating Entities

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\Entity;

use DateTimeImmutable;
use InvalidArgumentException;

class Post
{
    private function __construct(
        private readonly string $id,
        private readonly string $slug,
        private readonly string $title,
        private readonly string $content,
        private readonly DateTimeImmutable $createdAt,
        private readonly bool $isPublished = false,
        private readonly ?DateTimeImmutable $publishedAt = null
    ) {
        $this->validateSlug($slug);
        $this->validateTitle($title);
    }

    public static function create(
        string $id,
        string $slug,
        string $title,
        string $content
    ): self {
        return new self(
            $id,
            $slug,
            $title,
            $content,
            new DateTimeImmutable(),
            false,
            null
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function publish(): self
    {
        if ($this->isPublished) {
            throw new InvalidArgumentException('Post is already published');
        }

        return new self(
            $this->id,
            $this->slug,
            $this->title,
            $this->content,
            $this->createdAt,
            true,
            new DateTimeImmutable()
        );
    }

    public function unpublish(): self
    {
        if (!$this->isPublished) {
            throw new InvalidArgumentException('Post is not published');
        }

        return new self(
            $this->id,
            $this->slug,
            $this->title,
            $this->content,
            $this->createdAt,
            false,
            null
        );
    }

    public function updateContent(string $content): self
    {
        if (empty(trim($content))) {
            throw new InvalidArgumentException('Content cannot be empty');
        }

        return new self(
            $this->id,
            $this->slug,
            $this->title,
            $content,
            $this->createdAt,
            $this->isPublished,
            $this->publishedAt
        );
    }

    private function validateSlug(string $slug): void
    {
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new InvalidArgumentException(
                'Slug must contain only lowercase letters, numbers, and hyphens'
            );
        }

        if (strlen($slug) < 3 || strlen($slug) > 100) {
            throw new InvalidArgumentException(
                'Slug must be between 3 and 100 characters'
            );
        }
    }

    private function validateTitle(string $title): void
    {
        if (empty(trim($title))) {
            throw new InvalidArgumentException('Title cannot be empty');
        }

        if (strlen($title) > 200) {
            throw new InvalidArgumentException(
                'Title cannot be longer than 200 characters'
            );
        }
    }
}
```

### Entity Best Practices

1. **Immutability** - Entities should be immutable when possible
2. **Business Logic** - Include business rules and validation
3. **Factory Methods** - Use static factory methods for creation
4. **Value Objects** - Use value objects for complex attributes
5. **Encapsulation** - Keep internal state private

## Value Objects

Value objects are immutable objects defined by their attributes.

### Creating Value Objects

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\ValueObject;

use InvalidArgumentException;

class Email
{
    private function __construct(
        private readonly string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDomain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }

    public function getLocalPart(): string
    {
        return substr($this->value, 0, strpos($this->value, '@'));
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address: {$email}");
        }
    }
}
```

### Money Value Object

```php
<?php

declare(strict_types=1);

namespace Minimal\Shop\Domain\ValueObject;

use InvalidArgumentException;

class Money
{
    private function __construct(
        private readonly int $amount, // Amount in cents
        private readonly string $currency
    ) {
        $this->validateAmount($amount);
        $this->validateCurrency($currency);
    }

    public static function fromCents(int $cents, string $currency): self
    {
        return new self($cents, $currency);
    }

    public static function fromFloat(float $amount, string $currency): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function add(Money $other): self
    {
        $this->ensureSameCurrency($other);
        
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->ensureSameCurrency($other);
        
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(float $multiplier): self
    {
        return new self((int) round($this->amount * $multiplier), $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function isGreaterThan(Money $other): bool
    {
        $this->ensureSameCurrency($other);
        
        return $this->amount > $other->amount;
    }

    private function ensureSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Cannot operate on different currencies: {$this->currency} and {$other->currency}"
            );
        }
    }

    private function validateAmount(int $amount): void
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
    }

    private function validateCurrency(string $currency): void
    {
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new InvalidArgumentException('Currency must be a 3-letter ISO code');
        }
    }
}
```

## Domain Services

Domain services contain business logic that doesn't naturally belong to entities.

### Creating Domain Services

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\Service;

use Minimal\Blog\Domain\Entity\Post;
use Minimal\Blog\Domain\Repository\PostRepositoryInterface;
use Minimal\Blog\Domain\Exception\DuplicateSlugException;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {
    }

    public function createPost(
        string $slug,
        string $title,
        string $content
    ): Post {
        // Business rule: slug must be unique
        if ($this->postRepository->existsBySlug($slug)) {
            throw new DuplicateSlugException("Post with slug '{$slug}' already exists");
        }

        $post = Post::create(
            $this->generateId(),
            $slug,
            $title,
            $content
        );

        $this->postRepository->save($post);

        return $post;
    }

    public function publishPost(string $id): Post
    {
        $post = $this->postRepository->findById($id);
        
        if ($post === null) {
            throw new PostNotFoundException("Post with ID '{$id}' not found");
        }

        $publishedPost = $post->publish();
        $this->postRepository->save($publishedPost);

        return $publishedPost;
    }

    public function getPublishedPosts(): array
    {
        return $this->postRepository->findPublished();
    }

    public function getPostBySlug(string $slug): ?Post
    {
        $post = $this->postRepository->findBySlug($slug);
        
        // Business rule: only return published posts
        return $post && $post->isPublished() ? $post : null;
    }

    private function generateId(): string
    {
        return uniqid('post_', true);
    }
}
```

## Repository Pattern

Repositories abstract data access and provide a domain-oriented interface.

### Repository Interface

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\Repository;

use Minimal\Blog\Domain\Entity\Post;

interface PostRepositoryInterface
{
    public function findById(string $id): ?Post;
    
    public function findBySlug(string $slug): ?Post;
    
    /**
     * @return array<Post>
     */
    public function findPublished(): array;
    
    /**
     * @return array<Post>
     */
    public function findByAuthor(string $authorId): array;
    
    public function save(Post $post): void;
    
    public function delete(string $id): void;
    
    public function existsBySlug(string $slug): bool;
    
    /**
     * @return array<Post>
     */
    public function findRecent(int $limit = 10): array;
}
```

### In-Memory Repository Implementation

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Infrastructure\Repository;

use Minimal\Blog\Domain\Entity\Post;
use Minimal\Blog\Domain\Repository\PostRepositoryInterface;

class InMemoryPostRepository implements PostRepositoryInterface
{
    /** @var array<string, Post> */
    private array $posts = [];

    public function findById(string $id): ?Post
    {
        return $this->posts[$id] ?? null;
    }

    public function findBySlug(string $slug): ?Post
    {
        foreach ($this->posts as $post) {
            if ($post->getSlug() === $slug) {
                return $post;
            }
        }
        
        return null;
    }

    public function findPublished(): array
    {
        return array_filter($this->posts, fn(Post $post) => $post->isPublished());
    }

    public function findByAuthor(string $authorId): array
    {
        // Implementation depends on Post entity having author
        return [];
    }

    public function save(Post $post): void
    {
        $this->posts[$post->getId()] = $post;
    }

    public function delete(string $id): void
    {
        unset($this->posts[$id]);
    }

    public function existsBySlug(string $slug): bool
    {
        return $this->findBySlug($slug) !== null;
    }

    public function findRecent(int $limit = 10): array
    {
        $posts = array_values($this->posts);
        
        // Sort by creation date (newest first)
        usort($posts, fn(Post $a, Post $b) => $b->getCreatedAt() <=> $a->getCreatedAt());
        
        return array_slice($posts, 0, $limit);
    }
}
```

## Domain Exceptions

Create specific exceptions for domain errors.

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\Exception;

use DomainException;

class DuplicateSlugException extends DomainException
{
    public static function forSlug(string $slug): self
    {
        return new self("Post with slug '{$slug}' already exists");
    }
}

class PostNotFoundException extends DomainException
{
    public static function forId(string $id): self
    {
        return new self("Post with ID '{$id}' not found");
    }

    public static function forSlug(string $slug): self
    {
        return new self("Post with slug '{$slug}' not found");
    }
}
```

## Testing Domain Layer

### Unit Testing Entities

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Test\Domain\Entity;

use Minimal\Blog\Domain\Entity\Post;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class PostTest extends TestCase
{
    public function testCreatePost(): void
    {
        $post = Post::create('1', 'test-post', 'Test Post', 'Content');
        
        $this->assertEquals('1', $post->getId());
        $this->assertEquals('test-post', $post->getSlug());
        $this->assertEquals('Test Post', $post->getTitle());
        $this->assertEquals('Content', $post->getContent());
        $this->assertFalse($post->isPublished());
    }

    public function testPublishPost(): void
    {
        $post = Post::create('1', 'test-post', 'Test Post', 'Content');
        $publishedPost = $post->publish();
        
        $this->assertTrue($publishedPost->isPublished());
        $this->assertNotNull($publishedPost->getPublishedAt());
    }

    public function testInvalidSlugThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        Post::create('1', 'Invalid Slug!', 'Test Post', 'Content');
    }
}
```

### Unit Testing Domain Services

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Test\Domain\Service;

use Minimal\Blog\Domain\Service\PostService;
use Minimal\Blog\Domain\Repository\PostRepositoryInterface;
use Minimal\Blog\Domain\Exception\DuplicateSlugException;
use PHPUnit\Framework\TestCase;

class PostServiceTest extends TestCase
{
    public function testCreatePost(): void
    {
        $repository = $this->createMock(PostRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('existsBySlug')
            ->with('test-post')
            ->willReturn(false);
        
        $repository->expects($this->once())
            ->method('save');
        
        $service = new PostService($repository);
        $post = $service->createPost('test-post', 'Test Post', 'Content');
        
        $this->assertEquals('test-post', $post->getSlug());
    }

    public function testCreatePostWithDuplicateSlugThrowsException(): void
    {
        $repository = $this->createMock(PostRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('existsBySlug')
            ->with('existing-post')
            ->willReturn(true);
        
        $service = new PostService($repository);
        
        $this->expectException(DuplicateSlugException::class);
        $service->createPost('existing-post', 'Test Post', 'Content');
    }
}
```

## Best Practices

### Entity Design

1. **Immutability** - Make entities immutable when possible
2. **Business Logic** - Include validation and business rules
3. **Factory Methods** - Use static factory methods for creation
4. **Encapsulation** - Keep internal state private

### Value Object Design

1. **Immutability** - Value objects should always be immutable
2. **Equality** - Implement proper equality comparison
3. **Validation** - Validate state in constructor
4. **Self-Documenting** - Use descriptive method names

### Domain Service Design

1. **Stateless** - Domain services should be stateless
2. **Single Responsibility** - Each service should have one clear purpose
3. **Interface Segregation** - Use specific interfaces
4. **Dependency Injection** - Inject dependencies through constructor

### Repository Design

1. **Domain-Oriented** - Design interfaces from domain perspective
2. **Collection-Like** - Think of repositories as collections
3. **Query Methods** - Provide specific query methods
4. **Abstraction** - Hide infrastructure details

## Next Steps

- [Configuration](configuration.md) - Framework configuration
- [Development](development.md) - Development workflow
- [Testing](testing.md) - Testing strategies
- [Deployment](deployment.md) - Production deployment
