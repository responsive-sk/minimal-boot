# Template System Documentation

## Overview

Minimal Boot uses a modern, organized template system with theme-aware capabilities and centralized path management via the Paths service.

## Directory Structure

```
templates/
├── themes/
│   ├── bootstrap/
│   │   ├── layouts/
│   │   │   ├── app.phtml          # Main Bootstrap layout
│   │   │   └── default.phtml      # Default Bootstrap layout
│   │   ├── pages/
│   │   │   ├── home.phtml         # Bootstrap home page
│   │   │   └── demo.phtml         # Bootstrap demo page
│   │   └── partials/
│   │       ├── header.phtml       # Bootstrap header
│   │       ├── footer.phtml       # Bootstrap footer
│   │       └── navigation.phtml   # Bootstrap navigation
│   └── tailwind/
│       ├── layouts/
│       │   ├── app.phtml          # Main Tailwind layout
│       │   └── default.phtml      # Default Tailwind layout
│       ├── pages/
│       │   ├── home.phtml         # Tailwind home page
│       │   └── demo.phtml         # Tailwind demo page
│       └── partials/
│           ├── header.phtml       # Tailwind header
│           ├── footer.phtml       # Tailwind footer
│           └── navigation.phtml   # Tailwind navigation
├── modules/
│   ├── auth/
│   │   ├── login.phtml
│   │   ├── register.phtml
│   │   └── dashboard.phtml
│   ├── contact/
│   │   └── form.phtml
│   ├── user/
│   │   ├── profile.phtml
│   │   └── settings.phtml
│   ├── page/
│   │   ├── view.phtml             # Dynamic page view
│   │   ├── about.phtml
│   │   └── who-we-are.phtml
│   └── session/
│       └── info.phtml
├── shared/
│   ├── error/
│   │   ├── 404.phtml
│   │   ├── 403.phtml
│   │   └── error.phtml
│   └── email/
│       ├── layout.phtml
│       └── welcome.phtml
└── components/
    ├── forms/
    │   ├── input.phtml
    │   ├── textarea.phtml
    │   └── button.phtml
    └── ui/
        ├── card.phtml
        ├── modal.phtml
        └── alert.phtml
```

## Template Namespaces

### Theme-based Templates
- `bootstrap_layouts::app` → `templates/themes/bootstrap/layouts/app.phtml`
- `bootstrap_pages::home` → `templates/themes/bootstrap/pages/home.phtml`
- `tailwind_layouts::app` → `templates/themes/tailwind/layouts/app.phtml`
- `tailwind_pages::home` → `templates/themes/tailwind/pages/home.phtml`

### Module Templates
- `auth::login` → `templates/modules/auth/login.phtml`
- `contact::form` → `templates/modules/contact/form.phtml`
- `user::profile` → `templates/modules/user/profile.phtml`
- `page::view` → `templates/modules/page/view.phtml`

### Shared Templates
- `error::404` → `templates/shared/error/404.phtml`
- `error::error` → `templates/shared/error/error.phtml`
- `email::welcome` → `templates/shared/email/welcome.phtml`

### Component Templates
- `forms::input` → `templates/components/forms/input.phtml`
- `ui::card` → `templates/components/ui/card.phtml`

## Configuration

### Paths Configuration (`config/autoload/paths.global.php`)

```php
'paths' => [
    // Template paths
    'templates' => 'templates',
    'templates_themes' => 'templates/themes',
    'templates_bootstrap' => 'templates/themes/bootstrap',
    'templates_tailwind' => 'templates/themes/tailwind',
    
    // Theme-specific paths
    'templates_bootstrap_layouts' => 'templates/themes/bootstrap/layouts',
    'templates_tailwind_layouts' => 'templates/themes/tailwind/layouts',
    'templates_bootstrap_pages' => 'templates/themes/bootstrap/pages',
    'templates_tailwind_pages' => 'templates/themes/tailwind/pages',
    
    // Module paths
    'templates_auth' => 'templates/modules/auth',
    'templates_contact' => 'templates/modules/contact',
    'templates_user' => 'templates/modules/user',
    'templates_page' => 'templates/modules/page',
    
    // Shared paths
    'templates_error' => 'templates/shared/error',
    'templates_email' => 'templates/shared/email',
    
    // Component paths
    'templates_components' => 'templates/components',
    'templates_forms' => 'templates/components/forms',
    'templates_ui' => 'templates/components/ui',
],
```

### Template Configuration (`config/autoload/templates.global.php`)

