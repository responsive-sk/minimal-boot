# ThemeService Documentation

## Overview

The `ThemeService` is a core service in Minimal Boot that manages theme switching, asset loading, and template selection. It provides a centralized way to handle multiple themes (Bootstrap and Tailwind) with automatic asset resolution.

## Features

- **Multi-theme support** - Bootstrap 5 and Tailwind CSS themes
- **Session-based theme persistence** - User's theme choice is remembered
- **Automatic asset resolution** - CSS and JS URLs based on current theme
- **Template namespace mapping** - Theme-aware template selection
- **Development mode support** - Easy theme switching for development

## Configuration

### Default Theme

The default theme is configured in `ThemeService.php`:

```php
private const DEFAULT_THEME = 'bootstrap';
```

### Available Themes

```php
private const AVAILABLE_THEMES = [
    'bootstrap' => [
        'name' => 'Bootstrap 5',
        'css' => 'themes/bootstrap/assets/main.css',
        'js' => 'themes/bootstrap/assets/main.js',
        'description' => 'Bootstrap 5 with modern components'
    ],
    'tailwind' => [
        'name' => 'Tailwind CSS',
        'css' => 'themes/main/assets/main.css',
        'js' => 'themes/main/assets/main.js',
        'description' => 'Tailwind CSS with Alpine.js'
    ]
];
```

## Usage

### Basic Usage

```php
// Get current theme
$currentTheme = $themeService->getCurrentTheme();

// Set theme
$themeService->setTheme('tailwind');

// Get theme CSS URL
$cssUrl = $themeService->getThemeCssUrl();

// Get theme JS URL
$jsUrl = $themeService->getThemeJsUrl();

// Get all available themes
$themes = $themeService->getAvailableThemes();
```

### In Handlers

```php
class IndexHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly TemplateRendererInterface $template,
        private readonly ThemeService $themeService
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Get current theme for template selection
        $currentTheme = $this->themeService->getCurrentTheme();
        $templateName = $currentTheme . '_pages::home';
        
        return new HtmlResponse(
            $this->template->render($templateName, [
                'title' => 'Home Page',
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
$currentTheme = $this->themeService->getCurrentTheme();
$layout($currentTheme . '_layouts::app', [
    'title' => $title,
    'cssUrl' => $cssUrl,
    'jsUrl' => $jsUrl,
]);
?>
```

## API Reference

### Methods

#### `getCurrentTheme(): string`
Returns the currently active theme name.

```php
$theme = $themeService->getCurrentTheme(); // 'bootstrap' or 'tailwind'
```

#### `setTheme(string $theme): void`
Sets the active theme and stores it in session.

```php
$themeService->setTheme('tailwind');
```

#### `getThemeCssUrl(): string`
Returns the CSS URL for the current theme.

```php
$cssUrl = $themeService->getThemeCssUrl(); // 'themes/main/assets/main.css'
```

#### `getThemeJsUrl(): string`
Returns the JavaScript URL for the current theme.

```php
$jsUrl = $themeService->getThemeJsUrl(); // 'themes/main/assets/main.js'
```

#### `getAvailableThemes(): array`
Returns all available themes with their configuration.

```php
$themes = $themeService->getAvailableThemes();
// Returns:
// [
//     'bootstrap' => ['name' => 'Bootstrap 5', 'css' => '...', ...],
//     'tailwind' => ['name' => 'Tailwind CSS', 'css' => '...', ...]
// ]
```

#### `isValidTheme(string $theme): bool`
Checks if a theme is valid and available.

```php
$isValid = $themeService->isValidTheme('tailwind'); // true
$isValid = $themeService->isValidTheme('invalid'); // false
```

## Theme Switching

### Manual Theme Switch

```php
// In a handler
$themeService->setTheme('tailwind');
```

### Theme Switch Handler

The application includes a theme switch handler at `/theme/switch`:

```php
class ThemeSwitchHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $currentTheme = $this->themeService->getCurrentTheme();
        $newTheme = $currentTheme === 'bootstrap' ? 'tailwind' : 'bootstrap';
        
        $this->themeService->setTheme($newTheme);
        
        // Redirect back to previous page
        $referer = $request->getHeaderLine('Referer') ?: '/';
        return new RedirectResponse($referer);
    }
}
```

## Template Integration

### Theme-Aware Template Selection

