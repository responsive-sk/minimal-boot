# Core Module Documentation

The Core module provides essential infrastructure services for the entire application. It follows Domain-Driven Design principles and PSR standards compliance.

## Architecture Overview

The Core module is organized into several key layers:

- **Database Layer** - Connection management, migrations, query building
- **Template System** - Native PHP templating with namespace support
- **Service Layer** - Configuration and factory classes
- **Compatibility Layer** - Shared hosting environment support

## Components

### Database Layer

#### DatabaseConnectionFactory

Manages SQLite database connections with per-module separation.

```php
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;

$factory = new DatabaseConnectionFactory('var/db');

// Get connection for specific module
$userDb = $factory->getConnection('user');
$pageDb = $factory->getConnection('page');

// In-memory database for testing
$testFactory = new DatabaseConnectionFactory(':memory:');
$testDb = $testFactory->getConnection('test');
```

**Features:**
- Connection pooling for performance
- Per-module database isolation
- Automatic directory creation
- In-memory support for testing

#### MigrationRunner

Handles database schema migrations with tracking.

```php
use Minimal\Core\Database\Migration\MigrationRunner;

$runner = new MigrationRunner($connectionFactory, 'migrations');

// Run migrations for specific module
$newMigrations = $runner->runMigrations('user');

// Run all pending migrations
$allResults = $runner->runAllMigrations();

// Create new migration file
$migrationFile = $runner->createMigration('user', 'add_email_verification');
```

**Migration File Structure:**
```
migrations/
├── user/
│   ├── 001_create_users_table.php
│   ├── 002_add_email_verification.php
│   └── ...
└── page/
    ├── 001_create_pages_table.php
    └── ...
```

#### QueryBuilder

Fluent interface for building SQL queries.

```php
use Minimal\Core\Database\Query\QueryBuilder;

$builder = new QueryBuilder($pdo);

// SELECT with conditions
$users = $builder->table('users')
    ->select(['id', 'email', 'username'])
    ->where('status', '=', 'active')
    ->where('created_at', '>', '2024-01-01')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Single record
$user = $builder->table('users')
    ->where('id', '=', 123)
    ->first();

// INSERT
$userId = $builder->table('users')
    ->insert([
        'email' => 'user@example.com',
        'username' => 'johndoe',
        'password_hash' => password_hash('secret', PASSWORD_DEFAULT)
    ]);

// UPDATE
$affected = $builder->table('users')
    ->where('id', '=', 123)
    ->update(['last_login' => date('Y-m-d H:i:s')]);

// DELETE
$deleted = $builder->table('users')
    ->where('status', '=', 'inactive')
    ->delete();
```

### Template System

#### NativePhpRenderer

Pure PHP template renderer implementing Mezzio TemplateRendererInterface.

```php
use Minimal\Core\Template\NativePhpRenderer;

$renderer = new NativePhpRenderer();

// Add template paths
$renderer->addPath('user', '/path/to/user/templates');
$renderer->addPath('page', '/path/to/page/templates');

// Set layout
$renderer->setLayout('layout::main');

// Render template
$html = $renderer->render('user::profile', [
    'user' => $user,
    'title' => 'User Profile'
]);
```

**Template Structure:**
```
templates/
├── layout/
│   ├── main.phtml
│   └── admin.phtml
├── user/
│   ├── profile.phtml
│   ├── login.phtml
│   └── register.phtml
└── page/
    ├── home.phtml
    └── about.phtml
```

**Template Example (user/profile.phtml):**
```php
<?php $this->layout('layout::main', ['title' => 'User Profile']) ?>

<div class="user-profile">
    <h1>Welcome, <?= $this->e($user->getUsername()) ?></h1>
    <p>Email: <?= $this->e($user->getEmail()) ?></p>
    
    <?php if ($user->isEmailVerified()): ?>
        <span class="badge badge-success">Email Verified</span>
    <?php else: ?>
        <span class="badge badge-warning">Email Not Verified</span>
    <?php endif ?>
</div>
```

#### Template Path Provider

Centralized template path management.

