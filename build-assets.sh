#!/bin/bash

# Build Assets Script for Minimal Boot
# This script builds both Bootstrap and TailwindCSS themes

set -e  # Exit on any error

echo "🚀 Building Minimal Boot Assets..."
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if pnpm is installed
if ! command -v pnpm &> /dev/null; then
    print_error "pnpm is not installed. Please install pnpm first:"
    echo "  npm install -g pnpm"
    exit 1
fi

# Build Bootstrap theme
print_status "Building Bootstrap theme..."
cd src/Assets/bootstrap

if [ ! -d "node_modules" ]; then
    print_status "Installing Bootstrap dependencies..."
    pnpm install
fi

print_status "Building Bootstrap assets..."
pnpm build

if [ $? -eq 0 ]; then
    print_success "Bootstrap theme built successfully!"
else
    print_error "Bootstrap build failed!"
    exit 1
fi

cd ../../..

# Build TailwindCSS theme
print_status "Building TailwindCSS theme..."
cd src/Assets/main

if [ ! -d "node_modules" ]; then
    print_status "Installing TailwindCSS dependencies..."
    pnpm install
fi

print_status "Building TailwindCSS assets..."
pnpm build

if [ $? -eq 0 ]; then
    print_success "TailwindCSS theme built successfully!"
else
    print_error "TailwindCSS build failed!"
    exit 1
fi

cd ../../..

# Verify built assets
print_status "Verifying built assets..."

if [ -f "public/themes/bootstrap/assets/main.css" ] && [ -f "public/themes/bootstrap/assets/main.js" ]; then
    print_success "Bootstrap assets verified ✓"
else
    print_warning "Bootstrap assets not found!"
fi

if [ -f "public/themes/main/assets/main.css" ] && [ -f "public/themes/main/assets/main.js" ]; then
    print_success "TailwindCSS assets verified ✓"
else
    print_warning "TailwindCSS assets not found!"
fi

echo ""
print_success "🎉 All assets built successfully!"
echo ""
echo "Available demos:"
echo "  • Bootstrap 5:        http://localhost:8080/demo/bootstrap"
echo "  • TailwindCSS:        http://localhost:8080/demo"
echo ""
echo "Asset locations:"
echo "  • Bootstrap assets:   public/themes/bootstrap/assets/"
echo "  • TailwindCSS assets: public/themes/main/assets/"
echo ""
