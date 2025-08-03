#!/bin/bash

# Production Build Script for Minimal Boot
# Creates a minimized, production-ready build

set -e

echo "🚀 Building production version of Minimal Boot..."

# Configuration
BUILD_DIR="build/production"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

# Step 1: Clean previous build
log_info "Cleaning previous build..."
if [ -d "$BUILD_DIR" ]; then
    rm -rf "$BUILD_DIR"
fi
mkdir -p "$BUILD_DIR"
log_success "Build directory cleaned"

# Step 2: Copy essential application files
log_info "Copying application files..."
cp -r config "$BUILD_DIR/"
cp -r public "$BUILD_DIR/"
cp -r src "$BUILD_DIR/"
cp -r templates "$BUILD_DIR/"
cp composer.json "$BUILD_DIR/"
cp composer.lock "$BUILD_DIR/"

# Copy database and var directory if they exist
if [ -d "data" ]; then
    cp -r data "$BUILD_DIR/"
    log_success "Database directory copied"
fi

if [ -d "var" ]; then
    cp -r var "$BUILD_DIR/"
    log_success "Var directory copied"
else
    # Create var directory structure for production
    mkdir -p "$BUILD_DIR/var/cache"
    mkdir -p "$BUILD_DIR/var/log"
    mkdir -p "$BUILD_DIR/var/sessions"
    mkdir -p "$BUILD_DIR/var/tmp"
    mkdir -p "$BUILD_DIR/var/uploads"
    log_success "Var directory structure created"
fi

# Ensure data directory exists for SQLite
if [ ! -d "$BUILD_DIR/data" ]; then
    mkdir -p "$BUILD_DIR/data"
    log_success "Data directory created"
fi

# Copy environment template
if [ -f ".env.production" ]; then
    cp .env.production "$BUILD_DIR/.env"
    log_success "Production environment copied"
else
    log_warning "No .env.production found, creating template..."
    cat > "$BUILD_DIR/.env" << 'EOF'
# Production Environment
APP_ENV=production
DEBUG=false

# Database Configuration
DB_HOST=localhost
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASS=your_database_password

# Security
SESSION_COOKIE_SECURE=true
SESSION_COOKIE_HTTPONLY=true
SESSION_COOKIE_SAMESITE=Strict

# Performance
OPCACHE_ENABLE=true
CACHE_ENABLE=true
EOF
    log_warning "Please update $BUILD_DIR/.env with your production settings"
fi

log_success "Application files copied"

# Step 3: Install production dependencies
log_info "Installing production dependencies..."
ORIGINAL_DIR=$(pwd)
cd "$BUILD_DIR"
composer install --no-dev --optimize-autoloader --no-scripts --quiet
if [ $? -eq 0 ]; then
    log_success "Production dependencies installed"
else
    log_error "Failed to install dependencies"
    exit 1
fi
cd "$ORIGINAL_DIR"

# Step 4: Build production assets
log_info "Building production assets..."

# Build Tailwind assets
if [ -d "src/Assets/main" ]; then
    cd src/Assets/main
    if [ -f "package.json" ]; then
        log_info "Building Tailwind assets..."
        if command -v pnpm &> /dev/null; then
            pnpm run build > /dev/null 2>&1
        elif command -v npm &> /dev/null; then
            npm run build > /dev/null 2>&1
        else
            log_warning "No package manager found, skipping asset build"
        fi
        log_success "Tailwind assets built"
    fi
    cd "$ORIGINAL_DIR"
fi

# Build Bootstrap assets
if [ -d "src/Assets/bootstrap" ]; then
    cd src/Assets/bootstrap
    if [ -f "package.json" ]; then
        log_info "Building Bootstrap assets..."
        if command -v pnpm &> /dev/null; then
            pnpm run build > /dev/null 2>&1
        elif command -v npm &> /dev/null; then
            npm run build > /dev/null 2>&1
        else
            log_warning "No package manager found, skipping asset build"
        fi
        log_success "Bootstrap assets built"
    fi
    cd "$ORIGINAL_DIR"
fi

# Step 5: Security hardening and cleanup
log_info "Security hardening and cleanup..."
cd "$ORIGINAL_DIR/$BUILD_DIR"

# Remove development and sensitive files
rm -rf .git 2>/dev/null || true
rm -rf tests 2>/dev/null || true
rm -rf docs 2>/dev/null || true
rm -f phpunit.xml 2>/dev/null || true
rm -f phpstan.neon 2>/dev/null || true
rm -f .gitignore 2>/dev/null || true
rm -rf .github 2>/dev/null || true
rm -f .env.local 2>/dev/null || true
rm -f .env.development 2>/dev/null || true

# Remove documentation files (except vendor)
find . -name "*.md" -not -path "./vendor/*" -delete 2>/dev/null || true
find . -name ".DS_Store" -delete 2>/dev/null || true
find . -name "Thumbs.db" -delete 2>/dev/null || true

