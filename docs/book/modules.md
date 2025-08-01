# Modules

Minimal Boot uses a modular architecture where each module is a self-contained unit with its own handlers, templates, services, and configuration.

## Module Structure

A typical module follows this structure:

```
src/ModuleName/
├── Domain/                 # Domain layer (optional)
│   ├── Entity/            # Domain entities
│   ├── Repository/        # Repository interfaces
│   ├── Service/           # Domain services
│   └── Exception/         # Domain exceptions
├── Handler/               # HTTP request handlers
├── Factory/               # Dependency injection factories
├── templates/             # View templates
├── ConfigProvider.php    # Module configuration
└── RoutesDelegator.php    # Route definitions
```

## Creating a New Module

### Step 1: Create Module Directory

```bash
mkdir -p src/Blog/{Handler,Factory,templates,Domain/{Entity,Repository,Service}}
```

### Step 2: Create ConfigProvider

Create `src/Blog/ConfigProvider.php`:

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog;

use Minimal\Blog\Factory\BlogHandlerFactory;
use Minimal\Blog\Handler\BlogHandler;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                BlogHandler::class => BlogHandlerFactory::class,
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'blog' => [__DIR__ . '/templates'],
            ],
        ];
    }
}
```

### Step 3: Create RoutesDelegator

Create `src/Blog/RoutesDelegator.php`:

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog;

use Minimal\Blog\Handler\BlogHandler;
use Mezzio\Application;
use Psr\Container\ContainerInterface;

class RoutesDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ): Application {
        $app = $callback();
        assert($app instanceof Application);

        // Blog routes
        $app->get('/blog', [BlogHandler::class], 'blog::index');
        $app->get('/blog/{slug}', [BlogHandler::class], 'blog::view');

        return $app;
    }
}
```

### Step 4: Register Module

Add to `config/config.php`:

```php
$aggregator = new ConfigAggregator([
    // ... existing providers
    \Minimal\Blog\ConfigProvider::class,
    
    // ... rest of configuration
]);
```

Add routes delegator to `config/autoload/routes.global.php`:

```php
return [
    'dependencies' => [
        'delegators' => [
            \Mezzio\Application::class => [
                // ... existing delegators
                \Minimal\Blog\RoutesDelegator::class,
            ],
        ],
    ],
];
```

## Module Components

### Handlers

Handlers process HTTP requests:

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BlogHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('blog::index', [
                'posts' => $this->getBlogPosts(),
            ])
        );
    }

    private function getBlogPosts(): array
    {
        // Fetch blog posts
        return [];
    }
}
```

### Factories

Factories create and configure objects:

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Factory;

use Minimal\Blog\Handler\BlogHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class BlogHandlerFactory
{
    public function __invoke(ContainerInterface $container): BlogHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new BlogHandler($template);
    }
}
```

### Templates

Create `src/Blog/templates/index.phtml`:

```php
<?php
$layout('layout::default', [
    'title' => 'Blog',
    'description' => 'Latest blog posts',
]);
?>

<div class="container mt-5">
    <h1>Blog Posts</h1>
    
    <?php if (empty($posts)): ?>
        <p>No blog posts found.</p>
    <?php else: ?>
        <div class="row">
            <?php foreach ($posts as $post): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $this->escapeHtml($post['title']) ?></h5>
                            <p class="card-text"><?= $this->escapeHtml($post['excerpt']) ?></p>
                            <a href="/blog/<?= $this->escapeHtmlAttr($post['slug']) ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
```

## Domain Layer

For complex modules, implement a domain layer:

### Domain Entity

Create `src/Blog/Domain/Entity/Post.php`:

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\Entity;

use DateTimeImmutable;

