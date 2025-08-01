# Templates

Minimal Boot uses a native PHP template system that provides excellent performance, debugging capabilities, and full access to PHP features without the overhead of template compilation.

## Template System Overview

### Why Native PHP Templates?

- **No Compilation** - Templates are executed directly as PHP
- **Better Performance** - No template compilation overhead
- **Full PHP Power** - Access to all PHP functions and features
- **Easy Debugging** - Standard PHP debugging tools work seamlessly
- **No Learning Curve** - If you know PHP, you know the template syntax

### Template Architecture

```
Templates/
├── Layout System      # Hierarchical template inheritance
├── Helper Functions   # URL generation, escaping, utilities
├── Component Support  # Reusable template components
└── Security Features  # XSS protection, CSRF tokens
```

## Basic Template Usage

### Simple Template

Create `src/Module/templates/example.phtml`:

```php
<?php
// Set layout and pass data to it
$layout('layout::default', [
    'title' => 'Example Page',
    'description' => 'This is an example page',
]);
?>

<div class="container">
    <h1><?= $this->escapeHtml($title) ?></h1>
    <p><?= $this->escapeHtml($content) ?></p>
</div>
```

### Template Variables

Variables are passed from handlers:

```php
// In handler
return new HtmlResponse(
    $this->template->render('module::template', [
        'title' => 'Page Title',
        'content' => 'Page content',
        'items' => ['item1', 'item2', 'item3'],
    ])
);
```

```php
<!-- In template -->
<h1><?= $this->escapeHtml($title) ?></h1>
<p><?= $this->escapeHtml($content) ?></p>

<ul>
    <?php foreach ($items as $item): ?>
        <li><?= $this->escapeHtml($item) ?></li>
    <?php endforeach; ?>
</ul>
```

## Layout System

### Using Layouts

Layouts provide consistent structure across pages:

```php
<?php
$layout('layout::default', [
    'title' => 'Page Title',
    'description' => 'SEO description',
    'author' => 'Author Name',
    'keywords' => 'keyword1, keyword2',
]);
?>

<!-- Page content goes here -->
<main class="content">
    <h1>Welcome</h1>
</main>
```

### Available Layouts

#### Default Layout (`layout::default`)

Bootstrap-based layout with navigation:

```php
$layout('layout::default', [
    'title' => 'Page Title',
    'description' => 'SEO description',
    'author' => 'Author Name',
]);
```

Features:
- Bootstrap 5 CSS framework
- Responsive navigation
- Dark mode toggle
- SEO meta tags

#### Tailwind Layout (`layout::tailwind`)

TailwindCSS-based layout with Alpine.js:

```php
$layout('layout::tailwind', [
    'title' => 'Page Title',
    'description' => 'SEO description',
    'cssUrl' => '/themes/main/assets/main.css',
    'jsUrl' => '/themes/main/assets/main.js',
]);
```

Features:
- TailwindCSS utility classes
- Alpine.js for interactivity
- Modern design components
- Responsive layout

#### Bootstrap Layout (`layout::bootstrap`)

Pure Bootstrap layout:

```php
$layout('layout::bootstrap', [
    'title' => 'Page Title',
    'description' => 'SEO description',
]);
```

Features:
- Clean Bootstrap design
- Minimal JavaScript
- Fast loading
- Classic styling

### Creating Custom Layouts

Create `src/Shared/templates/layout/custom.phtml`:

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->escapeHtml($title ?? 'Default Title') ?></title>
    <meta name="description" content="<?= $this->escapeHtmlAttr($description ?? '') ?>">
    
    <!-- Custom CSS -->
    <link href="/css/custom.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar">
            <!-- Navigation -->
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        <!-- Footer content -->
    </footer>

    <!-- Custom JavaScript -->
    <script src="/js/custom.js"></script>
</body>
</html>
```

Use the custom layout:

```php
$layout('layout::custom', [
    'title' => 'Custom Page',
    'description' => 'Page with custom layout',
]);
```

## Template Helpers

### Escaping Functions

Always escape output to prevent XSS:

```php
<!-- Escape HTML content -->
<h1><?= $this->escapeHtml($title) ?></h1>

<!-- Escape HTML attributes -->
<a href="<?= $this->escapeHtmlAttr($url) ?>"><?= $this->escapeHtml($linkText) ?></a>

<!-- Escape JavaScript -->
<script>
    var data = <?= $this->escapeJs($jsonData) ?>;
</script>

<!-- Escape CSS -->
<style>
    .class { color: <?= $this->escapeCss($color) ?>; }
</style>

<!-- Escape URLs -->
<a href="<?= $this->escapeUrl($dynamicUrl) ?>">Link</a>
```

### URL Generation

Generate URLs using the URL helper:

```php
<!-- Generate route URLs -->
<a href="<?= $url('page::index') ?>">Home</a>
<a href="<?= $url('page::view', ['slug' => 'about']) ?>">About</a>

<!-- Generate URLs with query parameters -->
<a href="<?= $url('blog::index', [], ['page' => 2]) ?>">Page 2</a>

