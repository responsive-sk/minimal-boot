#!/bin/bash

# Download Google Fonts locally for better performance
# This script downloads Source Sans Pro fonts from Google Fonts

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

echo "🔤 Downloading Google Fonts locally..."

# Font directory
FONT_DIR="src/Assets/main/src/fonts/source-sans-pro"

# Create font directory if it doesn't exist
mkdir -p "$FONT_DIR"

# Google Fonts URLs for Source Sans Pro
# We need different weights: 300, 400, 600, 700
# And different formats: woff2 (modern), woff (fallback)

log_info "Downloading Source Sans Pro fonts..."

# Source Sans Pro 300 (Light)
log_info "Downloading Source Sans Pro 300 (Light)..."
curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xK3dSBYKcSV-LCoeQqfX1RYOo3qOK7lujVj9w.woff2" \
  -o "$FONT_DIR/source-sans-pro-300.woff2"

curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xK3dSBYKcSV-LCoeQqfX1RYOo3qOK7luixj9_2pGg.woff" \
  -o "$FONT_DIR/source-sans-pro-300.woff"

# Source Sans Pro 400 (Regular)
log_info "Downloading Source Sans Pro 400 (Regular)..."
curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xK3dSBYKcSV-LCoeQqfX1RYOo3qOK7lujVj9w.woff2" \
  -o "$FONT_DIR/source-sans-pro-400.woff2"

curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xK3dSBYKcSV-LCoeQqfX1RYOo3qOK7luixj9_2pGg.woff" \
  -o "$FONT_DIR/source-sans-pro-400.woff"

# Source Sans Pro 600 (SemiBold)
log_info "Downloading Source Sans Pro 600 (SemiBold)..."
curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xKydSBYKcSV-LCoeQqfX1RYOo3i54rwlxdu3cOWxw.woff2" \
  -o "$FONT_DIR/source-sans-pro-600.woff2"

curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xKydSBYKcSV-LCoeQqfX1RYOo3i54rwlxdu3cOWxw.woff" \
  -o "$FONT_DIR/source-sans-pro-600.woff"

# Source Sans Pro 700 (Bold)
log_info "Downloading Source Sans Pro 700 (Bold)..."
curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xKydSBYKcSV-LCoeQqfX1RYOo3ig4vwlxdu3cOWxw.woff2" \
  -o "$FONT_DIR/source-sans-pro-700.woff2"

curl -s "https://fonts.gstatic.com/s/sourcesanspro/v22/6xKydSBYKcSV-LCoeQqfX1RYOo3ig4vwlxdu3cOWxw.woff" \
  -o "$FONT_DIR/source-sans-pro-700.woff"

log_success "All fonts downloaded successfully!"

# Create CSS file for local fonts
log_info "Creating local font CSS..."

cat > "$FONT_DIR/source-sans-pro.css" << 'EOF'
/* Source Sans Pro Local Fonts */

@font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 300;
  font-display: swap;
  src: url('./source-sans-pro-300.woff2') format('woff2'),
       url('./source-sans-pro-300.woff') format('woff');
}

@font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('./source-sans-pro-400.woff2') format('woff2'),
       url('./source-sans-pro-400.woff') format('woff');
}

@font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 600;
  font-display: swap;
  src: url('./source-sans-pro-600.woff2') format('woff2'),
       url('./source-sans-pro-600.woff') format('woff');
}

@font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url('./source-sans-pro-700.woff2') format('woff2'),
       url('./source-sans-pro-700.woff') format('woff');
}
EOF

log_success "Local font CSS created!"

# Show downloaded files
echo ""
log_info "Downloaded files:"
ls -la "$FONT_DIR"

echo ""
log_success "Font download completed!"
echo ""
echo "Next steps:"
echo "1. Import the local fonts in your CSS: @import './fonts/source-sans-pro/source-sans-pro.css';"
echo "2. Remove Google Fonts link from HTML"
echo "3. Rebuild assets with: cd src/Assets/main && pnpm run build"
echo ""
echo "This will eliminate the 780ms render-blocking request from Google Fonts!"
