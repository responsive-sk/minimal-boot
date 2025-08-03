# Core Template System

The Core template system provides a lightweight, native PHP templating solution with namespace support and layout management.

## Architecture

### NativePhpRenderer

The `NativePhpRenderer` implements Mezzio's `TemplateRendererInterface` using pure PHP templates without external dependencies.

**Key Features:**
- Native PHP syntax
- Template namespaces
- Layout system
- Automatic escaping
- Path management
- Default parameters

## Basic Usage

### Setup

```php
use Minimal\Core\Template\NativePhpRenderer;

$renderer = new NativePhpRenderer();

// Add template paths with namespaces
$renderer->addPath('layout', 'templates/layout');
$renderer->addPath('user', 'templates/user');
$renderer->addPath('page', 'templates/page');

// Set default layout
$renderer->setLayout('layout::main');
```

### Rendering Templates

```php
// Render template with data
$html = $renderer->render('user::profile', [
    'user' => $user,
    'title' => 'User Profile',
    'breadcrumbs' => ['Home', 'Users', 'Profile']
]);

// Render without layout
$html = $renderer->render('user::profile-partial', $data);
```

## Template Structure

### Directory Organization

```
templates/
├── layout/
│   ├── main.phtml           # Main application layout
│   ├── admin.phtml          # Admin panel layout
│   └── email.phtml          # Email template layout
├── user/
│   ├── profile.phtml        # User profile page
│   ├── login.phtml          # Login form
│   ├── register.phtml       # Registration form
│   └── partials/
│       ├── user-card.phtml  # User card component
│       └── avatar.phtml     # Avatar component
├── page/
│   ├── home.phtml           # Homepage
│   ├── about.phtml          # About page
│   └── contact.phtml        # Contact page
└── error/
    ├── 404.phtml            # Not found page
    ├── 500.phtml            # Server error page
    └── maintenance.phtml    # Maintenance page
```

### Template Namespaces

```php
// Register namespaces
$renderer->addPath('layout', 'templates/layout');
$renderer->addPath('user', 'templates/user');
$renderer->addPath('admin', 'templates/admin');

// Use namespaced templates
$renderer->render('layout::main', $data);
$renderer->render('user::profile', $data);
$renderer->render('admin::dashboard', $data);
```

## Layout System

### Main Layout Template

```php
<!-- templates/layout/main.phtml -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Default Title') ?></title>
    
    <!-- CSS -->
    <link href="/css/app.css" rel="stylesheet">
    
    <!-- Additional head content -->
    <?= $this->section('head') ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">My App</a>
            
            <?php if (isset($user)): ?>
                <div class="navbar-nav">
                    <span>Welcome, <?= $this->e($user->getUsername()) ?></span>
                    <a href="/logout">Logout</a>
                </div>
            <?php else: ?>
                <div class="navbar-nav">
                    <a href="/login">Login</a>
                    <a href="/register">Register</a>
                </div>
            <?php endif ?>
        </div>
    </nav>
    
    <!-- Main content -->
    <main class="container">
        <?= $this->section('content') ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 My Application. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="/js/app.js"></script>
    <?= $this->section('scripts') ?>
</body>
</html>
```

### Content Template with Layout

