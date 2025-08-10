# 🔧 Production Troubleshooting Guide

## 🚨 Common Production Issues

### 1. **"Failed opening required 'config/container.php'"**

**Symptom:**
```
PHP Fatal error: Uncaught Error: Failed opening required 'config/container.php'
```

**Cause:** Path resolution issues on shared hosting or when document root is set to `/public`

**Solution:**
✅ **Fixed in latest version** - `public/index.php` now uses absolute paths

**Manual Fix (if needed):**
```php
// In public/index.php, ensure these lines exist:
$appRoot = dirname(__DIR__);
chdir($appRoot);

// And use absolute paths:
$container = require $appRoot . '/config/container.php';
$pipeline = require $appRoot . '/config/pipeline.php';
```

### 2. **Session Not Working**

**Symptoms:**
- Theme switching doesn't persist
- User login doesn't work
- Session data disappears

**Solutions:**

**A. Check Session Directory Permissions:**
```bash
chmod 755 var/sessions
chown www-data:www-data var/sessions  # or apache:apache
```

**B. Verify .env Configuration:**
```env
SESSION_COOKIE_SECURE=true  # for HTTPS
SESSION_COOKIE_SECURE=false # for HTTP (development only)
SESSION_COOKIE_SAMESITE=Strict
SESSION_NAME=minimal_boot_session
SESSION_LIFETIME=3600
```

**C. Check PHP Session Settings:**
```php
// Add to public/index.php for debugging
error_log("Session save path: " . session_save_path());
error_log("Session name: " . session_name());
```

### 3. **Database Connection Issues**

**Symptoms:**
- "Database file not found"
- "Permission denied" errors
- SQLite connection failures

**Solutions:**

**A. Check Database Directory:**
```bash
mkdir -p var/db
chmod 755 var/db
chown www-data:www-data var/db
```

**B. Verify Database Files:**
```bash
ls -la var/db/
# Should show: page.sqlite, user.sqlite, etc.
```

**C. Check SQLite Extension:**
```bash
php -m | grep sqlite
# Should show: pdo_sqlite, sqlite3
```

### 4. **File Permission Issues**

**Symptoms:**
- "Permission denied" errors
- Cannot write to cache/logs
- Upload failures

**Solution:**
```bash
# Set proper permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Make var/ writable
chmod -R 755 var/
chown -R www-data:www-data var/  # or apache:apache

# Specific directories
chmod 755 var/cache var/logs var/sessions var/uploads
```

### 5. **Environment Variables Not Loading**

**Symptoms:**
- Default values used instead of .env
- Configuration not applied
- $_ENV variables empty

**Solutions:**

**A. Check .env File Exists:**
```bash
ls -la .env
# Should exist and be readable
```

**B. Verify .env Loading in index.php:**
```php
// Should be in public/index.php
if (file_exists($appRoot . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($appRoot);
    $dotenv->load();
}
```

**C. Test Environment Loading:**
```php
// Add temporary debug code
var_dump($_ENV['APP_ENV'] ?? 'NOT_SET');
var_dump($_ENV['SESSION_NAME'] ?? 'NOT_SET');
```

### 6. **Theme Assets Not Loading**

**Symptoms:**
- CSS/JS files return 404
- Fonts not loading
- Images missing

**Solutions:**

**A. Check Asset Paths:**
```bash
ls -la public/themes/
# Should contain theme directories
```

**B. Verify .htaccess:**
```apache
# Ensure this exists in public/.htaccess
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} -s [OR]
RewriteCond %{REQUEST_FILENAME} -l [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^.*$ - [NC,L]
```

**C. Check Web Server Configuration:**
- Apache: Ensure mod_rewrite is enabled
- Nginx: Configure proper try_files directive

### 7. **Memory or Performance Issues**

**Symptoms:**
- Slow page loads
- Memory limit exceeded
- Timeouts

**Solutions:**

**A. Enable OPcache:**
```ini
; In php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
```

**B. Optimize Composer:**
```bash
composer install --no-dev --optimize-autoloader
composer dump-autoload --optimize --no-dev
```

**C. Clear Caches:**
```bash
rm -rf var/cache/*
```

## 🔍 Debugging Steps

### 1. **Enable Error Reporting**
```php
// Add to public/index.php temporarily
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

### 2. **Check Error Logs**
```bash
# Common log locations
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log
tail -f var/logs/app.log
```

### 3. **Test Basic Functionality**
```bash
# Test PHP syntax
php -l public/index.php

# Test application bootstrap
php -f public/index.php

# Test specific components
php -r "require 'vendor/autoload.php'; echo 'Autoloader OK';"
```

## 📞 Getting Help

If you're still experiencing issues:

1. **Check the error logs** for specific error messages
2. **Verify file permissions** are correct
3. **Ensure all dependencies** are installed
4. **Test on a local environment** first
5. **Contact your hosting provider** for server-specific issues

## 🚀 Production Checklist

- [ ] .env file configured with production values
- [ ] File permissions set correctly (644 for files, 755 for directories)
- [ ] var/ directories writable by web server
- [ ] Database files exist and are accessible
- [ ] PHP extensions installed (pdo_sqlite, mbstring, etc.)
- [ ] Web server configured properly (Apache/Nginx)
- [ ] HTTPS enabled and SSL certificate valid
- [ ] Error reporting disabled in production
- [ ] OPcache enabled for performance
- [ ] Backups configured

**Your Minimal Boot application should now run smoothly in production!** 🎉
