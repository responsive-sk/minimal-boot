#!/bin/bash

# Test Cache Headers Script
# Tests if cache headers are properly set on production server

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

# Configuration
DOMAIN="${1:-boot.responsive.sk}"
PROTOCOL="https"

echo "🔍 Testing Cache Headers for $DOMAIN"
echo "===================================="

# Test URLs
declare -A TEST_URLS=(
    ["CSS"]="/themes/main/assets/main.css"
    ["JavaScript"]="/themes/main/assets/main.js"
    ["Font WOFF2"]="/themes/main/assets/fonts/source-sans-pro-400.woff2"
    ["Font WOFF"]="/themes/main/assets/fonts/source-sans-pro-400.woff"
    ["SVG Image"]="/themes/main/assets/images/nav/logo.svg"
    ["HTML Page"]="/"
)

# Function to test cache headers
test_cache_headers() {
    local name="$1"
    local url="$2"
    local full_url="$PROTOCOL://$DOMAIN$url"
    
    log_info "Testing $name: $url"
    
    # Get headers
    headers=$(curl -s -I "$full_url" 2>/dev/null || echo "ERROR")
    
    if [[ "$headers" == "ERROR" ]]; then
        log_error "$name: Failed to fetch headers"
        return 1
    fi
    
    # Check for cache-control header
    cache_control=$(echo "$headers" | grep -i "cache-control:" | head -1 | cut -d: -f2- | tr -d '\r\n' | sed 's/^ *//')
    expires=$(echo "$headers" | grep -i "expires:" | head -1 | cut -d: -f2- | tr -d '\r\n' | sed 's/^ *//')
    
    echo "  Cache-Control: ${cache_control:-'Not set'}"
    echo "  Expires: ${expires:-'Not set'}"
    
    # Analyze cache headers
    if [[ -n "$cache_control" ]]; then
        if [[ "$cache_control" =~ max-age=([0-9]+) ]]; then
            max_age="${BASH_REMATCH[1]}"
            days=$((max_age / 86400))
            
            if [[ "$name" =~ (CSS|JavaScript|Font|SVG) ]]; then
                # Static assets should have long cache (at least 30 days)
                if [[ $max_age -ge 2592000 ]]; then
                    log_success "$name: Good cache ($days days)"
                else
                    log_warning "$name: Short cache ($days days, should be 365+ days)"
                fi
            else
                # HTML should have short cache
                if [[ $max_age -le 86400 ]]; then
                    log_success "$name: Appropriate cache ($days days)"
                else
                    log_warning "$name: Cache too long for dynamic content ($days days)"
                fi
            fi
        else
            log_warning "$name: Cache-Control set but no max-age found"
        fi
    else
        log_error "$name: No Cache-Control header found"
    fi
    
    echo ""
}

# Test all URLs
for name in "${!TEST_URLS[@]}"; do
    test_cache_headers "$name" "${TEST_URLS[$name]}"
done

# Summary and recommendations
echo "📋 Cache Header Recommendations:"
echo "================================"
echo ""
echo "✅ Static Assets (CSS, JS, Fonts, Images):"
echo "   Cache-Control: public, max-age=31536000, immutable"
echo "   Expires: 1 year from now"
echo ""
echo "✅ HTML Pages:"
echo "   Cache-Control: public, max-age=3600"
echo "   Expires: 1 hour from now"
echo ""
echo "🔧 If cache headers are not working:"
echo "1. Ensure .htaccess is uploaded to web root"
echo "2. Check if mod_headers and mod_expires are enabled"
echo "3. Verify server supports .htaccess files"
echo "4. Contact hosting provider if issues persist"
echo ""
echo "📊 Expected PageSpeed Insights improvement:"
echo "   - 109 KiB cache savings"
echo "   - Better repeat visit performance"
echo "   - Improved LCP and FCP scores"