```php
'templates' => [
    'extension' => 'phtml',
    'paths' => [
        // Theme-based templates (using Paths service)
        'bootstrap' => ['@templates_bootstrap'],
        'tailwind' => ['@templates_tailwind'],
        
        // Theme layouts
        'bootstrap_layouts' => ['@templates_bootstrap_layouts'],
        'tailwind_layouts' => ['@templates_tailwind_layouts'],
        
        // Theme pages
        'bootstrap_pages' => ['@templates_bootstrap_pages'],
        'tailwind_pages' => ['@templates_tailwind_pages'],
        
        // Module templates
        'auth' => ['@templates_auth'],
        'contact' => ['@templates_contact'],
        'user' => ['@templates_user'],
        'page' => ['@templates_page'],
        
        // Shared templates
        'error' => ['@templates_error'],
        'email' => ['@templates_email'],
        
        // Component templates
        'forms' => ['@templates_forms'],
        'ui' => ['@templates_ui'],
        
        // Backward compatibility
        'layout' => ['@templates_bootstrap_layouts'],
    ],
],
```

## Usage Examples

### In Handlers

```php
class IndexHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get current theme
        $currentTheme = $this->themeService->getCurrentTheme();
        
        // Use theme-specific template
        $templateName = $currentTheme . '_pages::home';
        
        return new HtmlResponse(
            $this->template->render($templateName, [
                'title' => 'Welcome',
                'cssUrl' => $this->themeService->getThemeCssUrl(),
                'jsUrl' => $this->themeService->getThemeJsUrl(),
            ])
        );
    }
}
```

### In Templates

```php
<?php
// Theme-aware layout selection
$currentTheme = 'bootstrap'; // default
if (isset($cssUrl) && str_contains($cssUrl, 'themes/main/assets')) {
    $currentTheme = 'tailwind';
}

$layout($currentTheme . '_layouts::app', [
    'title' => $title ?? 'Default Title',
    'description' => $description ?? 'Default Description',
    'cssUrl' => $cssUrl ?? null,
    'jsUrl' => $jsUrl ?? null,
]);
?>

<div class="container">
    <h1><?= $this->e($title) ?></h1>
    <p><?= $this->e($content) ?></p>
</div>
```

## Theme-Aware Template Service

### ThemeAwareTemplateService

```php
class ThemeAwareTemplateService
{
    public function getLayoutTemplate(string $layout = 'app'): string
    {
        $theme = $this->themeService->getCurrentTheme();
        return "{$theme}_layouts::{$layout}";
    }
    
    public function getPageTemplate(string $page): string
    {
        $theme = $this->themeService->getCurrentTheme();
        return "{$theme}_pages::{$page}";
    }
    
    public function getModuleTemplate(string $module, string $template): string
    {
        return "{$module}::{$template}";
    }
}
```

## Best Practices

### 1. Theme-Aware Templates
Always use theme-aware template selection in handlers:

```php
// ✅ Good - theme-aware
$templateName = $currentTheme . '_pages::home';

// ❌ Bad - hardcoded theme
$templateName = 'bootstrap_pages::home';
```

### 2. CSS/JS Asset Passing
Always pass CSS and JS URLs to templates:

```php
return new HtmlResponse(
    $this->template->render($templateName, [
        'title' => 'Page Title',
        'cssUrl' => $this->themeService->getThemeCssUrl(),
        'jsUrl' => $this->themeService->getThemeJsUrl(),
    ])
);
```

### 3. Layout Selection in Templates
Use dynamic layout selection based on theme:

```php
<?php
$currentTheme = 'bootstrap'; // default
if (isset($cssUrl) && str_contains($cssUrl, 'themes/main/assets')) {
    $currentTheme = 'tailwind';
}

$layout($currentTheme . '_layouts::app', [
    'title' => $title,
    'cssUrl' => $cssUrl ?? null,
    'jsUrl' => $jsUrl ?? null,
]);
?>
```

### 4. Template Organization
- **Theme-specific templates** → `templates/themes/{theme}/`
- **Module templates** → `templates/modules/{module}/`
- **Shared templates** → `templates/shared/{type}/`
- **Reusable components** → `templates/components/{type}/`

### 5. Asset Management
Always use the `$asset()` helper for proper asset URLs:

```php
<!-- CSS and JS assets -->
<link rel="stylesheet" href="<?= $asset($finalCssUrl) ?>">
<script src="<?= $asset($finalJsUrl) ?>" defer></script>

<!-- Images with proper dimensions -->
<img src="<?= $asset('images/app/logo.svg') ?>"
     alt="Logo"
     width="120"
     height="32"
     class="h-8 w-auto">
```

### 6. Font Management
Fonts are centrally managed and automatically copied during build:

```css
/* Font definitions in theme CSS */
@font-face {
    font-family: 'Source Sans Pro';
    font-weight: 400;
    font-display: optional;
    src: url('/fonts/source-sans-pro-400.woff2?v=1') format('woff2');
}
```