<!-- Generate absolute URLs -->
<a href="<?= $url('page::index', [], [], null, ['force_canonical' => true]) ?>">Absolute URL</a>
```

### Path Helpers

Access application paths:

```php
<!-- Asset URLs -->
<img src="<?= $this->asset('/images/logo.png') ?>" alt="Logo">
<link href="<?= $this->asset('/css/style.css') ?>" rel="stylesheet">

<!-- Public path -->
<form action="<?= $this->path('/upload') ?>" method="post">
    <!-- Form content -->
</form>
```

## Template Components

### Partial Templates

Create reusable template components:

Create `src/Shared/templates/partial/pagination.phtml`:

```php
<?php if ($totalPages > 1): ?>
    <nav aria-label="Pagination">
        <ul class="pagination">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $url($route, $routeParams, ['page' => $currentPage - 1]) ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $url($route, $routeParams, ['page' => $i]) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $url($route, $routeParams, ['page' => $currentPage + 1]) ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>
```

Use the partial:

```php
<!-- In your template -->
<?= $this->render('partial::pagination', [
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'route' => 'blog::index',
    'routeParams' => [],
]) ?>
```

### Template Inheritance

Create base templates and extend them:

Create `src/Shared/templates/base/admin.phtml`:

```php
<?php $layout('layout::default', $layoutData ?? []); ?>

<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <!-- Admin navigation -->
        <nav>
            <ul>
                <li><a href="<?= $url('admin::dashboard') ?>">Dashboard</a></li>
                <li><a href="<?= $url('admin::users') ?>">Users</a></li>
                <li><a href="<?= $url('admin::settings') ?>">Settings</a></li>
            </ul>
        </nav>
    </aside>

    <main class="admin-content">
        <?= $content ?>
    </main>
</div>
```

Extend the admin base:

```php
<?php
$this->layout('base::admin', [
    'layoutData' => [
        'title' => 'Admin Dashboard',
        'description' => 'Administration panel',
    ],
]);
?>

<h1>Dashboard</h1>
<div class="dashboard-widgets">
    <!-- Dashboard content -->
</div>
```

## Form Handling

### CSRF Protection

Include CSRF tokens in forms:

```php
<form method="post" action="<?= $url('contact::submit') ?>">
    <?= $this->csrfToken() ?>
    
    <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
```

### Form Validation Display

Display validation errors:

```php
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $field => $fieldErrors): ?>
                <?php foreach ($fieldErrors as $error): ?>
                    <li><?= $this->escapeHtml($error) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
```

### Flash Messages

Display flash messages:

```php
<?php if (!empty($flashMessages)): ?>
    <?php foreach ($flashMessages as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?= $this->escapeHtmlAttr($type) ?> alert-dismissible fade show">
                <?= $this->escapeHtml($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
```

## Template Configuration

### Template Paths

Configure template paths in `config/autoload/templates.global.php`:

```php
return [
    'templates' => [
        'paths' => [
            'layout' => [getcwd() . '/src/Shared/templates/layout'],
            'partial' => [getcwd() . '/src/Shared/templates/partial'],
            'error' => [getcwd() . '/src/Shared/templates/error'],
            'page' => [getcwd() . '/src/Page/templates'],
            'contact' => [getcwd() . '/src/Contact/templates'],
        ],
    ],
];
```

### Template Extensions

Register custom template extensions:

```php
// In module ConfigProvider
return [
    'templates' => [
        'extension' => 'phtml', // Default extension
        'paths' => [
            'module' => [__DIR__ . '/templates'],
        ],
    ],
];
```

## Performance Optimization

### Template Caching

Enable template caching in production:

```php
// config/autoload/templates.production.php
return [
    'templates' => [
        'cache' => [
            'enabled' => true,
            'path' => 'var/cache/templates',
        ],
    ],
];
```

### Asset Optimization

Optimize assets in templates:

```php
<!-- Minified CSS in production -->
<?php if ($this->isProduction()): ?>
    <link href="<?= $this->asset('/css/app.min.css') ?>" rel="stylesheet">
<?php else: ?>
    <link href="<?= $this->asset('/css/app.css') ?>" rel="stylesheet">
<?php endif; ?>

<!-- Deferred JavaScript -->
<script src="<?= $this->asset('/js/app.js') ?>" defer></script>
```

## Security Best Practices

### Always Escape Output

```php
<!-- GOOD -->
<h1><?= $this->escapeHtml($title) ?></h1>

<!-- BAD -->
<h1><?= $title ?></h1>
```

### Validate Template Data

```php
// In handler
$data = [
    'title' => $this->validateTitle($request->getQueryParams()['title'] ?? ''),
    'content' => $this->sanitizeContent($content),
];

return new HtmlResponse($this->template->render('page::view', $data));
```

### Use CSRF Tokens

```php
<!-- Always include CSRF tokens in forms -->
<form method="post">
    <?= $this->csrfToken() ?>
    <!-- Form fields -->
</form>
```

## Next Steps

- [Domain Layer](domain.md) - Domain-Driven Design patterns
- [Configuration](configuration.md) - Advanced configuration
- [Development](development.md) - Development workflow
- [Deployment](deployment.md) - Production deployment
