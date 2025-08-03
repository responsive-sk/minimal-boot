---
layout: page
title: "Installation"
description: "Complete installation guide for Minimal Boot framework"
nav_order: 1
---

# Installation

This guide will walk you through installing Minimal Boot and setting up your development environment.

## System Requirements

Before installing Minimal Boot, ensure your system meets the following requirements:

- **PHP 8.1 or higher** with the following extensions:
  - `ext-json`
  - `ext-mbstring`
  - `ext-openssl`
  - `ext-pdo` (for database operations)
- **Composer 2.0 or higher**
- **Web server** (Apache, Nginx, or PHP built-in server)

## Installation Methods

### Method 1: Clone from GitHub (Recommended)

```bash
# Clone the repository
git clone https://github.com/responsive-sk/minimal-boot.git
cd minimal-boot

# Install dependencies
composer install

# Set up configuration
cp config/autoload/local.php.dist config/autoload/local.php
```

### Method 2: Composer Create-Project

```bash
# Create new project using Composer
composer create-project responsive-sk/minimal-boot my-project
cd my-project
```

## Configuration

### Environment Setup

1. **Copy configuration files:**
```bash
cp config/autoload/local.php.dist config/autoload/local.php
```

2. **Configure your application** by editing `config/autoload/local.php`:
```php
<?php
declare(strict_types=1);

$baseUrl = 'http://localhost:8080';

return [
    'application' => [
        'url' => $baseUrl,
    ],
    'routes' => [
        'page' => [
            'about' => 'about',
            'who-we-are' => 'who-we-are',
        ],
    ],
];
```

### Database Setup

Minimal Boot uses modular SQLite databases. Create the database directory:

```bash
mkdir -p var/db
chmod 755 var/db
```

The application will automatically create SQLite database files as needed:
- `var/db/page.sqlite` - Page content
- `var/db/contact.sqlite` - Contact forms
- `var/db/auth.sqlite` - User authentication
- `var/db/session.sqlite` - Session storage

### Directory Permissions

Ensure the following directories are writable:

```bash
chmod -R 755 var/
chmod -R 755 public/
```

## Development Server

### Using PHP Built-in Server

The quickest way to get started is using PHP's built-in server:

```bash
php -S localhost:8080 -t public/
```

Your application will be available at `http://localhost:8080`

### Using Apache

Create a virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName minimal-boot.local
    DocumentRoot /path/to/minimal-boot/public
    
    <Directory /path/to/minimal-boot/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Using Nginx

Create a server block configuration:

```nginx
server {
    listen 80;
    server_name minimal-boot.local;
    root /path/to/minimal-boot/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Verification

After installation, verify everything is working:

1. **Visit your application** in a web browser
2. **Check the homepage** - you should see the Minimal Boot welcome page
3. **Test routes:**
   - `/` - Homepage
   - `/demo` - Demo page with TailwindCSS
   - `/page/about` - Sample about page
   - `/contact` - Contact form

## Development Tools

Install development tools for code quality:

```bash
# Run code quality checks
composer check

# Fix code style issues
composer cs-fix

# Run static analysis
composer static-analysis
```

## Troubleshooting

### Common Issues

**Issue: 500 Internal Server Error**
- Check PHP error logs
- Verify file permissions
- Ensure all dependencies are installed

**Issue: Class not found errors**
- Run `composer dump-autoload`
- Check namespace declarations

**Issue: Template not found**
- Verify template paths in `config/autoload/templates.global.php`
- Check file permissions on template directories

### Debug Mode

Debug mode is controlled in `config/autoload/mezzio.global.php`:

```php
// config/autoload/mezzio.global.php
return [
    'debug' => true, // Set to false in production
    ConfigAggregator::ENABLE_CACHE => false, // Disable config cache in development
];
```

For development, you can also copy and modify:
```bash
cp config/autoload/development.local.php.dist config/autoload/development.local.php
```

## Next Steps

- [Getting Started](getting-started.md) - Learn the basics
- [Architecture](architecture.md) - Understand the framework structure
- [Configuration](configuration.md) - Detailed configuration options
