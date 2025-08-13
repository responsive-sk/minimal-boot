#!/bin/bash

# Create Production Build Script for Minimal Boot
# Usage: ./scripts/create-production-build.sh [port]

set -e

PORT=${1:-8082}
BUILD_DIR="build/test-production-$(date +%Y%m%d-%H%M%S)"

echo "🚀 Creating production build in: $BUILD_DIR"

# Create build directory
mkdir -p "$BUILD_DIR"

# Copy all files except development-specific ones
rsync -av --exclude='node_modules' \
          --exclude='.git' \
          --exclude='var/cache/*' \
          --exclude='var/sessions/*' \
          --exclude='var/tmp/*' \
          --exclude='var/logs/*' \
          --exclude='build/' \
          --exclude='.env.local' \
          . "$BUILD_DIR/"

cd "$BUILD_DIR"

echo "📦 Setting up production environment..."

# Disable development mode
composer development-disable 2>/dev/null || echo "Development mode already disabled"

# Create production .env
cp .env .env.development.backup

# Update .env for production
sed -i 's/APP_ENV=development/APP_ENV=production/' .env
sed -i 's/DEBUG=true/DEBUG=false/' .env
sed -i 's/SESSION_COOKIE_SECURE=false/SESSION_COOKIE_SECURE=true/' .env
sed -i 's/SESSION_COOKIE_SAMESITE=Lax/SESSION_COOKIE_SAMESITE=Strict/' .env
sed -i 's/OPCACHE_ENABLE=false/OPCACHE_ENABLE=true/' .env
sed -i 's/CACHE_ENABLE=false/CACHE_ENABLE=true/' .env
sed -i 's/LOG_LEVEL=debug/LOG_LEVEL=error/' .env
sed -i "s|APP_URL=http://localhost:8080|APP_URL=http://localhost:$PORT|" .env
sed -i "s|ASSET_URL=http://localhost:8080|ASSET_URL=http://localhost:$PORT|" .env

# Create necessary directories
mkdir -p var/cache var/sessions var/tmp var/logs

# Set permissions
chmod -R 755 var/
chmod -R 777 var/cache var/sessions var/tmp var/logs

echo "✅ Production build created successfully!"
echo ""
echo "🌐 To start the production server:"
echo "   cd $BUILD_DIR"
echo "   php -S localhost:$PORT -t public/"
echo ""
echo "🔗 Then visit: http://localhost:$PORT"