```php
<!-- templates/user/profile.phtml -->
<?php $this->layout('layout::main', [
    'title' => 'User Profile - ' . $user->getUsername()
]) ?>

<?php $this->start('head') ?>
<meta name="description" content="Profile page for <?= $this->e($user->getUsername()) ?>">
<link href="/css/profile.css" rel="stylesheet">
<?php $this->end() ?>

<?php $this->start('content') ?>
<div class="profile-page">
    <div class="row">
        <div class="col-md-4">
            <!-- User avatar and basic info -->
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <img src="<?= $this->e($user->getAvatarUrl()) ?>" 
                         alt="<?= $this->e($user->getUsername()) ?>" 
                         class="avatar-large">
                </div>
                
                <h2><?= $this->e($user->getFullName()) ?></h2>
                <p class="text-muted">@<?= $this->e($user->getUsername()) ?></p>
                
                <?php if ($user->isEmailVerified()): ?>
                    <span class="badge badge-success">Email Verified</span>
                <?php else: ?>
                    <span class="badge badge-warning">Email Not Verified</span>
                <?php endif ?>
            </div>
        </div>
        
        <div class="col-md-8">
            <!-- Profile details -->
            <div class="profile-content">
                <h3>Profile Information</h3>
                
                <dl class="row">
                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9"><?= $this->e($user->getEmail()) ?></dd>
                    
                    <dt class="col-sm-3">Member Since:</dt>
                    <dd class="col-sm-9"><?= $user->getCreatedAt()->format('F j, Y') ?></dd>
                    
                    <dt class="col-sm-3">Last Login:</dt>
                    <dd class="col-sm-9">
                        <?php if ($user->getLastLogin()): ?>
                            <?= $user->getLastLogin()->format('F j, Y g:i A') ?>
                        <?php else: ?>
                            Never
                        <?php endif ?>
                    </dd>
                </dl>
                
                <!-- Edit profile button -->
                <?php if ($canEdit): ?>
                    <a href="/user/edit" class="btn btn-primary">Edit Profile</a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<?php $this->end() ?>

<?php $this->start('scripts') ?>
<script src="/js/profile.js"></script>
<?php $this->end() ?>
```

## Template Features

### Automatic Escaping

```php
<!-- Safe output escaping -->
<h1><?= $this->e($title) ?></h1>
<p><?= $this->e($user->getDescription()) ?></p>

<!-- Raw output (use carefully) -->
<div><?= $this->raw($htmlContent) ?></div>

<!-- URL escaping -->
<a href="<?= $this->escapeUrl($url) ?>">Link</a>

<!-- Attribute escaping -->
<input type="text" value="<?= $this->escapeHtmlAttr($value) ?>">
```

### Conditional Rendering

```php
<!-- Simple conditions -->
<?php if ($user->isAdmin()): ?>
    <a href="/admin" class="btn btn-admin">Admin Panel</a>
<?php endif ?>

<!-- If-else -->
<?php if ($user->hasAvatar()): ?>
    <img src="<?= $this->e($user->getAvatarUrl()) ?>" alt="Avatar">
<?php else: ?>
    <div class="default-avatar">
        <?= strtoupper(substr($user->getUsername(), 0, 1)) ?>
    </div>
<?php endif ?>

<!-- Switch statement -->
<?php switch ($user->getRole()): ?>
    <?php case 'admin': ?>
        <span class="badge badge-danger">Administrator</span>
        <?php break ?>
    <?php case 'moderator': ?>
        <span class="badge badge-warning">Moderator</span>
        <?php break ?>
    <?php default: ?>
        <span class="badge badge-secondary">User</span>
<?php endswitch ?>
```

### Loops and Iteration

```php
<!-- Simple foreach -->
<ul class="user-list">
    <?php foreach ($users as $user): ?>
        <li>
            <a href="/user/<?= $user->getId() ?>">
                <?= $this->e($user->getUsername()) ?>
            </a>
        </li>
    <?php endforeach ?>
</ul>

<!-- Loop with counter -->
<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Username</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $index => $user): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= $this->e($user->getUsername()) ?></td>
                <td><?= $this->e($user->getEmail()) ?></td>
                <td>
                    <span class="badge badge-<?= $user->isActive() ? 'success' : 'secondary' ?>">
                        <?= $user->isActive() ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<!-- Empty state -->
<?php if (empty($users)): ?>
    <div class="empty-state">
        <p>No users found.</p>
        <a href="/user/create" class="btn btn-primary">Create First User</a>
    </div>
<?php endif ?>
```

### Partials and Includes

```php
<!-- Include partial template -->
<?= $this->render('user::partials/user-card', ['user' => $user]) ?>

<!-- Include with additional data -->
<?= $this->render('shared::pagination', [
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'baseUrl' => '/users'
]) ?>
```

### Helper Functions