```php
// In handlers
$currentTheme = $this->themeService->getCurrentTheme();

// Select theme-specific templates
$layoutTemplate = $currentTheme . '_layouts::app';
$pageTemplate = $currentTheme . '_pages::home';

// Render with theme assets
return new HtmlResponse(
    $this->template->render($pageTemplate, [
        'cssUrl' => $this->themeService->getThemeCssUrl(),
        'jsUrl' => $this->themeService->getThemeJsUrl(),
    ])
);
```

### Template Namespace Mapping

Templates are organized by theme:

```
templates/
├── themes/
│   ├── bootstrap/
│   │   ├── layouts/app.phtml     → bootstrap_layouts::app
│   │   └── pages/home.phtml      → bootstrap_pages::home
│   └── tailwind/
│       ├── layouts/app.phtml     → tailwind_layouts::app
│       └── pages/home.phtml      → tailwind_pages::home
```

## Session Storage

Theme preference is stored in the user's session:

```php
// Session key
private const SESSION_KEY = 'theme';

// Storage
$_SESSION['theme'] = 'tailwind';
```

## Development Mode

### Adding New Themes

1. **Add theme configuration:**

```php
private const AVAILABLE_THEMES = [
    'bootstrap' => [...],
    'tailwind' => [...],
    'custom' => [
        'name' => 'Custom Theme',
        'css' => 'themes/custom/assets/main.css',
        'js' => 'themes/custom/assets/main.js',
        'description' => 'Custom theme description'
    ]
];
```

2. **Create theme assets:**
```
public/themes/custom/
├── assets/
│   ├── main.css
│   └── main.js
```

3. **Create theme templates:**
```
templates/themes/custom/
├── layouts/
│   └── app.phtml
└── pages/
    └── home.phtml
```

4. **Update template configuration:**
```php
// In config/autoload/templates.global.php
'custom_layouts' => ['@templates_custom_layouts'],
'custom_pages' => ['@templates_custom_pages'],
```

## Error Handling

The ThemeService includes validation:

```php
// Invalid theme falls back to default
$themeService->setTheme('invalid'); // Sets to DEFAULT_THEME

// Safe theme retrieval
$theme = $themeService->getCurrentTheme(); // Always returns valid theme
```

## Performance Considerations

- **Session storage** - Minimal overhead for theme persistence
- **Asset caching** - CSS/JS URLs are cached per theme
- **Template caching** - Template paths are resolved efficiently
- **Validation** - Theme validation prevents errors

## Best Practices

1. **Always use ThemeService** for theme-related operations
2. **Pass CSS/JS URLs** to templates for proper asset loading
3. **Use theme-aware templates** for consistent styling
4. **Validate themes** before setting them
5. **Handle fallbacks** gracefully for invalid themes

## Integration with Other Services

### With TemplateRenderer

```php
$templateName = $this->themeService->getCurrentTheme() . '_pages::home';
$html = $this->template->render($templateName, $data);
```

### With Asset Helper

```php
$cssUrl = $this->themeService->getThemeCssUrl();
$fullCssUrl = $this->asset($cssUrl);
```

### With Middleware

```php
// Theme detection middleware
$theme = $request->getQueryParams()['theme'] ?? null;
if ($theme && $this->themeService->isValidTheme($theme)) {
    $this->themeService->setTheme($theme);
}
```

## Troubleshooting

### Common Issues

1. **Theme not switching**
   - Check session configuration
   - Verify theme is valid
   - Clear browser cache

2. **Assets not loading**
   - Verify asset paths in theme configuration
   - Check file permissions
   - Ensure assets are built

3. **Template not found**
   - Check template namespace configuration
   - Verify template files exist
   - Check template path mapping

### Debug Information

```php
// Get current theme info
$currentTheme = $themeService->getCurrentTheme();
$themes = $themeService->getAvailableThemes();
$cssUrl = $themeService->getThemeCssUrl();
$jsUrl = $themeService->getThemeJsUrl();

var_dump([
    'current_theme' => $currentTheme,
    'available_themes' => array_keys($themes),
    'css_url' => $cssUrl,
    'js_url' => $jsUrl
]);
```

## Conclusion

The ThemeService provides a robust, flexible system for managing multiple themes in Minimal Boot. It handles theme switching, asset resolution, and template selection automatically, making it easy to create applications with multiple visual themes.
