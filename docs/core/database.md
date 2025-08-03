# Core Database Layer

The Core database layer provides a modular approach to database management with SQLite as the primary storage engine.

## Architecture

### Modular Database Design

Each module/domain has its own SQLite database file:

```
var/db/
├── user.sqlite      # User management data
├── page.sqlite      # Page/content data
├── blog.sqlite      # Blog posts and comments
└── analytics.sqlite # Analytics and tracking
```

**Benefits:**
- Clear separation of concerns
- Independent module development
- Easier backup and migration
- Reduced lock contention
- Module-specific optimization

## DatabaseConnectionFactory

### Basic Usage

```php
use Minimal\Core\Database\Connection\DatabaseConnectionFactory;

// Initialize with database directory
$factory = new DatabaseConnectionFactory('var/db');

// Get connection for specific module
$userDb = $factory->getConnection('user');
$pageDb = $factory->getConnection('page');

// Connections are pooled and reused
$sameUserDb = $factory->getConnection('user'); // Returns same instance
```

### Configuration Options

```php
// Standard file-based databases
$factory = new DatabaseConnectionFactory('var/db');

// In-memory database (for testing)
$testFactory = new DatabaseConnectionFactory(':memory:');

// Custom database names
$factory = new DatabaseConnectionFactory('var/db');
$customDb = $factory->getConnection('analytics', 'custom_analytics.sqlite');
```

### Connection Features

**Automatic Configuration:**
- WAL mode for better concurrency
- Foreign key constraints enabled
- UTF-8 encoding
- Optimized pragma settings

**Error Handling:**
```php
try {
    $db = $factory->getConnection('user');
} catch (RuntimeException $e) {
    // Handle connection errors
    error_log("Database connection failed: " . $e->getMessage());
}
```

## MigrationRunner

### Migration Structure

```php
// migrations/user/001_create_users_table.php
<?php

return [
    'up' => "
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email VARCHAR(255) UNIQUE NOT NULL,
            username VARCHAR(100) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(100),
            last_name VARCHAR(100),
            email_verified_at DATETIME,
            email_verification_token VARCHAR(255),
            password_reset_token VARCHAR(255),
            password_reset_expires_at DATETIME,
            role VARCHAR(50) DEFAULT 'user',
            status VARCHAR(20) DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE INDEX idx_users_email ON users(email);
        CREATE INDEX idx_users_username ON users(username);
        CREATE INDEX idx_users_status ON users(status);
    ",
    'down' => "
        DROP TABLE IF EXISTS users;
    "
];
```

### Running Migrations

```php
use Minimal\Core\Database\Migration\MigrationRunner;

$runner = new MigrationRunner($connectionFactory, 'migrations');

// Run migrations for specific module
$results = $runner->runMigrations('user');
foreach ($results as $migration => $result) {
    echo "Migration {$migration}: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
}

// Run all pending migrations
$allResults = $runner->runAllMigrations();

// Check migration status
$pending = $runner->getPendingMigrations('user');
$applied = $runner->getAppliedMigrations('user');
```

### Creating Migrations

```php
// Create new migration file
$migrationFile = $runner->createMigration('user', 'add_two_factor_auth');

// Generated file: migrations/user/002_add_two_factor_auth.php
<?php

return [
    'up' => "
        ALTER TABLE users ADD COLUMN two_factor_secret VARCHAR(255);
        ALTER TABLE users ADD COLUMN two_factor_enabled BOOLEAN DEFAULT 0;
    ",
    'down' => "
        ALTER TABLE users DROP COLUMN two_factor_secret;
        ALTER TABLE users DROP COLUMN two_factor_enabled;
    "
];
```

### Migration Tracking

Migrations are tracked in a `migrations` table:

```sql
CREATE TABLE migrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    module VARCHAR(100) NOT NULL,
    migration VARCHAR(255) NOT NULL,
    executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(module, migration)
);
```

## QueryBuilder

### Basic Queries

```php
use Minimal\Core\Database\Query\QueryBuilder;

$builder = new QueryBuilder($pdo);

// Simple SELECT
$users = $builder->table('users')->get();

// SELECT with columns
$users = $builder->table('users')
    ->select(['id', 'email', 'username'])
    ->get();

// Single record
$user = $builder->table('users')
    ->where('id', '=', 123)
    ->first();
```

### WHERE Conditions

```php
// Single condition
$activeUsers = $builder->table('users')
    ->where('status', '=', 'active')
    ->get();

// Multiple conditions (AND)
$recentActiveUsers = $builder->table('users')
    ->where('status', '=', 'active')
    ->where('created_at', '>', '2024-01-01')
    ->get();

// Different operators
$builder->where('age', '>', 18)
        ->where('email', 'LIKE', '%@example.com')
        ->where('role', 'IN', ['admin', 'moderator']);
```

### Ordering and Limiting

```php
// Order by single column
$users = $builder->table('users')
    ->orderBy('created_at', 'DESC')
    ->get();

// Multiple order columns
$users = $builder->table('users')
    ->orderBy('last_name', 'ASC')
    ->orderBy('first_name', 'ASC')
    ->get();

// Pagination
$users = $builder->table('users')
    ->orderBy('id')
    ->limit(20)
    ->offset(40)
    ->get();
```