class Post
{
    public function __construct(
        private readonly string $id,
        private readonly string $slug,
        private readonly string $title,
        private readonly string $content,
        private readonly string $excerpt,
        private readonly DateTimeImmutable $publishedAt,
        private readonly bool $isPublished = false
    ) {
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

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function isPublished(): bool
    {
        return $this->isPublished && $this->publishedAt <= new DateTimeImmutable();
    }

    public function publish(): self
    {
        return new self(
            $this->id,
            $this->slug,
            $this->title,
            $this->content,
            $this->excerpt,
            $this->publishedAt,
            true
        );
    }
}
```

### Repository Interface

Create `src/Blog/Domain/Repository/PostRepositoryInterface.php`:

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\Repository;

use Minimal\Blog\Domain\Entity\Post;

interface PostRepositoryInterface
{
    public function findBySlug(string $slug): ?Post;
    
    /**
     * @return array<Post>
     */
    public function findPublished(): array;
    
    public function save(Post $post): void;
    
    public function delete(string $id): void;
}
```

### Domain Service

Create `src/Blog/Domain/Service/PostService.php`:

```php
<?php

declare(strict_types=1);

namespace Minimal\Blog\Domain\Service;

use Minimal\Blog\Domain\Entity\Post;
use Minimal\Blog\Domain\Repository\PostRepositoryInterface;

class PostService
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository
    ) {
    }

    public function getPublishedPosts(): array
    {
        return $this->postRepository->findPublished();
    }

    public function getPostBySlug(string $slug): ?Post
    {
        $post = $this->postRepository->findBySlug($slug);
        
        return $post && $post->isPublished() ? $post : null;
    }

    public function createPost(
        string $slug,
        string $title,
        string $content,
        string $excerpt
    ): Post {
        // Business logic validation
        if ($this->postRepository->findBySlug($slug)) {
            throw new \InvalidArgumentException("Post with slug '{$slug}' already exists");
        }

        $post = new Post(
            uniqid(),
            $slug,
            $title,
            $content,
            $excerpt,
            new \DateTimeImmutable(),
            false
        );

        $this->postRepository->save($post);

        return $post;
    }
}
```

## Module Communication

### Shared Interfaces

Modules can share interfaces for loose coupling:

```php
namespace Minimal\Shared\Contract;

interface NotificationServiceInterface
{
    public function send(string $recipient, string $message): void;
}
```

### Events

Use events for decoupled communication:

```php
// In one module
$this->eventDispatcher->dispatch(new UserRegisteredEvent($user));

// In another module
class SendWelcomeEmailListener
{
    public function __invoke(UserRegisteredEvent $event): void
    {
        // Send welcome email
    }
}
```

## Module Configuration

### Environment-specific Configuration

Create module-specific configuration files:

```php
// config/autoload/blog.global.php
return [
    'blog' => [
        'posts_per_page' => 10,
        'cache_enabled' => false,
    ],
];

// config/autoload/blog.production.php
return [
    'blog' => [
        'cache_enabled' => true,
    ],
];
```

### Accessing Configuration

In your factories:

```php
class BlogServiceFactory
{
    public function __invoke(ContainerInterface $container): BlogService
    {
        $config = $container->get('config');
        $blogConfig = $config['blog'] ?? [];
        
        return new BlogService($blogConfig);
    }
}
```

## Testing Modules

### Unit Testing

Test domain logic:

```php
class PostServiceTest extends TestCase
{
    public function testCreatePost(): void
    {
        $repository = $this->createMock(PostRepositoryInterface::class);
        $service = new PostService($repository);
        
        $post = $service->createPost('test-post', 'Test Post', 'Content', 'Excerpt');
        
        $this->assertEquals('test-post', $post->getSlug());
    }
}
```

### Integration Testing

Test handlers:

```php
class BlogHandlerTest extends TestCase
{
    public function testBlogIndex(): void
    {
        $response = $this->get('/blog');
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Blog Posts', $response->getBody());
    }
}
```

## Best Practices

### Module Design

1. **Single Responsibility** - Each module should have one clear purpose
2. **Loose Coupling** - Minimize dependencies between modules
3. **High Cohesion** - Related functionality should be grouped together
4. **Interface Segregation** - Use specific interfaces rather than large ones

### Naming Conventions

- **Modules** - PascalCase (e.g., `Blog`, `UserManagement`)
- **Handlers** - End with `Handler` (e.g., `BlogHandler`)
- **Factories** - End with `Factory` (e.g., `BlogHandlerFactory`)
- **Services** - End with `Service` (e.g., `PostService`)

### File Organization

- Keep related files together
- Use consistent directory structure
- Separate concerns (Domain, Application, Infrastructure)

## Next Steps

- [Templates](templates.md) - Working with templates
- [Domain Layer](domain.md) - Domain-Driven Design
- [Configuration](configuration.md) - Advanced configuration
- [Development](development.md) - Development workflow