# Remove source asset files and development dependencies (keep only built assets)
log_info "Removing development files and node_modules..."
rm -rf src/Assets/*/node_modules 2>/dev/null || true
rm -rf src/Assets/*/src 2>/dev/null || true
rm -f src/Assets/*/package*.json 2>/dev/null || true
rm -f src/Assets/*/pnpm-lock.yaml 2>/dev/null || true
rm -f src/Assets/*/yarn.lock 2>/dev/null || true
rm -f src/Assets/*/package-lock.json 2>/dev/null || true
rm -f src/Assets/*/vite.config.js 2>/dev/null || true
rm -f src/Assets/*/tailwind.config.js 2>/dev/null || true
rm -f src/Assets/*/postcss.config.js 2>/dev/null || true
rm -rf src/Assets/*/.vite 2>/dev/null || true
rm -rf src/Assets/*/dist 2>/dev/null || true

# Remove any remaining node_modules directories
find . -name "node_modules" -type d -exec rm -rf {} + 2>/dev/null || true
find . -name ".pnpm-store" -type d -exec rm -rf {} + 2>/dev/null || true

log_success "Development files and node_modules removed"

# Ensure proper permissions for var directory
if [ -d "var" ]; then
    chmod -R 755 var/
    chmod -R 777 var/cache 2>/dev/null || true
    chmod -R 777 var/log 2>/dev/null || true
    chmod -R 777 var/sessions 2>/dev/null || true
    chmod -R 777 var/tmp 2>/dev/null || true
    chmod -R 777 var/uploads 2>/dev/null || true
fi

# Ensure proper permissions for data directory
if [ -d "data" ]; then
    chmod -R 755 data/
    chmod 666 data/*.db 2>/dev/null || true
    chmod -R 777 data/cache 2>/dev/null || true
    chmod -R 777 data/logs 2>/dev/null || true
fi

log_success "Security hardening completed"

# Step 6: Copy production .htaccess
log_info "Setting up production .htaccess..."
if [ -f "../../../public/.htaccess.production" ]; then
    cp "../../../public/.htaccess.production" "public/.htaccess"
    log_success "Production .htaccess copied with optimized cache headers"
else
    log_warning "Production .htaccess not found, using default"
fi

cd "$ORIGINAL_DIR"

# Step 6: Create deployment info
log_info "Creating deployment info..."
cat > "$BUILD_DIR/DEPLOYMENT_INFO.txt" << EOF
Minimal Boot Production Build
============================

Build Date: $(date)
Build Version: $TIMESTAMP
Git Commit: $(git rev-parse HEAD 2>/dev/null || echo "N/A")
Git Branch: $(git branch --show-current 2>/dev/null || echo "N/A")

Deployment Instructions:
1. Upload all files to your web server
2. Point your domain to the 'public' directory
3. Update .env with your production settings
4. Set proper file permissions (644 for files, 755 for directories)
5. Ensure your web server has PHP 8.3+ with required extensions

Required PHP Extensions:
- json
- mbstring
- openssl
- pdo
- pdo_mysql (or your database driver)
- session
- xml

For detailed deployment instructions, see:
https://github.com/responsive-sk/minimal-boot/blob/main/docs/DEPLOYMENT.md
EOF

# Step 7: Calculate build size and show breakdown
log_info "Calculating build size..."
BUILD_SIZE=$(du -sh "$BUILD_DIR" | cut -f1)

# Show size breakdown
echo ""
log_info "Build size breakdown:"
echo "  Total build size: $BUILD_SIZE"
echo "  Vendor dependencies: $(du -sh "$BUILD_DIR/vendor" 2>/dev/null | cut -f1 || echo 'N/A')"
echo "  Application code: $(du -sh "$BUILD_DIR/src" 2>/dev/null | cut -f1 || echo 'N/A')"
echo "  Templates: $(du -sh "$BUILD_DIR/templates" 2>/dev/null | cut -f1 || echo 'N/A')"
echo "  Public assets: $(du -sh "$BUILD_DIR/public" 2>/dev/null | cut -f1 || echo 'N/A')"
echo "  Configuration: $(du -sh "$BUILD_DIR/config" 2>/dev/null | cut -f1 || echo 'N/A')"

# Check for any remaining large files/directories
echo ""
log_info "Checking for large files (>1MB)..."
find "$BUILD_DIR" -type f -size +1M -exec ls -lh {} \; 2>/dev/null | head -10 || echo "  No large files found"

log_success "Production build completed!"

echo ""
echo "📊 Build Summary:"
echo "=================="
echo "Build directory: $BUILD_DIR"
echo "Build size: $BUILD_SIZE"
echo "Build timestamp: $TIMESTAMP"
echo ""
echo "🚀 Ready for deployment!"
echo ""
echo "Next steps:"
echo "1. Review $BUILD_DIR/.env and update with production settings"
echo "2. Test the build locally if needed"
echo "3. Deploy to production using FTPS or your preferred method"
echo ""
echo "For FTPS deployment, use: bin/deploy-ftps.sh"
echo "For manual deployment, upload contents of $BUILD_DIR/ to your web server"
echo ""
echo "🧹 Cleanup verification:"
if [ -d "$BUILD_DIR" ]; then
    NODE_MODULES_COUNT=$(find "$BUILD_DIR" -name "node_modules" -type d | wc -l)
    PACKAGE_JSON_COUNT=$(find "$BUILD_DIR" -name "package*.json" | wc -l)

    if [ "$NODE_MODULES_COUNT" -eq 0 ]; then
        echo "  ✅ No node_modules directories found"
    else
        echo "  ⚠️  Found $NODE_MODULES_COUNT node_modules directories"
    fi

    if [ "$PACKAGE_JSON_COUNT" -eq 0 ]; then
        echo "  ✅ No package.json files found"
    else
        echo "  ⚠️  Found $PACKAGE_JSON_COUNT package.json files"
    fi

    # Check for other development files (excluding vendor directory)
    DEV_FILES=$(find "$BUILD_DIR" -path "*/vendor" -prune -o -name "*.config.js" -o -name "*.lock" -o -name ".git*" -print | wc -l)
    if [ "$DEV_FILES" -eq 0 ]; then
        echo "  ✅ No development configuration files found (excluding vendor)"
    else
        echo "  ℹ️  Found $DEV_FILES development files (excluding vendor - this is normal)"
    fi
fi
