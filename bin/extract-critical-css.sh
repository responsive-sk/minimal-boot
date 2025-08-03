#!/bin/bash

# Extract Critical CSS for Above-the-Fold Content
# This script extracts critical CSS that should be inlined in HTML

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

echo "🎯 Extracting Critical CSS for Above-the-Fold Content"
echo "===================================================="

# Critical CSS for both themes
CRITICAL_CSS_DIR="src/Assets/critical"
mkdir -p "$CRITICAL_CSS_DIR"

# Bootstrap Critical CSS
log_info "Creating Bootstrap critical CSS..."
cat > "$CRITICAL_CSS_DIR/bootstrap-critical.css" << 'EOF'
/* Bootstrap Critical CSS - Above the fold only */
*,*::before,*::after{box-sizing:border-box}
body{margin:0;font-family:'Source Sans Pro','Segoe UI',Tahoma,Geneva,Verdana,sans-serif;font-size:1rem;font-weight:400;line-height:1.5;color:#212529;background-color:#fff}
.container{width:100%;padding-right:var(--bs-gutter-x,.75rem);padding-left:var(--bs-gutter-x,.75rem);margin-right:auto;margin-left:auto}
@media (min-width:576px){.container{max-width:540px}}
@media (min-width:768px){.container{max-width:720px}}
@media (min-width:992px){.container{max-width:960px}}
@media (min-width:1200px){.container{max-width:1140px}}
.navbar{position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;padding-top:.5rem;padding-bottom:.5rem}
.navbar-brand{padding-top:.3125rem;padding-bottom:.3125rem;margin-right:1rem;font-size:1.25rem;text-decoration:none;white-space:nowrap}
.navbar-nav{display:flex;flex-direction:column;padding-left:0;margin-bottom:0;list-style:none}
.nav-link{display:block;padding:.5rem 1rem;color:#0d6efd;text-decoration:none;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out}
.btn{display:inline-block;font-weight:400;line-height:1.5;color:#212529;text-align:center;text-decoration:none;vertical-align:middle;cursor:pointer;user-select:none;background-color:transparent;border:1px solid transparent;padding:.375rem .75rem;font-size:1rem;border-radius:.375rem;transition:color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out}
.btn-primary{color:#fff;background-color:#0d6efd;border-color:#0d6efd}
h1,h2,h3,h4,h5,h6{margin-top:0;margin-bottom:.5rem;font-weight:500;line-height:1.2}
h1{font-size:calc(1.375rem + 1.5vw)}
@media (min-width:1200px){h1{font-size:2.5rem}}
p{margin-top:0;margin-bottom:1rem}
EOF

log_success "Bootstrap critical CSS created"

# Tailwind Critical CSS
log_info "Creating Tailwind critical CSS..."
cat > "$CRITICAL_CSS_DIR/tailwind-critical.css" << 'EOF'
/* Tailwind Critical CSS - Above the fold only */
*,::before,::after{box-sizing:border-box;border-width:0;border-style:solid;border-color:#e5e7eb}
::before,::after{--tw-content:''}
html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;font-family:'Source Sans Pro',Inter,system-ui,sans-serif;font-feature-settings:normal;font-variation-settings:normal}
body{margin:0;line-height:inherit}
h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}
a{color:inherit;text-decoration:inherit}
button{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;color:inherit;margin:0;padding:0}
.container{width:100%}
@media (min-width:640px){.container{max-width:640px}}
@media (min-width:768px){.container{max-width:768px}}
@media (min-width:1024px){.container{max-width:1024px}}
@media (min-width:1280px){.container{max-width:1280px}}
@media (min-width:1536px){.container{max-width:1536px}}
.mx-auto{margin-left:auto;margin-right:auto}
.flex{display:flex}
.hidden{display:none}
.h-16{height:4rem}
.w-full{width:100%}
.items-center{align-items:center}
.justify-between{justify-content:space-between}
.space-x-4>:not([hidden])~:not([hidden]){--tw-space-x-reverse:0;margin-right:calc(1rem * var(--tw-space-x-reverse));margin-left:calc(1rem * calc(1 - var(--tw-space-x-reverse)))}
.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255 / var(--tw-bg-opacity))}
.px-4{padding-left:1rem;padding-right:1rem}
.py-2{padding-top:.5rem;padding-bottom:.5rem}
.text-xl{font-size:1.25rem;line-height:1.75rem}
.font-bold{font-weight:700}
.text-gray-900{--tw-text-opacity:1;color:rgb(17 24 39 / var(--tw-text-opacity))}
.text-blue-600{--tw-text-opacity:1;color:rgb(37 99 235 / var(--tw-text-opacity))}
@media (min-width:768px){.md\:flex{display:flex}.md\:hidden{display:none}}
EOF

log_success "Tailwind critical CSS created"

# Create critical CSS injection script
log_info "Creating critical CSS injection helper..."
cat > "$CRITICAL_CSS_DIR/inject-critical.php" << 'EOF'
<?php
/**
 * Critical CSS Injection Helper
 * Inlines critical CSS based on current theme
 */

function getCriticalCSS(string $theme = 'bootstrap'): string
{
    $criticalFile = __DIR__ . "/{$theme}-critical.css";
    
    if (file_exists($criticalFile)) {
        return file_get_contents($criticalFile);
    }
    
    return '';
}

function injectCriticalCSS(string $theme = 'bootstrap'): void
{
    $criticalCSS = getCriticalCSS($theme);
    
    if (!empty($criticalCSS)) {
        echo "<style id=\"critical-css\">\n";
        echo $criticalCSS;
        echo "\n</style>\n";
    }
}
?>
EOF

log_success "Critical CSS injection helper created"

# Update build script to include critical CSS
log_info "Updating build script to include critical CSS..."

# Add critical CSS to build process
if ! grep -q "critical" bin/build-production.sh; then
    sed -i '/# Copy essential application files/a\
\
# Copy critical CSS\
if [ -d "src/Assets/critical" ]; then\
    cp -r src/Assets/critical "$BUILD_DIR/src/Assets/"\
    log_success "Critical CSS copied"\
fi' bin/build-production.sh
fi

echo ""
log_success "Critical CSS extraction completed!"

echo ""
echo "📋 Next Steps:"
echo "1. Update your layout templates to include critical CSS"
echo "2. Defer non-critical CSS loading"
echo "3. Test the implementation"
echo ""
echo "Example usage in layout:"
echo "<?php include 'src/Assets/critical/inject-critical.php'; ?>"
echo "<?php injectCriticalCSS('bootstrap'); // or 'tailwind' ?>"
echo ""
echo "This will reduce critical path latency by inlining essential CSS!"
