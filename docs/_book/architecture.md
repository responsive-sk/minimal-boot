---
layout: page
title: "Architecture"
description: "Framework architecture and Domain-Driven Design principles"
nav_order: 3
---

# Architecture

Minimal Boot follows Domain-Driven Design (DDD) principles and implements a clean, layered architecture that promotes separation of concerns and maintainability.

## Architectural Overview

The framework is built on three main layers:

```
┌─────────────────────────────────────┐
│           Presentation Layer        │
│     (HTTP Handlers, Templates)      │
├─────────────────────────────────────┤
│           Application Layer         │
│    (Use Cases, Application Logic)   │
├─────────────────────────────────────┤
│             Domain Layer            │
│   (Entities, Services, Repositories)│
├─────────────────────────────────────┤
│         Infrastructure Layer        │
│  (Database, External Services, DI)  │
└─────────────────────────────────────┘
```

## Layer Responsibilities

### 1. Presentation Layer

**Location:** `src/*/Handler/`, `src/*/templates/`

**Responsibilities:**
- Handle HTTP requests and responses
- Render templates and views
- Input validation and sanitization
- Route handling

**Components:**
- **Handlers** - PSR-15 request handlers
- **Templates** - Native PHP templates with layouts
- **Middleware** - Cross-cutting concerns

**Example:**
```php
class ContactHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Handle HTTP request, delegate to application layer
        return new HtmlResponse($this->template->render('contact::form'));
    }
}
```

### 2. Application Layer

**Location:** `src/*/Handler/` (orchestration logic)

**Responsibilities:**
- Orchestrate domain operations
- Handle application-specific logic
- Coordinate between domain and infrastructure
- Transaction management

**Example:**
```php
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $data = $request->getParsedBody();
    
    // Orchestrate domain operations
    $page = $this->pageService->createPage(
        $data['slug'],
        $data['title'],
        $data['content']
    );
    
    return new JsonResponse(['id' => $page->getId()]);
}
```

### 3. Domain Layer

**Location:** `src/*/Domain/`

**Responsibilities:**
- Core business logic
- Domain entities and value objects
- Domain services
- Business rules and invariants

**Structure:**
```
Domain/
├── Entity/           # Domain entities
├── Service/          # Domain services
├── Repository/       # Repository interfaces
└── Exception/        # Domain exceptions
```

**Example Entity:**
```php
class Page
{
    public function __construct(
        private readonly string $slug,
        private readonly string $title,
        private readonly string $content,
        private readonly bool $isPublished = false
    ) {
        $this->validateSlug($slug);
    }

    public function publish(): self
    {
        return new self($this->slug, $this->title, $this->content, true);
    }

    private function validateSlug(string $slug): void
    {
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            throw new InvalidArgumentException('Invalid slug format');
        }
    }
}
```

### 4. Infrastructure Layer

**Location:** `src/*/Factory/`, `config/`

**Responsibilities:**
- Dependency injection
- External service integration
- Database access implementations
- Configuration management

## Modular Architecture

### Module Structure

Each module is self-contained and follows this structure:

```
ModuleName/
├── Domain/              # Business logic (optional)
│   ├── Entity/         # Domain entities
│   ├── Repository/     # Repository interfaces
│   └── Service/        # Domain services
├── Handler/            # HTTP handlers
├── Factory/            # DI factories
├── templates/          # View templates
├── ConfigProvider.php # Module configuration
└── RoutesDelegator.php # Route definitions
```

### Module Independence

- **Self-contained** - Each module has its own dependencies
- **Loose coupling** - Modules communicate through interfaces
- **High cohesion** - Related functionality grouped together
- **Testable** - Easy to unit test in isolation

### Module Communication

Modules communicate through:

1. **Shared interfaces** in the Domain layer
2. **Events** for loose coupling
3. **Dependency injection** for service sharing

## Dependency Injection

### Container Configuration

Each module provides a `ConfigProvider` that defines:

