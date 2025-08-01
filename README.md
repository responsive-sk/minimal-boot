# Minimal Boot - Mezzio Light Application

A PSR-15 compliant application skeleton with modular template architecture and Domain-Driven Design principles.

## Features

- **Domain-Driven Design Architecture** - Clean separation of concerns with Domain, Application, and Infrastructure layers
- **Modular Structure** - Each module is self-contained with its own handlers, templates, and services
- **PSR-15 Middleware** - Full PSR-15 compliance for HTTP message handling
- **Native PHP Templates** - Clean PHP template system with layout support (no Twig dependency)
- **Repository Pattern** - Abstracted data access with interface-based design
- **CSRF Protection** - Built-in CSRF token validation for forms
- **Session Management** - Integrated session handling with flash messages
- **Code Quality Tools** - PHPStan and PHP_CodeSniffer integration for high code quality

## Requirements

- PHP 8.1 or higher
- Composer 2.0 or higher
- Web server (Apache, Nginx, or PHP built-in server)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/responsive-sk/minimal-boot.git
cd minimal-boot
```

1. Install dependencies:
```bash
composer install
```

1. Start the development server:
```bash
php -S localhost:8080 -t public/
```

1. Open your browser and navigate to `http://localhost:8080`

## Project Structure

```
src/
├── Core/                   # Core infrastructure services
│   ├── Factory/           # Dependency injection factories
│   ├── Service/           # Core services (template paths, etc.)
│   └── Template/          # Native PHP template renderer
├── Page/                  # Page management module
│   ├── Domain/            # Domain layer (DDD)
│   │   ├── Entity/        # Domain entities
│   │   ├── Repository/    # Repository interfaces and implementations
│   │   └── Service/       # Domain services with business logic
│   ├── Handler/           # HTTP request handlers
│   ├── Factory/           # Module-specific factories
│   └── templates/         # Page templates
├── Contact/               # Contact form module
│   ├── Handler/           # Contact form handlers
│   ├── Factory/           # Contact factories
│   └── templates/         # Contact templates
├── Auth/                  # Authentication module
├── Session/               # Session management module
└── Shared/                # Shared templates and components
    └── templates/
        ├── layout/        # Layout templates
        └── error/         # Error page templates
```

## Architecture

### Domain-Driven Design

The application follows DDD principles with clear separation between:

- **Domain Layer** - Contains business logic, entities, and domain services
- **Application Layer** - HTTP handlers that orchestrate domain operations
- **Infrastructure Layer** - Factories, repositories, and external services

### Module Structure

Each module follows a consistent structure:

```
ModuleName/
├── Domain/                # Business logic (for complex modules)
│   ├── Entity/           # Domain entities
│   ├── Repository/       # Data access interfaces
│   └── Service/          # Business logic services
├── Handler/              # HTTP request handlers
├── Factory/              # Dependency injection factories
├── templates/            # Module-specific templates
├── ConfigProvider.php   # Module configuration
└── RoutesDelegator.php   # Route definitions
```

## Available Routes

- `GET /` - Homepage
- `GET /demo` - TailwindCSS + Alpine.js demo
- `GET /page/{slug}` - Dynamic pages (about, privacy, terms)
- `GET /contact` - Contact form
- `POST /contact` - Contact form submission

## Configuration

### Template Paths

Templates are configured in `config/autoload/templates.global.php`:

```php
'templates' => [
    'paths' => [
        'contact' => [getcwd() . '/src/Contact/templates'],
        'page' => [getcwd() . '/src/Page/templates'],
        'layout' => [getcwd() . '/src/Shared/templates/layout'],
        'error' => [getcwd() . '/src/Shared/templates/error'],
    ],
],
```

### Module Registration

Modules are registered in `config/config.php`:

```php
$aggregator = new ConfigAggregator([
    \Light\Core\ConfigProvider::class,
    \Light\Page\ConfigProvider::class,
    \Light\Contact\ConfigProvider::class,
    \Light\Auth\ConfigProvider::class,
    \Light\Session\ConfigProvider::class,
    // ...
]);
```

## Development

### Code Quality

Run code quality checks:

```bash
# PHPStan analysis
composer static-analysis

# Code style check
composer cs-check

# Code style fix
composer cs-fix
```

### Creating New Modules

Use the provided script to generate new modules:

```bash
./create-module.sh ModuleName
```

This creates a complete module structure with:
- ConfigProvider
- RoutesDelegator
- Sample handler and factory
- Template directory
- Proper PSR-4 autoloading

### Adding New Pages

To add new pages to the Page module:

1. Add page data to `InMemoryPageRepository::initializeDefaultPages()`
2. The page will be automatically available at `/page/{slug}`

Example:
```php
$this->pages['new-page'] = new Page(
    slug: 'new-page',
    title: 'New Page Title',
    content: '<h1>New Page</h1><p>Page content here.</p>',
    metaDescription: 'Description for SEO',
    metaKeywords: ['keyword1', 'keyword2'],
    isPublished: true,
    publishedAt: new DateTimeImmutable(),
    createdAt: new DateTimeImmutable(),
    updatedAt: new DateTimeImmutable()
);
```

## Template System

### Layout Usage

Templates use the layout system:

```php
<?php
$layout('layout::default', [
    'title' => 'Page Title',
    'description' => 'Page description for SEO',
    'author' => 'Author Name'
]);
?>

<div class="container">
    <h1>Page Content</h1>
    <p>Your content here...</p>
</div>
```

### Available Layouts

- `layout::default` - Bootstrap-based layout with navigation and dark mode toggle
- `layout::tailwind` - TailwindCSS-based layout with Alpine.js components
- `layout::bootstrap` - Pure Bootstrap layout for simple pages

### Template Features

- **Native PHP Templates** - No external template engine dependencies
- **Layout System** - Hierarchical template inheritance
- **Helper Functions** - URL generation, escaping, and utility functions
- **Component Support** - Reusable template components and partials

## Testing

The Domain layer is designed for easy testing:

```php
// Example: Testing PageService
$repository = new InMemoryPageRepository();
$pageService = new PageService($repository);

$page = $pageService->getPageBySlug('about');
$this->assertNotNull($page);
$this->assertEquals('About Us', $page->getTitle());
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run code quality checks
5. Submit a pull request

## Template System Details

The project uses a **Native PHP Template System** with the following advantages:

### Why Native PHP Templates?

- **No External Dependencies** - Eliminates Twig/Smarty dependencies
- **Better Performance** - Direct PHP execution without compilation overhead
- **Full PHP Power** - Access to all PHP functions and features
- **Easier Debugging** - Standard PHP debugging tools work seamlessly
- **Smaller Footprint** - Reduced vendor directory size and complexity

### Template Architecture

```php
// Layout usage in templates
<?php
$layout('layout::default', [
    'title' => 'Page Title',
    'description' => 'SEO description',
    'author' => 'Author Name'
]);
?>

<div class="content">
    <h1><?= $this->escapeHtml($title) ?></h1>
    <p><?= $content ?></p>
</div>
```

### Security Features

- **Automatic Escaping** - Built-in XSS protection
- **CSRF Protection** - Token validation for forms
- **Safe Includes** - Controlled template inclusion

## License

This project is open source and available under the MIT License.
