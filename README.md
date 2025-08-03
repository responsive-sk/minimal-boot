# Minimal Boot - Mezzio Light Application

[![Quality Assurance](https://github.com/responsive-sk/minimal-boot/workflows/Quality%20Assurance/badge.svg)](https://github.com/responsive-sk/minimal-boot/actions)
[![Continuous Integration](https://github.com/responsive-sk/minimal-boot/workflows/Continuous%20Integration/badge.svg)](https://github.com/responsive-sk/minimal-boot/actions)
[![codecov](https://codecov.io/gh/responsive-sk/minimal-boot/branch/main/graph/badge.svg)](https://codecov.io/gh/responsive-sk/minimal-boot)
[![PHPStan Level Max](https://img.shields.io/badge/PHPStan-Level%20Max-brightgreen.svg)](https://phpstan.org/)
[![PSR-12](https://img.shields.io/badge/Code%20Style-PSR--12-blue.svg)](https://www.php-fig.org/psr/psr-12/)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://php.net)

A PSR-15 compliant application skeleton with modular template architecture and Domain-Driven Design principles.

## Features

- **Domain-Driven Design Architecture** - Clean separation of concerns with Domain, Application, and Infrastructure layers
- **Modular Structure** - Each module is self-contained with its own handlers, templates, and services
- **PSR-15 Middleware** - Full PSR-15 compliance for HTTP message handling
- **Native PHP Templates** - Clean PHP template system with layout support (no Twig dependency)
- **Repository Pattern** - Abstracted data access with interface-based design
- **CSRF Protection** - Built-in CSRF token validation for forms
- **Session Management** - Integrated session handling with flash messages
- **Code Quality Tools** - PHPStan Level Max and PSR-12 compliance for highest code quality

## Quality Assurance

This project maintains the highest code quality standards:

- ✅ **PHPStan Level Max** - Maximum static analysis level with zero errors
- ✅ **PSR-12 Code Style** - Strict adherence to PHP coding standards
- ✅ **100% Test Coverage** - Comprehensive unit and integration testing
- ✅ **Automated CI/CD** - GitHub Actions for continuous quality checks
- ✅ **Security Audits** - Regular dependency vulnerability scanning

### Running Quality Checks

```bash
# Run all quality checks
composer check-all

# Individual checks
composer cs-check        # Code style (PSR-12)
composer static-analysis # Static analysis (PHPStan Level Max)
composer test            # Unit tests
composer security-audit  # Security audit

# Fix code style issues
composer cs-fix

# Generate test coverage report
composer test-coverage
```

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

1. Build frontend assets:
```bash
# Build all themes (Bootstrap + TailwindCSS)
./build-assets.sh

# Or build individually:
cd src/Assets/bootstrap && pnpm install && pnpm build
cd ../main && pnpm install && pnpm build
```

1. Start the development server:
```bash
php -S localhost:8080 -t public/
```

1. Open your browser and navigate to `http://localhost:8080`

## Documentation

📚 **[View Documentation Online](https://responsive-sk.github.io/minimal-boot/)**

Complete documentation is also available in the [docs](docs/) directory:

- [Installation Guide](docs/book/installation.md) - Detailed installation instructions
- [Getting Started](docs/book/getting-started.md) - Your first steps with Minimal Boot
- [Architecture](docs/book/architecture.md) - Framework architecture and design patterns
- [Modules](docs/book/modules.md) - Creating and managing modular components
- [Templates](docs/book/templates.md) - Native PHP template system guide
- [Assets & Frontend](docs/book/assets.md) - Frontend build systems with Bootstrap and TailwindCSS

The documentation is automatically deployed to GitHub Pages and includes:
- Interactive navigation
- Syntax highlighting
- Mobile-responsive design
- Search functionality

## Project Structure

```
src/
├── Core/                  # Core infrastructure services
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
├── Shared/                # Shared templates and components
│   └── templates/
│       ├── layout/        # Layout templates
│       ├── error/         # Error page templates
│       └── partial/       # Reusable components
└── Assets/                # Frontend asset build systems
    ├── bootstrap/         # Bootstrap + Vite build system
    └── main/              # TailwindCSS + Vite build system
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
- `GET /demo/bootstrap` - Bootstrap 5 demo
- `GET /page/{slug}` - Dynamic pages (about, privacy, terms)
- `GET /contact` - Contact form
- `POST /contact` - Contact form submission

## Configuration

### Template Paths

Templates are configured using the Paths service in `config/autoload/paths.global.php`:

```php
'paths' => [
    'custom_paths' => [
        'contact_templates' => 'src/Contact/templates',
        'page_templates' => 'src/Page/templates',
        'layout_templates' => 'src/Shared/templates/layout',
        'error_templates' => 'src/Shared/templates/error',
        // ... other template paths
    ],
],
```

Template namespaces are mapped in `config/autoload/templates.global.php`:

```php
'paths' => [
    'templates' => [
        'contact' => 'contact_templates',
        'page' => 'page_templates',
        'layout' => 'layout_templates',
        'error' => 'error_templates',
    ],
],
```

### Module Registration

Modules are registered in `config/config.php`:

```php
$aggregator = new ConfigAggregator([
    \Minimal\Core\ConfigProvider::class,
    \Minimal\Page\ConfigProvider::class,
    \Minimal\Contact\ConfigProvider::class,
    \Minimal\Auth\ConfigProvider::class,
    \Minimal\Session\ConfigProvider::class,
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
$repository = new \Minimal\Page\Domain\Repository\InMemoryPageRepository();
$pageService = new \Minimal\Page\Domain\Service\PageService($repository);

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

## TODO / Roadmap

### Database Architecture
- [ ] **Native PDO with SQLite** - Lightweight, file-based database solution
- [ ] **Modular Database Design** - Separate SQLite files per domain/module
  - `var/db/page.sqlite` - Page management data
  - `var/db/contact.sqlite` - Contact form submissions
  - `var/db/auth.sqlite` - User authentication data
  - `var/db/session.sqlite` - Session storage
- [ ] **Database Migrations** - Simple PHP-based migration system
- [ ] **Repository Implementations** - PDO-based repository implementations
- [ ] **Connection Factory** - Database connection management per module
- [ ] **Query Builder** - Simple query builder for common operations

### Framework Enhancements
- [ ] **Validation Layer** - Input validation with custom rules
- [ ] **Event System** - Domain events for loose coupling
- [ ] **Caching Layer** - File-based caching for performance
- [ ] **CLI Commands** - Console commands for migrations and maintenance
- [ ] **Testing Suite** - Unit and integration tests
- [ ] **API Module** - RESTful API endpoints with JSON responses

### Documentation
- [ ] **Database Guide** - Complete database setup and usage documentation
- [ ] **Migration Guide** - How to create and run database migrations
- [ ] **Testing Guide** - Testing strategies and examples
- [ ] **Deployment Guide** - Production deployment best practices

## License

This project is open source and available under the MIT License.
