# Production Deployment Guide

## Overview

This guide covers deploying Minimal Boot to production shared hosting via FTPS with a minimized build for optimal performance and security.

## Prerequisites

- PHP 8.3+ on production server
- Composer installed locally
- FTPS access to shared hosting
- Domain configured to point to `public/` directory

## Deployment Strategy

### 1. Minimized Build Approach

We create a production-ready build that excludes:
- Development dependencies
- Source assets (uncompiled)
- Documentation files
- Test files
- Git history
- IDE configuration

### 2. Build Process

#### Step 1: Create Production Build Directory

```bash
# Create build directory
mkdir -p build/production

# Copy application files
cp -r config build/production/
cp -r public build/production/
cp -r src build/production/
cp -r templates build/production/
cp -r vendor build/production/
cp composer.json build/production/
cp composer.lock build/production/
```

#### Step 2: Install Production Dependencies

```bash
cd build/production

# Install production dependencies only (no dev dependencies)
composer install --no-dev --optimize-autoloader --no-scripts

# Clear any development caches
rm -rf data/cache/*
```

#### Step 3: Optimize Assets

```bash
# Build production assets
cd src/Assets/main
pnpm run build

cd ../bootstrap
pnpm run build

# Return to build directory
cd ../../../build/production
```

#### Step 4: Security Hardening

```bash
# Remove sensitive files
rm -f .env.local
rm -f .env.development
rm -rf .git
rm -rf tests
rm -rf docs
rm -f phpunit.xml
rm -f phpstan.neon
rm -f .gitignore
rm -f .github

# Create production .env
cp .env.production .env
```

## Production Configuration

### 1. Environment Configuration

Create `.env.production`:

```env
# Production Environment
APP_ENV=production
DEBUG=false

# Database Configuration
DB_HOST=your_production_db_host
DB_NAME=your_production_db_name
DB_USER=your_production_db_user
DB_PASS=your_production_db_password

# Security
SESSION_COOKIE_SECURE=true
SESSION_COOKIE_HTTPONLY=true
SESSION_COOKIE_SAMESITE=Strict

# Paths (adjust for shared hosting)
UPLOAD_PATH=/path/to/uploads
LOG_PATH=/path/to/logs

# Performance
OPCACHE_ENABLE=true
CACHE_ENABLE=true
```

### 2. Production Config Overrides

Create `config/autoload/production.local.php`:

```php
<?php

declare(strict_types=1);

return [
    // Disable debugging
    'debug' => false,
    
    // Enable configuration cache
    'config_cache_enabled' => true,
    
    // Production database settings
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'dbname' => $_ENV['DB_NAME'] ?? '',
        'user' => $_ENV['DB_USER'] ?? '',
        'password' => $_ENV['DB_PASS'] ?? '',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
    
    // Production logging
    'log' => [
        'level' => 'error',
        'path' => $_ENV['LOG_PATH'] ?? 'data/logs',
    ],
    
    // Security headers
    'security' => [
        'headers' => [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        ],
    ],
];
```

## FTPS Deployment

### 1. Automated Deployment Script

Create `deploy.sh`:

```bash
#!/bin/bash

# Production Deployment Script for Minimal Boot
set -e

echo "🚀 Starting production deployment..."

# Configuration
BUILD_DIR="build/production"
BACKUP_DIR="backup/$(date +%Y%m%d_%H%M%S)"
FTPS_HOST="your-ftp-host.com"
FTPS_USER="your-ftp-username"
FTPS_PASS="your-ftp-password"
REMOTE_PATH="/public_html"

# Step 1: Clean previous build
echo "🧹 Cleaning previous build..."
rm -rf $BUILD_DIR
mkdir -p $BUILD_DIR

# Step 2: Copy application files
echo "📦 Copying application files..."
cp -r config $BUILD_DIR/
cp -r public $BUILD_DIR/
cp -r src $BUILD_DIR/
cp -r templates $BUILD_DIR/
cp composer.json $BUILD_DIR/
cp composer.lock $BUILD_DIR/
cp .env.production $BUILD_DIR/.env

# Step 3: Install production dependencies
echo "📚 Installing production dependencies..."
cd $BUILD_DIR
composer install --no-dev --optimize-autoloader --no-scripts --quiet

# Step 4: Build assets
echo "🎨 Building production assets..."
cd ../../src/Assets/main
pnpm run build --silent
cd ../bootstrap
pnpm run build --silent
cd ../../../$BUILD_DIR

# Step 5: Security cleanup
echo "🔒 Security hardening..."
rm -rf .git tests docs phpunit.xml phpstan.neon .gitignore .github
find . -name "*.md" -not -path "./vendor/*" -delete
find . -name ".DS_Store" -delete

# Step 6: Create backup of current production
echo "💾 Creating backup..."
mkdir -p ../../$BACKUP_DIR

# Step 7: Deploy via FTPS
echo "🌐 Deploying to production..."
lftp -c "
set ftp:ssl-force true
set ftp:ssl-protect-data true
set ssl:verify-certificate no
open ftps://$FTPS_USER:$FTPS_PASS@$FTPS_HOST
mirror -R --delete --verbose . $REMOTE_PATH
quit
"

echo "✅ Deployment completed successfully!"
echo "🔗 Your application is now live!"

# Cleanup
cd ../../
rm -rf $BUILD_DIR

echo "🧹 Build directory cleaned up."
```

