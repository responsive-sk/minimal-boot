---
layout: page
title: "Assets & Frontend"
description: "Frontend asset management with Bootstrap and TailwindCSS build systems"
nav_order: 7
---

# Assets & Frontend

Minimal Boot includes two modern frontend build systems for different development preferences: Bootstrap 5 with Vite and TailwindCSS with Alpine.js and Vite.

## Asset Structure

```
src/Assets/
├── bootstrap/         # Bootstrap 5 + Vite build system
│   ├── package.json   # Dependencies and scripts
│   ├── vite.config.js # Vite configuration
│   └── src/
│       ├── main.js    # JavaScript entry point
│       └── style.css  # CSS entry point
└── main/              # TailwindCSS + Alpine.js + Vite
    ├── package.json   # Dependencies and scripts
    ├── vite.config.js # Vite configuration
    ├── tailwind.config.js # TailwindCSS configuration
    ├── postcss.config.js  # PostCSS configuration
    └── src/
        ├── main.js    # JavaScript entry point
        ├── style.css  # CSS entry point
        └── images/    # Image assets
```

## Bootstrap Build System

### Features

- **Bootstrap 5.3.0** - Latest version with modern components
- **Vite** - Fast build tool with hot module replacement
- **Sass Support** - For custom Bootstrap theming
- **JavaScript Modules** - Modern ES6+ JavaScript
- **Production Optimization** - Minification and tree-shaking

### Setup

Navigate to the Bootstrap assets directory:

```bash
cd src/Assets/bootstrap
```

Install dependencies:

```bash
# Using npm
npm install

# Using pnpm (recommended)
pnpm install

# Using yarn
yarn install
```

### Development

Start the development server with hot reload:

```bash
# Development mode
npm run dev
# or
pnpm dev
```

This will:
- Start Vite development server
- Enable hot module replacement
- Watch for file changes
- Serve assets at `http://localhost:5173`

### Production Build

Build optimized assets for production:

```bash
# Production build
npm run build
# or
pnpm build
```

Output files will be generated in `public/themes/bootstrap/assets/`:
- `bootstrap.css` - Compiled and minified CSS
- `bootstrap.js` - Bundled and minified JavaScript

### Configuration

#### Vite Configuration (`vite.config.js`)

```javascript
import { defineConfig } from 'vite'

export default defineConfig({
  build: {
    outDir: '../../../public/themes/bootstrap/assets',
    rollupOptions: {
      input: {
        bootstrap: './src/main.js'
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: '[name].js',
        assetFileNames: '[name].[ext]'
      }
    }
  }
})
```

#### Package.json Scripts

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  }
}
```

### Customization

#### Custom Bootstrap Theme

Edit `src/style.css` to customize Bootstrap:

```css
/* Custom Bootstrap variables */
:root {
  --bs-primary: #007bff;
  --bs-secondary: #6c757d;
  /* Add your custom variables */
}

/* Import Bootstrap */
@import 'bootstrap/scss/bootstrap';

/* Custom styles */
.custom-component {
  /* Your custom styles */
}
```

#### JavaScript Customization

Edit `src/main.js` to add custom JavaScript:

```javascript
// Import Bootstrap JavaScript
import 'bootstrap'

// Custom JavaScript
document.addEventListener('DOMContentLoaded', function() {
  // Your custom code
  console.log('Bootstrap theme loaded')
})
```

## TailwindCSS Build System

### Features

- **TailwindCSS 3.3.0** - Utility-first CSS framework
- **Alpine.js 3.x** - Lightweight reactive framework
- **Vite** - Fast build tool with hot module replacement
- **PostCSS** - CSS processing with plugins
- **Image Optimization** - Automatic image processing
- **Production Optimization** - PurgeCSS and minification

### Setup

Navigate to the TailwindCSS assets directory:

```bash
cd src/Assets/main
```

Install dependencies:

```bash
# Using pnpm (recommended)
pnpm install

# Using npm
npm install

