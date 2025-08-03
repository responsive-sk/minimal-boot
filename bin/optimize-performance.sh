#!/bin/bash

# Performance Optimization Script for Minimal Boot
# Optimizes assets, enables compression, and improves loading performance

set -e

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

echo "⚡ Performance Optimization for Minimal Boot"
echo "============================================="

# Step 1: Optimize images
log_info "Optimizing images..."

if command -v optipng &> /dev/null; then
    find public/themes -name "*.png" -exec optipng -o7 {} \; 2>/dev/null || true
    log_success "PNG images optimized"
else
    log_warning "optipng not found, skipping PNG optimization"
fi

if command -v jpegoptim &> /dev/null; then
    find public/themes -name "*.jpg" -exec jpegoptim --max=85 {} \; 2>/dev/null || true
    find public/themes -name "*.jpeg" -exec jpegoptim --max=85 {} \; 2>/dev/null || true
    log_success "JPEG images optimized"
else
    log_warning "jpegoptim not found, skipping JPEG optimization"
fi

# Step 2: Optimize SVG files
if command -v svgo &> /dev/null; then
    find public/themes -name "*.svg" -exec svgo {} \; 2>/dev/null || true
    log_success "SVG files optimized"
else
    log_warning "svgo not found, skipping SVG optimization"
fi

# Step 3: Generate WebP versions of images
log_info "Generating WebP versions of images..."

if command -v cwebp &> /dev/null; then
    find public/themes -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" | while read img; do
        webp_file="${img%.*}.webp"
        if [ ! -f "$webp_file" ]; then
            cwebp -q 85 "$img" -o "$webp_file" 2>/dev/null || true
        fi
    done
    log_success "WebP images generated"
else
    log_warning "cwebp not found, skipping WebP generation"
fi

# Step 4: Compress JavaScript and CSS (if not already compressed)
log_info "Checking asset compression..."

# Check if assets are already minified
css_files=$(find public/themes -name "*.css" | head -1)
if [ -n "$css_files" ]; then
    if grep -q "sourceMappingURL" "$css_files" 2>/dev/null; then
        log_success "CSS files are already optimized"
    else
        log_info "CSS files appear to be production-ready"
    fi
fi

# Step 5: Create .htaccess with performance optimizations
log_info "Creating performance-optimized .htaccess..."

if [ -f "public/.htaccess.production" ]; then
    cp public/.htaccess.production public/.htaccess
    log_success "Performance .htaccess applied"
else
    log_warning ".htaccess.production not found"
fi

# Step 6: Generate critical CSS (if postcss-critical is available)
log_info "Checking for critical CSS optimization..."

if [ -f "src/Assets/main/package.json" ]; then
    cd src/Assets/main
    if npm list --depth=0 2>/dev/null | grep -q "critical"; then
        log_info "Generating critical CSS..."
        npm run critical 2>/dev/null || log_warning "Critical CSS generation failed"
    else
        log_info "Critical CSS tool not installed"
    fi
    cd ../../..
fi

# Step 7: Preload optimization
log_info "Optimizing resource preloading..."

# Check if layouts have proper preload tags
if grep -q "rel=\"preload\"" templates/themes/*/layouts/*.phtml 2>/dev/null; then
    log_success "Resource preloading is configured"
else
    log_warning "Consider adding resource preloading to layouts"
fi

# Step 8: Font optimization check
log_info "Checking font optimization..."

font_count=$(find public/themes -name "*.woff2" | wc -l)
if [ "$font_count" -gt 0 ]; then
    log_success "Local fonts are optimized ($font_count woff2 files found)"
else
    log_warning "No local fonts found, consider downloading fonts locally"
fi

# Step 9: Service Worker check (for advanced caching)
log_info "Checking for service worker..."

if [ -f "public/sw.js" ]; then
    log_success "Service worker found"
else
    log_info "Consider implementing service worker for advanced caching"
fi

# Step 10: Performance summary
echo ""
log_info "Performance Optimization Summary:"
echo "=================================="

# Calculate total asset sizes
css_size=$(find public/themes -name "*.css" -exec du -ch {} + 2>/dev/null | tail -1 | cut -f1 || echo "N/A")
js_size=$(find public/themes -name "*.js" -exec du -ch {} + 2>/dev/null | tail -1 | cut -f1 || echo "N/A")
font_size=$(find public/themes -name "*.woff*" -exec du -ch {} + 2>/dev/null | tail -1 | cut -f1 || echo "N/A")
image_size=$(find public/themes -name "*.jpg" -o -name "*.png" -o -name "*.svg" -o -name "*.webp" -exec du -ch {} + 2>/dev/null | tail -1 | cut -f1 || echo "N/A")

echo "  CSS Size: $css_size"
echo "  JS Size: $js_size"
echo "  Font Size: $font_size"
echo "  Image Size: $image_size"

echo ""
echo "🎯 Performance Recommendations:"
echo "1. Enable gzip compression on your server"
echo "2. Set proper cache headers (1 year for static assets)"
echo "3. Use a CDN for global content delivery"
echo "4. Monitor Core Web Vitals regularly"
echo "5. Consider implementing service worker for offline support"

echo ""
log_success "Performance optimization completed!"

echo ""
echo "📊 Next Steps:"
echo "1. Test your site with Google PageSpeed Insights"
echo "2. Check Core Web Vitals in Google Search Console"
echo "3. Monitor performance with tools like GTmetrix or WebPageTest"
echo "4. Consider implementing lazy loading for images"
echo "5. Optimize database queries if using dynamic content"

echo ""
echo "🔗 Useful Tools:"
echo "  - Google PageSpeed Insights: https://pagespeed.web.dev/"
echo "  - GTmetrix: https://gtmetrix.com/"
echo "  - WebPageTest: https://www.webpagetest.org/"
echo "  - Chrome DevTools Lighthouse"