```php
use Minimal\Core\Service\ConfigBasedTemplatePathProvider;

// Configuration in config/autoload/templates.global.php
return [
    'paths' => [
        'templates' => [
            'layout' => 'templates/layout',
            'user' => 'templates/user',
            'page' => 'templates/page',
            'error' => 'templates/error'
        ]
    ]
];

// Usage
$provider = new ConfigBasedTemplatePathProvider($paths, $config);
$templatePaths = $provider->getTemplatePaths();
$userPath = $provider->getTemplatePathForNamespace('user');
```

### Service Layer

#### ConfigProvider

Dependency injection configuration for Core module.

```php
use Minimal\Core\ConfigProvider;

$configProvider = new ConfigProvider();
$config = $configProvider();

// Returns:
[
    'dependencies' => [
        'factories' => [
            Paths::class => PathsFactory::class,
            DatabaseConnectionFactory::class => DatabaseConnectionFactoryFactory::class,
            TemplatePathProviderInterface::class => TemplatePathProviderFactory::class,
            TemplateRendererInterface::class => NativePhpRendererFactory::class,
        ]
    ]
]
```

#### Factory Classes

**DatabaseConnectionFactoryFactory:**
```php
public function __invoke(ContainerInterface $container): DatabaseConnectionFactory
{
    $paths = $container->get(Paths::class);
    $databasePath = $paths->getPath('db', 'var/db');
    
    if (!is_string($databasePath)) {
        $databasePath = 'var/db';
    }

    return new DatabaseConnectionFactory($databasePath);
}
```

### Compatibility Layer

#### FunctionChecker

Detects available PHP functions in shared hosting environments.

```php
use Minimal\Core\Compatibility\FunctionChecker;

// Check if function is available
if (FunctionChecker::isAvailable('exec')) {
    exec('ls -la', $output);
}

// Safe execution with fallbacks
$result = FunctionChecker::safeExec('git --version');

// Check capabilities
$hasExec = FunctionChecker::hasExecCapability();
$canAccessUrls = FunctionChecker::canAccessUrls();
$hasCurl = FunctionChecker::hasCurlSupport();

// Get disabled functions list
$disabled = FunctionChecker::getDisabledFunctions();
```

#### SafeFileOperations

Safe file operations for shared hosting.

```php
use Minimal\Core\Compatibility\SafeFileOperations;

// Create directory with fallbacks
$success = SafeFileOperations::createDirectory('var/cache', 0755);

// Safe file writing
$written = SafeFileOperations::writeFile('var/cache/data.txt', $content);
```

## Configuration

### Database Configuration

```php
// config/autoload/database.local.php
return [
    'database' => [
        'path' => 'var/db',
        'modules' => [
            'user' => 'user.sqlite',
            'page' => 'page.sqlite'
        ]
    ]
];
```

### Template Configuration

```php
// config/autoload/templates.global.php
return [
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

## Testing

### Database Testing

```php
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;

class DatabaseTest extends TestCase
{
    private DatabaseConnectionFactory $factory;
    
    protected function setUp(): void
    {
        // Use in-memory database for tests
        $this->factory = new DatabaseConnectionFactory(':memory:');
    }
    
    public function testUserConnection(): void
    {
        $db = $this->factory->getConnection('user');
        $this->assertInstanceOf(PDO::class, $db);
    }
}
```

### Template Testing

```php
use Minimal\Core\Template\NativePhpRenderer;

class TemplateTest extends TestCase
{
    public function testRenderTemplate(): void
    {
        $renderer = new NativePhpRenderer();
        $renderer->addPath('test', __DIR__ . '/templates');
        
        $html = $renderer->render('test::simple', ['name' => 'John']);
        $this->assertStringContainsString('Hello John', $html);
    }
}
```

## Best Practices

### Database
- Use separate databases per module
- Always use parameter binding in queries
- Run migrations in development and staging first
- Use transactions for multi-step operations

### Templates
- Always escape output with `$this->e()`
- Use meaningful template namespaces
- Keep templates simple and focused
- Separate layout from content templates

### Compatibility
- Check function availability before use
- Provide fallbacks for disabled functions
- Test on shared hosting environments
- Use safe file operations

## Performance Considerations

- Connection pooling reduces database overhead
- Template compilation can be cached
- Query builder uses prepared statements
- Lazy loading of services through factories

## Security

- All database queries use parameter binding
- Template output is escaped by default
- File operations include permission checks
- Function availability is validated before execution