Build process automatically copies fonts:
```bash
# Vite build copies fonts from src to public/fonts/
Copied source-sans-pro-400.woff2 to public/fonts/
```

## Migration from Old System

The old template system used scattered templates in module directories:
- `src/Page/templates/` → `templates/themes/{theme}/pages/` or `templates/modules/page/`
- `src/Shared/templates/` → `templates/themes/{theme}/layouts/` or `templates/shared/`
- `src/Auth/templates/` → `templates/modules/auth/`

All old template directories have been removed and templates migrated to the new organized structure.

## Template Code Quality and Security

### Inline CSS and JavaScript Removal

All templates have been refactored to remove inline CSS and JavaScript for better security, performance, and maintainability:

#### Before (Problematic)
```html
<!-- Inline styles -->
<div style="z-index: 9999; display: none;">...</div>
<img style="width: 60px; height: 60px;" src="...">

<!-- Inline JavaScript -->
<button onclick="history.back()">Go Back</button>
<script>
    function toggleTheme() {
        // Large inline script...
    }
</script>
```

#### After (Clean)
```html
<!-- CSS classes -->
<div class="theme-notification">...</div>
<img class="icon-circle-60" src="..." width="60" height="60">

<!-- Data attributes with event listeners -->
<button data-action="go-back">Go Back</button>
<!-- JavaScript in separate modules -->
```

### CSS Architecture

#### Centralized Styles
- **Bootstrap theme**: `src/Assets/bootstrap/src/style.css`
- **Tailwind theme**: `src/Assets/main/src/style.css`
- **Font management**: Centralized in `public/fonts/`

#### CSS Classes for Inline Styles
```css
/* Theme utilities */
.theme-notification { z-index: 9999; display: none; }
.icon-8rem { font-size: 8rem; opacity: 0.8; }
.icon-circle-60 { width: 60px; height: 60px; }

/* Background patterns */
.bg-pattern-dots {
    background-image: radial-gradient(...);
    background-size: 20px 20px;
}
```

### JavaScript Architecture

#### Modular JavaScript
- **Bootstrap utilities**: `src/Assets/bootstrap/src/js/utils.js`
- **Tailwind utilities**: `src/Assets/main/src/js/utils.js`

#### Event Handling
```javascript
// Modern event delegation
document.addEventListener('click', (e) => {
    const action = e.target.getAttribute('data-action');

    switch (action) {
        case 'go-back':
            NavigationUtils.goBack();
            break;
        case 'toggle-theme':
            ThemeUtils.toggleTheme();
            break;
    }
});
```

### Performance Optimizations

#### Cache Headers
```apache
# .htaccess optimizations
# Fonts - 1 year cache
ExpiresByType font/woff2 "access plus 1 year"

# CSS/JS - 1 month cache
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/javascript "access plus 1 month"
```

#### FOUC Prevention
```html
<!-- Synchronous CSS loading -->
<link rel="stylesheet" href="<?= $asset($finalCssUrl) ?>">

<!-- No theme-loading classes needed -->
<body class="bg-gray-50 min-h-screen">
```

### SEO and Accessibility

#### Descriptive Link Text
```html
<!-- Before -->
<a href="/page/about">Learn More</a>

<!-- After -->
<a href="/page/about" aria-label="Learn more about our development approach">
    Learn More About Our Development Approach
</a>
```

#### Complete Meta Tags
```html
<meta name="description" content="Modern PHP framework...">
<meta name="robots" content="index, follow">
<meta name="keywords" content="PHP, Mezzio, Framework...">

<!-- Open Graph -->
<meta property="og:title" content="...">
<meta property="og:description" content="...">

<!-- Twitter Cards -->
<meta property="twitter:card" content="summary_large_image">
```

### Security Benefits

#### CSP Compliance
Templates are now Content Security Policy compliant:
```http
Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'
```

#### XSS Prevention
- No inline JavaScript execution
- All user content properly escaped
- Event handlers use data attributes

## Benefits

1. **Clear Organization** - Templates organized by theme and purpose
2. **Theme-Aware** - Automatic template selection based on current theme
3. **Centralized Paths** - All paths managed via Paths service
4. **Scalable** - Easy to add new themes and modules
5. **Maintainable** - Consistent structure and naming conventions
6. **Performance** - Efficient path resolution via Paths service
7. **Security** - CSP-compliant, no inline CSS/JS
8. **SEO Optimized** - Descriptive links, complete meta tags
9. **Accessible** - ARIA labels, semantic markup
10. **Fast Loading** - Cache optimizations, FOUC prevention
