#!/bin/bash

# FTPS Deployment Script for Minimal Boot
# Deploys production build to shared hosting via FTPS

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
BUILD_DIR="build/production"
BACKUP_DIR="backup/$(date +%Y%m%d_%H%M%S)"
CONFIG_FILE=".ftps-config"

echo "🚀 FTPS Deployment for Minimal Boot"
echo "===================================="

# Check if production build exists
if [ ! -d "$BUILD_DIR" ]; then
    log_error "Production build not found!"
    echo "Please run: bin/build-production.sh first"
    exit 1
fi

# Check for FTPS configuration
if [ ! -f "$CONFIG_FILE" ]; then
    log_warning "FTPS configuration not found. Creating template..."
    cat > "$CONFIG_FILE" << 'EOF'
# FTPS Configuration for Minimal Boot
# Copy this file and update with your hosting details

FTPS_HOST="your-ftp-host.com"
FTPS_USER="your-ftp-username"
FTPS_PASS="your-ftp-password"
FTPS_PORT="21"
REMOTE_PATH="/public_html"

# SSL Settings
FTPS_SSL_FORCE="true"
FTPS_SSL_PROTECT_DATA="true"
FTPS_SSL_VERIFY_CERT="false"

# Deployment options
BACKUP_ENABLED="true"
DELETE_REMOTE_FILES="false"
EXCLUDE_PATTERNS=".env .htaccess"
EOF
    log_warning "Please update $CONFIG_FILE with your FTPS settings and run again"
    exit 1
fi

# Load configuration
source "$CONFIG_FILE"

# Validate required configuration
if [ -z "$FTPS_HOST" ] || [ -z "$FTPS_USER" ] || [ -z "$FTPS_PASS" ]; then
    log_error "Missing required FTPS configuration!"
    echo "Please update $CONFIG_FILE with your FTPS settings"
    exit 1
fi

# Check if lftp is available
if ! command -v lftp &> /dev/null; then
    log_error "lftp is required for FTPS deployment"
    echo ""
    echo "Install lftp:"
    echo "  Ubuntu/Debian: sudo apt-get install lftp"
    echo "  macOS: brew install lftp"
    echo "  CentOS/RHEL: sudo yum install lftp"
    exit 1
fi

# Confirmation prompt
echo ""
log_info "Deployment Configuration:"
echo "  Host: $FTPS_HOST"
echo "  User: $FTPS_USER"
echo "  Remote Path: $REMOTE_PATH"
echo "  Build Directory: $BUILD_DIR"
echo ""

read -p "Continue with deployment? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    log_info "Deployment cancelled"
    exit 0
fi

# Create backup directory
if [ "$BACKUP_ENABLED" = "true" ]; then
    log_info "Creating backup directory..."
    mkdir -p "$BACKUP_DIR"
    log_success "Backup directory created: $BACKUP_DIR"
fi

# Prepare deployment
log_info "Preparing deployment..."
cd "$BUILD_DIR"

# Create deployment script for lftp
cat > deploy.lftp << EOF
set ftp:ssl-force $FTPS_SSL_FORCE
set ftp:ssl-protect-data $FTPS_SSL_PROTECT_DATA
set ssl:verify-certificate $FTPS_SSL_VERIFY_CERT
set ftp:passive-mode true
set net:timeout 30
set net:max-retries 3

open ftps://$FTPS_USER:$FTPS_PASS@$FTPS_HOST:$FTPS_PORT

# Change to remote directory
cd $REMOTE_PATH

# Create backup if enabled
EOF

if [ "$BACKUP_ENABLED" = "true" ]; then
    cat >> deploy.lftp << EOF
# Download current files for backup
mirror --verbose . ../../$BACKUP_DIR

EOF
fi

# Add deployment commands
if [ "$DELETE_REMOTE_FILES" = "true" ]; then
    cat >> deploy.lftp << EOF
# Mirror with delete (removes files not in local build)
mirror -R --delete --verbose . .

EOF
else
    cat >> deploy.lftp << EOF
# Mirror without delete (safer for first deployment)
mirror -R --verbose . .

EOF
fi

cat >> deploy.lftp << EOF
# Set proper permissions
chmod 644 index.php
chmod 644 .htaccess
chmod -R 644 themes/
chmod -R 755 themes/*/

quit
EOF

# Execute deployment
log_info "Starting FTPS deployment..."
echo ""

if lftp -f deploy.lftp; then
    log_success "Deployment completed successfully!"
    
    # Cleanup
    rm deploy.lftp
    
    echo ""
    echo "🎉 Deployment Summary:"
    echo "======================"
    echo "  Remote Host: $FTPS_HOST"
    echo "  Remote Path: $REMOTE_PATH"
    echo "  Deployed Files: $(find . -type f | wc -l) files"
    echo "  Build Size: $(du -sh . | cut -f1)"
    
    if [ "$BACKUP_ENABLED" = "true" ]; then
        echo "  Backup Location: $BACKUP_DIR"
    fi
    
    echo ""
    echo "🔗 Your application should now be live!"
    echo ""
    echo "Next steps:"
    echo "1. Test your application in a browser"
    echo "2. Check error logs if there are issues"
    echo "3. Monitor performance and functionality"
    
else
    log_error "Deployment failed!"
    rm deploy.lftp
    
    echo ""
    echo "Troubleshooting:"
    echo "1. Check your FTPS credentials in $CONFIG_FILE"
    echo "2. Verify your hosting provider supports FTPS"
    echo "3. Check if the remote path exists"
    echo "4. Try with FTPS_SSL_VERIFY_CERT=false if SSL issues"
    
    exit 1
fi

cd ../..

# Post-deployment checks
echo ""
log_info "Post-deployment recommendations:"
echo ""
echo "🔍 Health Checks:"
echo "  1. Visit your website and verify it loads"
echo "  2. Test theme switching (/theme/switch)"
echo "  3. Check demo pages (/demo, /demo/bootstrap)"
echo "  4. Verify contact form works (/contact)"
echo ""
echo "🔒 Security Checks:"
echo "  1. Ensure .env file is not publicly accessible"
echo "  2. Verify HTTPS is working"
echo "  3. Check security headers"
echo "  4. Test file permissions"
echo ""
echo "📊 Performance Checks:"
echo "  1. Test page load speeds"
echo "  2. Verify asset compression"
echo "  3. Check OPcache status"
echo "  4. Monitor error logs"

log_success "Deployment process completed!"