```php
<!-- URL generation -->
<a href="<?= $this->url('user.profile', ['id' => $user->getId()]) ?>">Profile</a>

<!-- Asset URLs -->
<img src="<?= $this->asset('images/logo.png') ?>" alt="Logo">
<link href="<?= $this->asset('css/app.css') ?>" rel="stylesheet">

<!-- Date formatting -->
<time datetime="<?= $user->getCreatedAt()->format('c') ?>">
    <?= $this->formatDate($user->getCreatedAt(), 'F j, Y') ?>
</time>

<!-- Number formatting -->
<span class="price"><?= $this->formatCurrency($price, 'USD') ?></span>
<span class="count"><?= $this->formatNumber($count) ?></span>
```

## Advanced Features

### Custom Helper Methods

```php
// In NativePhpRenderer or custom extension
public function formatUserRole(string $role): string
{
    return match($role) {
        'admin' => '<span class="badge badge-danger">Administrator</span>',
        'moderator' => '<span class="badge badge-warning">Moderator</span>',
        'user' => '<span class="badge badge-secondary">User</span>',
        default => '<span class="badge badge-light">Unknown</span>'
    };
}

// Usage in template
<?= $this->formatUserRole($user->getRole()) ?>
```

### Template Inheritance

```php
<!-- Base template: templates/layout/base.phtml -->
<!DOCTYPE html>
<html>
<head>
    <title><?= $this->section('title', 'Default Title') ?></title>
    <?= $this->section('head') ?>
</head>
<body>
    <header><?= $this->section('header') ?></header>
    <main><?= $this->section('content') ?></main>
    <footer><?= $this->section('footer') ?></footer>
</body>
</html>

<!-- Extended template: templates/layout/main.phtml -->
<?php $this->extend('layout::base') ?>

<?php $this->start('header') ?>
<nav class="navbar">
    <!-- Navigation content -->
</nav>
<?php $this->end() ?>

<?php $this->start('footer') ?>
<footer class="footer">
    <!-- Footer content -->
</footer>
<?php $this->end() ?>
```

### Error Handling

```php
try {
    $html = $renderer->render('user::profile', $data);
} catch (TemplateNotFoundException $e) {
    // Template file not found
    $html = $renderer->render('error::404');
} catch (TemplateRenderException $e) {
    // Error during template rendering
    error_log("Template render error: " . $e->getMessage());
    $html = $renderer->render('error::500');
}
```

## Configuration

### Template Path Provider

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
            'page' => 'templates/page',
            'admin' => 'templates/admin',
            'error' => 'templates/error'
        ]
    ]
];
```

### Factory Configuration

```php
// In ConfigProvider
'factories' => [
    TemplateRendererInterface::class => NativePhpRendererFactory::class,
    TemplatePathProviderInterface::class => TemplatePathProviderFactory::class,
]
```

## Testing

### Template Testing

```php
class TemplateTest extends TestCase
{
    private NativePhpRenderer $renderer;
    
    protected function setUp(): void
    {
        $this->renderer = new NativePhpRenderer();
        $this->renderer->addPath('test', __DIR__ . '/templates');
    }
    
    public function testRenderSimpleTemplate(): void
    {
        $html = $this->renderer->render('test::simple', [
            'name' => 'John Doe'
        ]);
        
        $this->assertStringContainsString('Hello John Doe', $html);
    }
    
    public function testEscaping(): void
    {
        $html = $this->renderer->render('test::escape', [
            'content' => '<script>alert("xss")</script>'
        ]);
        
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }
}
```

## Best Practices

### Security
- Always escape output with `$this->e()`
- Use `$this->raw()` only for trusted content
- Validate and sanitize data before passing to templates
- Use CSRF tokens in forms

### Performance
- Keep templates simple and focused
- Avoid complex logic in templates
- Use partials for reusable components
- Cache rendered output when appropriate

### Organization
- Use meaningful namespace names
- Group related templates in directories
- Keep template files small and focused
- Use consistent naming conventions

### Maintainability
- Document complex template logic
- Use descriptive variable names
- Separate presentation from business logic
- Test templates with various data scenarios