# Using yarn
yarn install
```

### Development

Start the development server:

```bash
# Development mode
pnpm dev
# or
npm run dev
```

This will:
- Start Vite development server
- Enable TailwindCSS JIT compilation
- Watch for file changes
- Serve assets with hot reload

### Production Build

Build optimized assets:

```bash
# Production build
pnpm build
# or
npm run build
```

Output files will be generated in `public/themes/main/assets/`:
- `main.css` - Compiled TailwindCSS with purged unused styles
- `main.js` - Bundled JavaScript with Alpine.js

### Configuration

#### TailwindCSS Configuration (`tailwind.config.js`)

```javascript
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{html,js}",
    "../../../src/**/*.phtml",
    "../../../src/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          500: '#3b82f6',
          900: '#1e3a8a',
        }
      }
    },
  },
  plugins: [],
}
```

#### PostCSS Configuration (`postcss.config.js`)

```javascript
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
```

### Customization

#### Custom TailwindCSS Styles

Edit `src/style.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom component classes */
@layer components {
  .btn-primary {
    @apply bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded;
  }
}

/* Custom utility classes */
@layer utilities {
  .text-shadow {
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
  }
}
```

#### Alpine.js Components

Edit `src/main.js` to add Alpine.js components:

```javascript
import Alpine from 'alpinejs'

// Custom Alpine.js components
Alpine.data('dropdown', () => ({
  open: false,
  toggle() {
    this.open = !this.open
  }
}))

// Start Alpine.js
Alpine.start()
```

## Demo Pages

### Bootstrap Demo

Visit `/demo/bootstrap` to see Bootstrap 5 components in action:

- **Responsive Grid System**
- **Navigation Components**
- **Cards and Layouts**
- **Forms and Inputs**
- **Buttons and Alerts**
- **Interactive Components**

### TailwindCSS Demo

Visit `/demo` to see TailwindCSS + Alpine.js components:

- **Utility-First Styling**
- **Responsive Design**
- **Interactive Components**
- **Custom Animations**
- **Modern Layout Techniques**

## Layout Integration

### Using Bootstrap Layout

```php
<?php
$layout('layout::bootstrap', [
    'title' => 'Page Title',
    'description' => 'Page description',
    'cssUrl' => '/themes/bootstrap/assets/bootstrap.css',
    'jsUrl' => '/themes/bootstrap/assets/bootstrap.js',
]);
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <!-- Bootstrap components -->
        </div>
    </div>
</div>
```

### Using TailwindCSS Layout

```php
<?php
$layout('layout::tailwind', [
    'title' => 'Page Title',
    'description' => 'Page description',
    'cssUrl' => '/themes/main/assets/main.css',
    'jsUrl' => '/themes/main/assets/main.js',
]);
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- TailwindCSS components -->
    </div>
</div>
```

## Production Deployment

### Build Process

1. **Build Bootstrap assets:**
```bash
cd src/Assets/bootstrap
pnpm build
```

2. **Build TailwindCSS assets:**
```bash
cd src/Assets/main
pnpm build
```

3. **Verify output:**
```bash
ls -la public/themes/*/assets/
```

### Optimization

Both build systems include:
- **CSS Minification** - Reduced file sizes
- **JavaScript Bundling** - Optimized module loading
- **Tree Shaking** - Unused code removal
- **Asset Hashing** - Cache busting (optional)
- **Gzip Compression** - Server-level compression

### CDN Integration

For production, consider using CDN for faster delivery:

```php
// In production configuration
'assets' => [
    'cdn_url' => 'https://cdn.example.com',
    'version' => '1.0.0',
],
```

## Best Practices

### Development Workflow

1. **Choose Your Stack** - Bootstrap for rapid prototyping, TailwindCSS for custom designs
2. **Use Development Mode** - Hot reload speeds up development
3. **Component-First** - Build reusable components
4. **Responsive Design** - Test on multiple screen sizes
5. **Performance** - Optimize images and minimize CSS/JS

### File Organization

- Keep source files in `src/` directories
- Use meaningful component names
- Organize styles by component or page
- Document custom utilities and components

### Version Control

- **Include source files** in version control
- **Exclude node_modules** and build outputs
- **Include package-lock.json** for reproducible builds
- **Tag releases** with version numbers

## Troubleshooting

### Common Issues

**Build fails with missing dependencies:**
```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
```

**Styles not updating:**
```bash
# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

**Production build issues:**
```bash
# Check build output
npm run build -- --debug
```

### Performance Tips

- Use PurgeCSS to remove unused styles
- Optimize images before including them
- Use CSS custom properties for theming
- Minimize JavaScript bundle size
- Enable gzip compression on server

## Next Steps

- [Templates](templates.md) - Learn about template integration
- [Development](development.md) - Development workflow
- [Deployment](deployment.md) - Production deployment
- [Configuration](configuration.md) - Advanced configuration