```php
class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    PageService::class => PageServiceFactory::class,
                    PageRepositoryInterface::class => PageRepositoryFactory::class,
                ],
            ],
            'routes' => [
                // Route configuration
            ],
        ];
    }
}
```

### Factory Pattern

Factories create and configure objects:

```php
class PageServiceFactory
{
    public function __invoke(ContainerInterface $container): PageService
    {
        $repository = $container->get(PageRepositoryInterface::class);
        return new PageService($repository);
    }
}
```

## Request Flow

1. **HTTP Request** arrives at `public/index.php`
2. **Router** matches request to handler
3. **Middleware** processes request (authentication, CORS, etc.)
4. **Handler** receives request
5. **Application Layer** orchestrates business logic
6. **Domain Layer** executes business rules
7. **Infrastructure Layer** persists data
8. **Response** is generated and returned

```
HTTP Request
     ↓
   Router
     ↓
 Middleware
     ↓
  Handler (Presentation)
     ↓
Application Logic
     ↓
Domain Services
     ↓
Repository (Infrastructure)
     ↓
HTTP Response
```

## Design Patterns

### Repository Pattern

Abstracts data access:

```php
interface PageRepositoryInterface
{
    public function findBySlug(string $slug): ?Page;
    public function save(Page $page): void;
}

class InMemoryPageRepository implements PageRepositoryInterface
{
    public function findBySlug(string $slug): ?Page
    {
        return $this->pages[$slug] ?? null;
    }
}
```

### Factory Pattern

Creates complex objects:

```php
class PageFactory
{
    public static function createFromArray(array $data): Page
    {
        return new Page(
            $data['slug'],
            $data['title'],
            $data['content']
        );
    }
}
```

### Service Layer Pattern

Encapsulates business logic:

```php
class PageService
{
    public function createPage(string $slug, string $title, string $content): Page
    {
        // Business logic
        if ($this->repository->existsBySlug($slug)) {
            throw new DuplicateSlugException();
        }

        $page = new Page($slug, $title, $content);
        $this->repository->save($page);
        
        return $page;
    }
}
```

## Configuration Architecture

### Hierarchical Configuration

Configuration is loaded in order:

1. **Global** configuration (`*.global.php`)
2. **Local** configuration (`*.local.php`)
3. **Environment** configuration (`*.production.php`)

### Module Configuration

Each module contributes configuration:

```php
// Module ConfigProvider
return [
    'templates' => [
        'paths' => [
            'page' => [__DIR__ . '/templates'],
        ],
    ],
    'routes' => [
        // Module routes
    ],
];
```

## Testing Architecture

### Unit Testing

Test domain logic in isolation:

```php
class PageServiceTest extends TestCase
{
    public function testCreatePage(): void
    {
        $repository = $this->createMock(PageRepositoryInterface::class);
        $service = new PageService($repository);
        
        $page = $service->createPage('test', 'Test Page', 'Content');
        
        $this->assertEquals('test', $page->getSlug());
    }
}
```

### Integration Testing

Test module integration:

```php
class PageHandlerTest extends TestCase
{
    public function testGetPage(): void
    {
        $response = $this->get('/page/about');
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
```

## Performance Considerations

### Configuration Caching

Enable in production:

```php
'config_cache_enabled' => true,
```

### Template Optimization

- Native PHP templates (no compilation)
- Layout inheritance
- Component reuse

### Database Optimization

- Repository pattern for data access
- Query optimization
- Connection pooling

## Security Architecture

### Input Validation

- Request validation in handlers
- Domain validation in entities
- Template escaping

### CSRF Protection

Built-in CSRF token validation:

```php
$csrfToken = $this->csrfService->generateToken();
```

### Authentication

Modular authentication system:

```php
class AuthHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Authentication logic
    }
}
```

## Next Steps

- [Modules](modules.md) - Creating and managing modules
- [Domain Layer](domain.md) - Domain-Driven Design patterns
- [Templates](templates.md) - Template system details
- [Configuration](configuration.md) - Advanced configuration
