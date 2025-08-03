---
layout: page
title: "Core Module Documentation"
description: "Essential infrastructure services for the Minimal Boot framework"
---

# Core Module Documentation

Welcome to the Core module documentation. The Core module provides essential infrastructure services that power the entire application.

## Quick Navigation

- **[Overview](README.md)** - Complete Core module overview and architecture
- **[Database Layer](database.md)** - Database connections, migrations, and query building
- **[Template System](templates.md)** - Native PHP templating with layouts and namespaces
- **[Compatibility Layer](compatibility.md)** - Shared hosting environment support

## What's in Core?

The Core module is the foundation of the application, providing:

### Database Infrastructure
- **Modular SQLite databases** - Each domain gets its own database
- **Migration system** - Version-controlled schema changes
- **Query builder** - Fluent interface for SQL operations
- **Connection pooling** - Efficient database connection management

### Template Engine
- **Native PHP templates** - No external dependencies
- **Namespace support** - Organized template structure
- **Layout system** - Consistent page layouts
- **Automatic escaping** - XSS protection built-in

### Compatibility Features
- **Function detection** - Check for disabled PHP functions
- **Safe operations** - Fallbacks for restricted environments
- **Shared hosting ready** - Works on limited hosting providers

### Service Infrastructure
- **Dependency injection** - PSR-11 compliant container integration
- **Factory pattern** - Clean object creation
- **Configuration management** - Centralized settings

## Getting Started

### Basic Setup

```php
// 1. Database connection
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;

$factory = new DatabaseConnectionFactory('var/db');
$userDb = $factory->getConnection('user');

// 2. Template rendering
use Minimal\Core\Template\NativePhpRenderer;

$renderer = new NativePhpRenderer();
$renderer->addPath('user', 'templates/user');
$html = $renderer->render('user::profile', ['user' => $user]);

// 3. Compatibility checking
use Minimal\Core\Compatibility\FunctionChecker;

if (FunctionChecker::isAvailable('exec')) {
    exec('git --version', $output);
}
```

### Configuration

```php
// config/autoload/core.global.php
return [
    'database' => [
        'path' => 'var/db'
    ],
    'templates' => [
        'extension' => 'phtml',
        'layout' => 'layout::main'
    ],
    'paths' => [
        'templates' => [
            'layout' => 'templates/layout',
            'user' => 'templates/user',
            'page' => 'templates/page'
        ]
    ]
];
```

## Architecture Principles

### Domain-Driven Design
- **Modular structure** - Each domain is independent
- **Clear boundaries** - Well-defined interfaces between modules
- **Shared kernel** - Core provides common infrastructure

### PSR Compliance
- **PSR-11** - Container interface for dependency injection
- **PSR-15** - HTTP server request handlers
- **PSR-12** - Coding style standards

### Design Patterns
- **Factory Pattern** - Object creation through factories
- **Strategy Pattern** - Pluggable template path providers
- **Builder Pattern** - Fluent query building interface

## Key Features

### Database Layer Features
- **Per-module databases** - Isolation and independence
- **Automatic migrations** - Schema version control
- **Query builder** - Type-safe SQL building
- **Connection pooling** - Performance optimization

### Template System Features
- **Pure PHP** - No learning curve, full PHP power
- **Namespace organization** - Clean template structure
- **Layout inheritance** - Consistent page structure
- **Security by default** - Automatic output escaping

### Compatibility Features
- **Environment detection** - Shared vs VPS vs dedicated hosting
- **Function availability** - Check for disabled PHP functions
- **Safe fallbacks** - Alternative approaches when functions are disabled
- **Graceful degradation** - Features adapt to environment limitations

## Common Use Cases

### Database Operations
```php
// Simple CRUD operations
$users = $builder->table('users')
    ->where('status', '=', 'active')
    ->orderBy('created_at', 'DESC')
    ->get();

$userId = $builder->table('users')
    ->insert(['email' => 'user@example.com']);

$builder->table('users')
    ->where('id', '=', $userId)
    ->update(['last_login' => date('Y-m-d H:i:s')]);
```

