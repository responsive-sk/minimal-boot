# Template System Refactor Plan

## Current Problems
- Multiple layout locations (4 different places)
- Duplicate layouts (default.phtml, main.phtml, bootstrap.phtml, tailwind.phtml)
- Inconsistent namespace usage
- Complex configuration
- Scattered template files

## Target Structure

```
templates/
├── themes/
│   ├── bootstrap/
│   │   ├── layouts/
│   │   │   ├── app.phtml          # Main layout
│   │   │   ├── auth.phtml         # Auth layout
│   │   │   └── admin.phtml        # Admin layout
│   │   ├── pages/
│   │   │   ├── home.phtml
│   │   │   ├── about.phtml
│   │   │   └── demo.phtml
│   │   └── partials/
│   │       ├── header.phtml
│   │       ├── footer.phtml
│   │       └── navigation.phtml
│   └── tailwind/
│       ├── layouts/
│       │   ├── app.phtml          # Main layout
│       │   ├── auth.phtml         # Auth layout
│       │   └── admin.phtml        # Admin layout
│       ├── pages/
│       │   ├── home.phtml
│       │   ├── about.phtml
│       │   └── demo.phtml
│       └── partials/
│           ├── header.phtml
│           ├── footer.phtml
│           └── navigation.phtml
├── modules/
│   ├── auth/
│   ├── contact/
│   ├── user/
│   └── page/
├── shared/
│   ├── error/
│   └── email/
└── components/
    ├── forms/
    └── ui/
```

## Implementation Steps

### Phase 1: Preparation
1. Create new directory structure
2. Backup existing templates
3. Analyze dependencies

### Phase 2: Migration
1. Move templates to new structure
2. Update template configuration
3. Update handlers to use new paths

### Phase 3: Cleanup
1. Remove old template files
2. Update documentation
3. Test all pages

### Phase 4: Enhancement
1. Add theme-aware template service
2. Implement template inheritance
3. Add component system

## Benefits
- Clear separation by theme
- Consistent naming convention
- Easier maintenance
- Better organization
- Simplified configuration

## Migration Commands

```bash
# Create new structure
mkdir -p templates/themes/{bootstrap,tailwind}/{layouts,pages,partials}
mkdir -p templates/modules/{auth,contact,user,page}
mkdir -p templates/shared/{error,email}
mkdir -p templates/components/{forms,ui}

# Move existing templates
# (detailed commands will be provided in implementation)
```

## Configuration Changes

```php
// New templates.global.php
return [
    'templates' => [
        'extension' => 'phtml',
        'paths' => [
            'bootstrap' => ['templates/themes/bootstrap'],
            'tailwind' => ['templates/themes/tailwind'],
            'auth' => ['templates/modules/auth'],
            'contact' => ['templates/modules/contact'],
            'user' => ['templates/modules/user'],
            'page' => ['templates/modules/page'],
            'shared' => ['templates/shared'],
            'components' => ['templates/components'],
        ]
    ]
];
```

## Handler Updates

```php
// Old way:
$layout('layout::default', $data);

// New way:
$layout($this->themeService->getLayoutTemplate('app'), $data);
```
