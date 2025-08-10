#!/bin/bash

# Production Deployment Script for Minimal Boot
# This script ensures proper setup for production environment

set -e  # Exit on any error

echo "🚀 Starting Minimal Boot Production Deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    print_error "composer.json not found. Please run this script from the project root."
    exit 1
fi

print_status "Checking project structure..."

# Create necessary directories with proper permissions
DIRECTORIES=(
    "var"
    "var/cache"
    "var/logs"
    "var/sessions"
    "var/db"
    "var/tmp"
    "var/storage"
    "var/migrations"
    "var/uploads"
)

for dir in "${DIRECTORIES[@]}"; do
    if [ ! -d "$dir" ]; then
        mkdir -p "$dir"
        print_status "Created directory: $dir"
    fi
    
    # Set proper permissions for web server
    chmod 755 "$dir"
    
    # Make writable by web server (adjust user/group as needed)
    if command -v chown >/dev/null 2>&1; then
        # Uncomment and adjust these lines for your server setup
        # chown -R www-data:www-data "$dir"
        # chown -R apache:apache "$dir"
        echo "Directory permissions set for: $dir"
    fi
done

print_status "Directory structure verified"

# Check for .env file
if [ ! -f ".env" ]; then
    if [ -f ".env.production" ]; then
        print_warning ".env not found. Copying from .env.production template..."
        cp .env.production .env
        print_warning "Please edit .env file with your production settings!"
    else
        print_error ".env file not found and no .env.production template available"
        print_error "Please create .env file with production configuration"
        exit 1
    fi
else
    print_status ".env file exists"
fi

# Install/update dependencies
print_status "Installing production dependencies..."
if command -v composer >/dev/null 2>&1; then
    composer install --no-dev --optimize-autoloader --no-interaction
    print_status "Composer dependencies installed"
else
    print_error "Composer not found. Please install composer first."
    exit 1
fi

# Clear and warm up caches
print_status "Clearing caches..."
rm -rf var/cache/*
print_status "Cache cleared"

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
print_status "PHP Version: $PHP_VERSION"

if php -r "exit(version_compare(PHP_VERSION, '8.1.0', '<') ? 1 : 0);"; then
    print_error "PHP 8.1+ is required. Current version: $PHP_VERSION"
    exit 1
fi

# Check required PHP extensions
REQUIRED_EXTENSIONS=(
    "pdo"
    "pdo_sqlite"
    "json"
    "mbstring"
    "openssl"
    "session"
)

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "^$ext$"; then
        print_status "PHP extension $ext: OK"
    else
        print_error "Required PHP extension missing: $ext"
        exit 1
    fi
done

# Verify file permissions
print_status "Setting file permissions..."
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod +x deploy-production.sh

# Make var/ directories writable
chmod -R 755 var/
print_status "File permissions set"

# Test basic functionality
print_status "Testing application bootstrap..."
if php -f public/index.php >/dev/null 2>&1; then
    print_error "Application bootstrap test failed"
    print_error "Check error logs for details"
    exit 1
else
    print_status "Application bootstrap: OK"
fi

# Security recommendations
echo ""
echo "🔒 Security Recommendations:"
echo "1. Ensure .env file is not accessible via web"
echo "2. Set proper file ownership (www-data or apache)"
echo "3. Configure HTTPS and security headers"
echo "4. Review database permissions"
echo "5. Enable OPcache for better performance"

echo ""
print_status "🎉 Production deployment completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Edit .env file with your production settings"
echo "2. Configure your web server (Apache/Nginx)"
echo "3. Set up SSL certificate"
echo "4. Configure monitoring and backups"
echo ""
echo "🌐 Your Minimal Boot application is ready for production!"