### 2. Manual FTPS Deployment

If you prefer manual deployment:

```bash
# 1. Create production build
./scripts/build-production.sh

# 2. Connect via FTPS client (FileZilla, WinSCP, etc.)
# Host: your-ftp-host.com
# Protocol: FTPS (FTP over TLS)
# Port: 21 (or 990 for implicit FTPS)
# User: your-username
# Password: your-password

# 3. Upload build/production/* to your web root
# Usually: /public_html/ or /www/ or /htdocs/

# 4. Set proper permissions
# Directories: 755
# Files: 644
# public/index.php: 644
```

## Shared Hosting Considerations

### 1. Directory Structure

Most shared hosting expects this structure:
```
/public_html/          # Web root (upload public/* here)
├── index.php         # Main entry point
├── themes/           # Static assets
└── .htaccess         # Apache configuration

/private/             # Private files (upload everything else here)
├── config/
├── src/
├── templates/
├── vendor/
└── composer.json
```

### 2. Modified public/index.php

Update `public/index.php` for shared hosting:

```php
<?php

declare(strict_types=1);

// Adjust paths for shared hosting structure
chdir(dirname(__DIR__) . '/private');

require 'vendor/autoload.php';

// Load configuration
$config = require 'config/config.php';

// Create container
$container = require 'config/container.php';

// Run application
$app = $container->get(\Mezzio\Application::class);
$app->run();
```

### 3. .htaccess Configuration

Create `public/.htaccess`:

```apache
# Production .htaccess for Minimal Boot

# Security Headers
Header always set X-Frame-Options "DENY"
Header always set X-Content-Type-Options "nosniff"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# HTTPS Redirect
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Front Controller
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Deny access to sensitive files
<FilesMatch "\.(env|json|lock|md|xml|neon|yml|yaml)$">
    Require all denied
</FilesMatch>

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
</IfModule>

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

## Performance Optimization

### 1. OPcache Configuration

Add to production config:

```php
// config/autoload/opcache.local.php
return [
    'opcache' => [
        'enable' => true,
        'memory_consumption' => 128,
        'max_accelerated_files' => 4000,
        'revalidate_freq' => 60,
        'fast_shutdown' => true,
    ],
];
```

### 2. Asset Optimization

```bash
# Minify CSS and JS
cd src/Assets/main
pnpm run build:production

# Optimize images
find public/themes -name "*.png" -exec optipng {} \;
find public/themes -name "*.jpg" -exec jpegoptim {} \;
```

## Security Checklist

- [ ] Remove all development files (.git, tests, docs)
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Configure HTTPS redirect in .htaccess
- [ ] Set security headers
- [ ] Use production environment variables
- [ ] Enable OPcache
- [ ] Disable debug mode
- [ ] Configure proper error logging
- [ ] Set secure session cookies
- [ ] Deny access to sensitive files

## Monitoring

### 1. Error Logging

Configure proper error logging in production:

```php
// config/autoload/logging.local.php
return [
    'log' => [
        'level' => 'error',
        'path' => '/path/to/logs',
        'filename' => 'application.log',
        'max_files' => 30,
    ],
];
```

### 2. Health Check

Create `public/health.php`:

```php
<?php
// Simple health check endpoint
http_response_code(200);
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('c'),
    'version' => '1.0.0'
]);
```

## Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check file permissions
   - Verify .htaccess syntax
   - Check error logs

2. **Composer autoload issues**
   - Run `composer dump-autoload --optimize`
   - Verify vendor directory uploaded

3. **Asset loading issues**
   - Check asset paths in templates
   - Verify themes directory uploaded
   - Check .htaccess rewrite rules

4. **Database connection issues**
   - Verify database credentials
   - Check database host/port
   - Ensure database exists

## Rollback Procedure

If deployment fails:

1. Restore from backup
2. Check error logs
3. Fix issues locally
4. Test thoroughly
5. Redeploy

## Conclusion

This deployment strategy ensures:
- ✅ Minimal file size
- ✅ Enhanced security
- ✅ Optimized performance
- ✅ Easy rollback capability
- ✅ Shared hosting compatibility

Your Minimal Boot application is now ready for production deployment!