### Template Rendering
```php
// Render page with layout
$html = $renderer->render('user::profile', [
    'user' => $user,
    'title' => 'User Profile'
]);

// Render partial without layout
$html = $renderer->render('user::partials/user-card', [
    'user' => $user
]);
```

### Environment Adaptation
```php
// Adapt features based on environment
if (FunctionChecker::hasExecCapability()) {
    // Use git commands for deployment
    $version = FunctionChecker::safeExec('git rev-parse HEAD');
} else {
    // Use file-based version tracking
    $version = file_get_contents('VERSION');
}
```

## Performance Considerations

### Database Performance
- **Connection reuse** - Connections are pooled and shared
- **Prepared statements** - Query builder uses parameter binding
- **Indexes** - Migration system supports index creation
- **WAL mode** - SQLite configured for better concurrency

### Template Performance
- **Native PHP** - No compilation overhead
- **Minimal abstraction** - Direct PHP execution
- **Efficient escaping** - Fast output sanitization
- **Layout caching** - Layouts can be cached when needed

### Compatibility Performance
- **One-time detection** - Function availability cached after first check
- **Minimal overhead** - Checks only when needed
- **Efficient fallbacks** - Alternative methods are optimized

## Security Features

### Database Security
- **Parameter binding** - All queries use prepared statements
- **Input validation** - Type checking in query builder
- **Connection isolation** - Per-module database separation

### Template Security
- **Automatic escaping** - Output is escaped by default
- **Context-aware escaping** - Different escaping for HTML, attributes, URLs
- **XSS prevention** - Built-in protection against cross-site scripting

### File Security
- **Safe operations** - Permission checking before file operations
- **Path validation** - Prevent directory traversal attacks
- **Error handling** - Secure error messages

## Testing Support

### Database Testing
```php
// Use in-memory database for tests
$factory = new DatabaseConnectionFactory(':memory:');
$testDb = $factory->getConnection('test');
```

### Template Testing
```php
// Test template rendering
$html = $renderer->render('test::template', $data);
$this->assertStringContainsString('expected content', $html);
```

### Compatibility Testing
```php
// Test environment capabilities
$this->assertTrue(FunctionChecker::isAvailable('strlen'));
$this->assertIsBool(FunctionChecker::hasExecCapability());
```

## Migration Guide

### From Other Template Engines
- **Twig users** - Similar namespace concept, but pure PHP syntax
- **Smarty users** - More direct PHP integration, less abstraction
- **Blade users** - Similar layout inheritance, different syntax

### From Other Database Layers
- **Doctrine users** - Simpler approach, less features but easier setup
- **Eloquent users** - Similar query builder pattern, different syntax
- **PDO users** - Higher-level abstraction with same underlying technology

## Troubleshooting

### Common Issues

**Database connection errors:**
```php
// Check database directory permissions
if (!is_writable('var/db')) {
    throw new RuntimeException('Database directory not writable');
}
```

**Template not found errors:**
```php
// Verify template paths
$paths = $renderer->getPaths();
var_dump($paths); // Check registered namespaces
```

**Function disabled errors:**
```php
// Check what functions are available
$disabled = FunctionChecker::getDisabledFunctions();
error_log('Disabled functions: ' . implode(', ', $disabled));
```

## Contributing

When contributing to the Core module:

1. **Follow PSR standards** - Code style and interfaces
2. **Add tests** - All new features need test coverage
3. **Update documentation** - Keep docs current with changes
4. **Consider compatibility** - Ensure shared hosting compatibility
5. **Performance impact** - Core changes affect entire application

## Support

For questions about the Core module:

1. **Check documentation** - Most common questions are covered
2. **Review examples** - Look at existing usage patterns
3. **Test in isolation** - Create minimal reproduction cases
4. **Check compatibility** - Verify hosting environment capabilities

The Core module is designed to be stable, reliable, and compatible across different hosting environments while providing the essential infrastructure your application needs.
