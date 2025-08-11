#!/bin/bash

# Minimal Shared Hosting Build Script
# Creates optimized build for shared hosting with minimal requirements

set -e

echo "🚀 Building Minimal Boot for Shared Hosting..."

# Configuration
BUILD_DIR="build/shared-hosting-minimal"
CURRENT_DIR=$(pwd)

# Clean and create build directory
echo "📁 Preparing build directory..."
rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR"

# Copy essential files
echo "📋 Copying essential files..."

# Core application files
cp -r public "$BUILD_DIR/"
cp -r src "$BUILD_DIR/"
cp -r config "$BUILD_DIR/"
cp -r templates "$BUILD_DIR/"
cp composer.json "$BUILD_DIR/"
cp composer.lock "$BUILD_DIR/"

# Create minimal var structure
echo "📂 Creating var structure..."
mkdir -p "$BUILD_DIR/var"/{cache,data,db,logs,sessions,storage,tmp}

# Copy only production vendor dependencies
echo "📦 Installing production dependencies..."
cd "$BUILD_DIR"
composer install --no-dev --optimize-autoloader --no-scripts --quiet

# Remove development files and optimize
echo "🧹 Cleaning up development files..."

# Remove development configs
rm -f config/autoload/development.local.php
rm -f config/autoload/debugbar.local.php.dist
rm -f config/development.config.php.dist

# Remove test files
rm -rf tests/

# Remove documentation
rm -rf docs/

# Remove build tools
rm -rf bin/

# Remove asset source files (keep only built assets)
find src/Assets -name "src" -type d -exec rm -rf {} + 2>/dev/null || true
find src/Assets -name "node_modules" -type d -exec rm -rf {} + 2>/dev/null || true
find src/Assets -name "package.json" -exec rm -f {} + 2>/dev/null || true
find src/Assets -name "package-lock.json" -exec rm -f {} + 2>/dev/null || true
find src/Assets -name "pnpm-lock.yaml" -exec rm -f {} + 2>/dev/null || true
find src/Assets -name "vite.config.js" -exec rm -f {} + 2>/dev/null || true

# Remove unnecessary vendor files
echo "🗑️ Removing unnecessary vendor files..."
find vendor -name "tests" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name "test" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name "docs" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name "examples" -type d -exec rm -rf {} + 2>/dev/null || true
find vendor -name "*.md" -exec rm -f {} + 2>/dev/null || true
find vendor -name "*.txt" -exec rm -f {} + 2>/dev/null || true
find vendor -name "LICENSE*" -exec rm -f {} + 2>/dev/null || true
find vendor -name "CHANGELOG*" -exec rm -f {} + 2>/dev/null || true

# Create optimized .env for shared hosting
echo "⚙️ Creating shared hosting configuration..."
cat > .env << 'EOF'
# Shared Hosting Production Configuration
APP_ENV=production
DEBUG=false

# URLs (update these for your domain)
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com

# Security Settings for HTTPS
SESSION_COOKIE_SECURE=true
SESSION_COOKIE_HTTPONLY=true
SESSION_COOKIE_SAMESITE=Strict

# Session Configuration
SESSION_NAME=minimal_boot_session
SESSION_LIFETIME=3600

# CSRF Protection
CSRF_TOKEN_NAME=csrf_token
CSRF_HEADER_NAME=X-CSRF-Token

# Cache Settings
CACHE_ENABLED=true
CACHE_TTL=3600

# Logging (minimal for shared hosting)
LOG_LEVEL=error
LOG_PATH=var/logs
EOF

# Create optimized .htaccess for shared hosting
echo "🔧 Creating optimized .htaccess..."
cat > public/.htaccess << 'EOF'
# Minimal Boot - Shared Hosting Optimized .htaccess

RewriteEngine On

# Security - Hide sensitive files
<FilesMatch "\.(env|json|lock|md|txt|log)$">
    Require all denied
</FilesMatch>

# Hide var directory
RewriteRule ^var/ - [F,L]

# Font MIME types
AddType font/woff2 .woff2
AddType font/woff .woff

# Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>

# Cache Control - Aggressive caching for static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType text/html "access plus 1 hour"
</IfModule>

# Alternative Cache-Control headers
<IfModule mod_headers.c>
    <FilesMatch "\.(css|js|svg|png|jpg|jpeg)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
    </FilesMatch>
    <FilesMatch "\.html?$">
        Header set Cache-Control "public, max-age=3600"
    </FilesMatch>
    
    # Security headers
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Redirect all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
EOF

# Create minimal robots.txt
cat > public/robots.txt << 'EOF'
User-agent: *
Allow: /

Sitemap: https://yourdomain.com/sitemap.xml
EOF

# Create installation instructions
echo "📖 Creating installation instructions..."
cat > SHARED_HOSTING_INSTALL.md << 'EOF'
# Shared Hosting Installation Guide

## Quick Setup

1. **Upload files**: Upload all files from this directory to your web hosting
2. **Set document root**: Point your domain to the `public/` directory
3. **Update configuration**: Edit `.env` file with your domain
4. **Set permissions**: Ensure `var/` directory is writable (755 or 777)

## Configuration

### Update .env file:
```
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
```

### File Permissions:
```
chmod 755 var/
chmod 755 var/cache/
chmod 755 var/sessions/
chmod 755 var/logs/
```

## Requirements

- PHP 8.1+
- Apache with mod_rewrite
- 64MB memory limit (recommended: 128MB)
- 50MB disk space

## Features Included

- ✅ Svelte theme (optimized)
- ✅ Contact form
- ✅ About page
- ✅ Session management
- ✅ Security headers
- ✅ Aggressive caching
- ✅ HTTPS support
- ✅ Service worker

## File Structure

```
public/          # Document root (point domain here)
├── index.php    # Application entry point
├── .htaccess    # Apache configuration
└── themes/      # Optimized assets

src/             # Application code
config/          # Configuration files
templates/       # Template files
var/             # Writable directory
vendor/          # Dependencies (production only)
```

## Troubleshooting

### 500 Error:
- Check file permissions on var/ directory
- Verify PHP version (8.1+ required)
- Check error logs in var/logs/

### Assets not loading:
- Verify document root points to public/
- Check .htaccess file exists
- Ensure mod_rewrite is enabled

### Contact form not working:
- Check var/sessions/ is writable
- Verify HTTPS is working
- Check session configuration in .env
EOF

# Calculate build size
echo "📊 Build statistics..."
BUILD_SIZE=$(du -sh "$BUILD_DIR" | cut -f1)
VENDOR_SIZE=$(du -sh "$BUILD_DIR/vendor" | cut -f1)
ASSETS_SIZE=$(du -sh "$BUILD_DIR/public/themes" | cut -f1)

echo ""
echo "✅ Shared hosting build completed!"
echo ""
echo "📊 Build Statistics:"
echo "   Total size: $BUILD_SIZE"
echo "   Vendor size: $VENDOR_SIZE"
echo "   Assets size: $ASSETS_SIZE"
echo ""
echo "📁 Build location: $BUILD_DIR"
echo "📖 Installation guide: $BUILD_DIR/SHARED_HOSTING_INSTALL.md"
echo ""
echo "🚀 Ready for shared hosting deployment!"

cd "$CURRENT_DIR"
