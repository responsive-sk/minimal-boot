---
layout: page
title: "Getting Started"
description: "Learn the basics of Minimal Boot and create your first components"
nav_order: 2
---

# Getting Started

This guide will help you understand the basics of Minimal Boot and create your first application components.

## Project Structure

After installation, your project structure looks like this:

```
minimal-boot/
├── config/                 # Configuration files
│   ├── autoload/          # Auto-loaded configuration
│   └── config.php         # Main configuration aggregator
├── public/                # Web root directory
│   └── index.php          # Application entry point
├── src/                   # Application source code
│   ├── Core/              # Core infrastructure
│   ├── Page/              # Page management module
│   ├── Contact/           # Contact form module
│   ├── Auth/              # Authentication module
│   ├── Session/           # Session management
│   ├── Shared/            # Shared templates and components
│   └── Assets/            # Frontend asset build systems
├── var/                   # Variable data (cache, logs)
└── vendor/                # Composer dependencies
```

## Understanding Modules

Minimal Boot uses a modular architecture. Each module is self-contained with:

- **Handlers** - HTTP request handlers
- **Domain** - Business logic and entities (for complex modules)
- **Templates** - View templates
- **Factories** - Dependency injection factories
- **Configuration** - Module-specific configuration

### Module Structure Example

```
src/Page/
├── Domain/                # Domain layer (DDD)
│   ├── Entity/           # Domain entities
│   ├── Repository/       # Data access interfaces
│   └── Service/          # Business logic services
├── Handler/              # HTTP handlers
├── Factory/              # DI factories
├── templates/            # Templates
├── ConfigProvider.php   # Module configuration
└── RoutesDelegator.php   # Route definitions
```

## Creating Your First Page

Let's create a simple "Hello World" page:

### Step 1: Create a Handler

Create `src/Page/Handler/HelloHandler.php`:

```php
<?php

declare(strict_types=1);

namespace Minimal\Page\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HelloHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse(
            $this->template->render('page::hello', [
                'name' => $request->getQueryParams()['name'] ?? 'World'
            ])
        );
    }
}
```

### Step 2: Create a Factory

Create `src/Page/Factory/HelloHandlerFactory.php`:

```php
<?php

declare(strict_types=1);

namespace Minimal\Page\Factory;

use Minimal\Page\Handler\HelloHandler;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

use function assert;

class HelloHandlerFactory
{
    public function __invoke(ContainerInterface $container): HelloHandler
    {
        $template = $container->get(TemplateRendererInterface::class);
        assert($template instanceof TemplateRendererInterface);

        return new HelloHandler($template);
    }
}
```

### Step 3: Create a Template

Create `src/Page/templates/hello.phtml`:

```php
<?php
$layout('layout::default', [
    'title' => 'Hello Page',
    'description' => 'A simple hello world page',
]);
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h1 class="card-title">Hello, <?= $this->escapeHtml($name) ?>!</h1>
                    <p class="card-text">Welcome to Minimal Boot framework.</p>
                    <a href="/" class="btn btn-primary">Go Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Step 4: Register the Handler

Update `src/Page/ConfigProvider.php`:

```php
// Add to the use statements
use Minimal\Page\Factory\HelloHandlerFactory;
use Minimal\Page\Handler\HelloHandler;

// Add to the factories array
'factories' => [
    // ... existing factories
    HelloHandler::class => HelloHandlerFactory::class,
],
```

### Step 5: Add the Route

Update `src/Page/RoutesDelegator.php`:

```php
// Add to the use statements
use Minimal\Page\Handler\HelloHandler;

// Add the route in the __invoke method
$app->get('/hello', [HelloHandler::class], 'page::hello');
```

### Step 6: Test Your Page

1. Clear the configuration cache:
```bash
php bin/clear-config-cache.php
```

2. Visit `http://localhost:8080/hello` in your browser
3. Try with a name parameter: `http://localhost:8080/hello?name=John`

## Working with Domain Layer

For more complex business logic, use the Domain layer:

### Creating a Domain Entity

```php
<?php

declare(strict_types=1);

namespace Minimal\Page\Domain\Entity;

class Article
{
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $content,
        private readonly \DateTimeImmutable $publishedAt
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPublishedAt(): \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function isPublished(): bool
    {
        return $this->publishedAt <= new \DateTimeImmutable();
    }
}
```

### Creating a Repository Interface

```php
<?php

declare(strict_types=1);

namespace Minimal\Page\Domain\Repository;

use Minimal\Page\Domain\Entity\Article;

interface ArticleRepositoryInterface
{
    public function findById(string $id): ?Article;
    
    /**
     * @return array<Article>
     */
    public function findPublished(): array;
    
    public function save(Article $article): void;
}
```

## Template System

Minimal Boot uses native PHP templates with a layout system:

### Layout Usage

```php
<?php
// At the top of your template
$layout('layout::default', [
    'title' => 'Page Title',
    'description' => 'SEO description',
    'author' => 'Author Name'
]);
?>

<!-- Your content here -->
<h1><?= $this->escapeHtml($title) ?></h1>
```

### Available Layouts

- `layout::default` - Bootstrap layout with navigation
- `layout::tailwind` - TailwindCSS layout with Alpine.js
- `layout::bootstrap` - Pure Bootstrap layout

## Configuration

### Module Configuration

Each module has a `ConfigProvider.php` that defines:

- Dependencies (factories, aliases)
- Routes (via RoutesDelegator)
- Templates paths
- Module-specific configuration

### Global Configuration

Configuration files in `config/autoload/` are automatically loaded:

- `*.global.php` - Global configuration
- `*.local.php` - Local/environment-specific configuration
- `*.production.php` - Production-specific configuration

## Next Steps

- [Architecture](architecture.md) - Deep dive into the framework architecture
- [Modules](modules.md) - Advanced module development
- [Templates](templates.md) - Template system details
- [Domain Layer](domain.md) - Domain-Driven Design patterns
