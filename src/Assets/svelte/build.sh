#!/bin/bash

# Svelte Theme Build Script
echo "🚀 Building Svelte theme..."

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies with pnpm..."
    pnpm install
fi

# Build the Svelte app
echo "🔨 Building Svelte components..."
pnpm run build

# Check if build was successful
if [ $? -eq 0 ]; then
    echo "✅ Svelte theme built successfully!"
    echo "📁 Output: public/themes/svelte/"
    
    # List generated files
    echo "📋 Generated files:"
    ls -la ../../../public/themes/svelte/assets/
else
    echo "❌ Build failed!"
    exit 1
fi
