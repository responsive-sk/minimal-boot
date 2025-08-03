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

## Migration from Old System

The old template system used scattered templates in module directories:
- `src/Page/templates/` → `templates/themes/{theme}/pages/` or `templates/modules/page/`
- `src/Shared/templates/` → `templates/themes/{theme}/layouts/` or `templates/shared/`
- `src/Auth/templates/` → `templates/modules/auth/`

All old template directories have been removed and templates migrated to the new organized structure.

## Benefits

1. **Clear Organization** - Templates organized by theme and purpose
2. **Theme-Aware** - Automatic template selection based on current theme
3. **Centralized Paths** - All paths managed via Paths service
4. **Scalable** - Easy to add new themes and modules
5. **Maintainable** - Consistent structure and naming conventions
6. **Performance** - Efficient path resolution via Paths service