### INSERT Operations

```php
// Single insert
$userId = $builder->table('users')
    ->insert([
        'email' => 'john@example.com',
        'username' => 'johndoe',
        'password_hash' => password_hash('secret', PASSWORD_DEFAULT),
        'first_name' => 'John',
        'last_name' => 'Doe'
    ]);

// Batch insert
$userIds = $builder->table('users')
    ->insertBatch([
        [
            'email' => 'user1@example.com',
            'username' => 'user1',
            'password_hash' => password_hash('pass1', PASSWORD_DEFAULT)
        ],
        [
            'email' => 'user2@example.com',
            'username' => 'user2',
            'password_hash' => password_hash('pass2', PASSWORD_DEFAULT)
        ]
    ]);
```

### UPDATE Operations

```php
// Update single record
$affected = $builder->table('users')
    ->where('id', '=', 123)
    ->update([
        'last_login' => date('Y-m-d H:i:s'),
        'login_count' => 'login_count + 1' // Raw SQL
    ]);

// Update multiple records
$affected = $builder->table('users')
    ->where('status', '=', 'pending')
    ->where('created_at', '<', date('Y-m-d', strtotime('-30 days')))
    ->update(['status' => 'expired']);
```

### DELETE Operations

```php
// Delete single record
$deleted = $builder->table('users')
    ->where('id', '=', 123)
    ->delete();

// Delete multiple records
$deleted = $builder->table('users')
    ->where('status', '=', 'inactive')
    ->where('last_login', '<', date('Y-m-d', strtotime('-1 year')))
    ->delete();
```

### Advanced Usage

```php
// Count records
$count = $builder->table('users')
    ->where('status', '=', 'active')
    ->count();

// Check if record exists
$exists = $builder->table('users')
    ->where('email', '=', 'test@example.com')
    ->exists();

// Raw SQL in SELECT
$users = $builder->table('users')
    ->select(['*', 'UPPER(username) as username_upper'])
    ->get();

// Complex WHERE with raw SQL
$builder->whereRaw('DATE(created_at) = ?', [date('Y-m-d')]);
```

## Best Practices

### Connection Management

```php
// Use dependency injection
class UserRepository
{
    public function __construct(
        private DatabaseConnectionFactory $connectionFactory
    ) {}
    
    private function getConnection(): PDO
    {
        return $this->connectionFactory->getConnection('user');
    }
}
```

### Transaction Handling

```php
$db = $factory->getConnection('user');

try {
    $db->beginTransaction();
    
    // Multiple operations
    $builder->table('users')->insert($userData);
    $builder->table('user_profiles')->insert($profileData);
    
    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    throw $e;
}
```

### Error Handling

```php
try {
    $users = $builder->table('users')->get();
} catch (PDOException $e) {
    // Log database errors
    error_log("Database query failed: " . $e->getMessage());
    
    // Return empty result or throw application exception
    return [];
}
```

### Performance Optimization

```php
// Use indexes for frequently queried columns
"CREATE INDEX idx_users_email ON users(email);"
"CREATE INDEX idx_users_status_created ON users(status, created_at);"

// Use LIMIT for large datasets
$users = $builder->table('users')
    ->limit(1000)
    ->get();

// Use specific columns instead of SELECT *
$users = $builder->table('users')
    ->select(['id', 'email', 'username'])
    ->get();
```

## Testing

### Test Database Setup

```php
class DatabaseTestCase extends TestCase
{
    protected DatabaseConnectionFactory $factory;
    
    protected function setUp(): void
    {
        $this->factory = new DatabaseConnectionFactory(':memory:');
        $this->runTestMigrations();
    }
    
    private function runTestMigrations(): void
    {
        $runner = new MigrationRunner($this->factory, 'tests/migrations');
        $runner->runMigrations('user');
    }
}
```

### Query Builder Testing

```php
class QueryBuilderTest extends DatabaseTestCase
{
    public function testInsertAndSelect(): void
    {
        $db = $this->factory->getConnection('user');
        $builder = new QueryBuilder($db);
        
        $userId = $builder->table('users')
            ->insert(['email' => 'test@example.com']);
            
        $this->assertIsInt($userId);
        
        $user = $builder->table('users')
            ->where('id', '=', $userId)
            ->first();
            
        $this->assertEquals('test@example.com', $user['email']);
    }
}
```

## Troubleshooting

### Common Issues

**Database Lock Errors:**
```php
// Enable WAL mode (done automatically)
$db->exec('PRAGMA journal_mode=WAL;');

// Set busy timeout
$db->exec('PRAGMA busy_timeout=30000;');
```

**Permission Issues:**
```php
// Check directory permissions
if (!is_writable('var/db')) {
    throw new RuntimeException('Database directory is not writable');
}
```

**Migration Failures:**
```php
// Check migration syntax
$migration = include 'migrations/user/001_create_users.php';
if (!isset($migration['up']) || !isset($migration['down'])) {
    throw new RuntimeException('Invalid migration format');
}
```
