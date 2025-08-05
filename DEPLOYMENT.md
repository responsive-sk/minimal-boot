# Minimal Boot - Deployment Guide

## Shared Hosting Deployment

### Prerequisites
- PHP 8.1+ with required extensions
- Apache/Nginx web server
- Composer installed (or upload vendor directory)

### Deployment Steps

#### 1. Upload Files
```bash
# Extract the deployment package
tar -xzf minimal-boot-deployment.tar.gz

# Upload all files to your hosting public_html directory
# Make sure .htaccess files are uploaded
```

#### 2. Set Permissions
```bash
# Set correct permissions
chmod 755 public/
chmod 644 public/.htaccess
chmod 755 public/fonts/
chmod 644 public/fonts/*
chmod 755 public/themes/
chmod -R 644 public/themes/*
```

#### 3. Install Dependencies (if needed)
```bash
# If vendor directory is not included, run:
composer install --no-dev --optimize-autoloader
```

#### 4. Configuration
```bash
# Copy environment configuration
cp config/autoload/local.php.dist config/autoload/local.php

# Edit database and other settings in:
# config/autoload/local.php
```

#### 5. Web Server Configuration

##### Apache (.htaccess already included)
The project includes optimized .htaccess files with:
- Font MIME types and CORS headers
- Cache headers for performance
- URL rewriting for clean URLs

##### Nginx (if using Nginx)
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/public;
    index index.php;

    # Font MIME types
    location ~* \.(woff2?|eot|ttf|otf)$ {
        add_header Access-Control-Allow-Origin "*";
        add_header Cache-Control "public, max-age=31536000, immutable";
        expires 1y;
    }

    # CSS/JS caching
    location ~* \.(css|js)$ {
        add_header Cache-Control "public, max-age=2592000";
        expires 1M;
    }

    # PHP handling
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Built Assets Included

#### Themes
- **Bootstrap Theme**: `public/themes/bootstrap/assets/`
  - main.css (235KB, 32KB gzipped)
  - main.js (83KB, 25KB gzipped)

- **Tailwind Theme**: `public/themes/main/assets/`
  - main.css (46KB, 8KB gzipped)
  - main.js (48KB, 17KB gzipped)

#### Fonts
- **Source Sans Pro**: `public/fonts/`
  - All weights (300, 400, 600, 700) in WOFF2 format
  - Optimized for modern browsers
  - Total size: ~65KB

### Performance Features

#### Cache Headers
- **Fonts**: 1 year cache with immutable flag
- **CSS/JS**: 1 month cache
- **Images**: 1 month cache
- **HTML**: 1 hour cache

#### Font Loading
- WOFF2 format for best compression
- font-display: swap for better performance
- CORS headers for cross-origin loading

#### SEO Optimizations
- Absolute canonical URLs
- Complete Open Graph meta tags
- Twitter Card meta tags
- Descriptive link texts

### Testing Deployment

#### 1. Check Font Loading
```bash
# Test font MIME type
curl -I https://your-domain.com/fonts/source-sans-pro-400.woff2

# Should return:
# Content-Type: font/woff2
# Access-Control-Allow-Origin: *
# Cache-Control: public, max-age=31536000, immutable
```

#### 2. Check Theme Switching
- Visit homepage
- Test theme switching functionality
- Verify both Bootstrap and Tailwind themes work

#### 3. Performance Testing
- Run Lighthouse audit
- Check cache headers in Network tab
- Verify no FOUC (Flash of Unstyled Content)

### Troubleshooting

#### Font Loading Issues
If fonts don't load properly:
1. Check .htaccess is uploaded and working
2. Verify font files exist in public/fonts/
3. Check server supports font/woff2 MIME type

#### Theme Switching Issues
1. Check JavaScript console for errors
2. Verify theme assets are accessible
3. Check PHP error logs

#### Performance Issues
1. Enable gzip compression on server
2. Verify cache headers are working
3. Check .htaccess rules are applied

### File Structure
```
public/
├── .htaccess              # Apache configuration
├── index.php             # Entry point
├── fonts/                 # Font files
│   ├── source-sans-pro-300.woff2
│   ├── source-sans-pro-400.woff2
│   ├── source-sans-pro-600.woff2
│   └── source-sans-pro-700.woff2
├── themes/
│   ├── bootstrap/assets/  # Bootstrap theme assets
│   └── main/assets/       # Tailwind theme assets
└── images/               # Static images
```

### Support
For issues or questions, check:
- Project documentation in docs/
- GitHub repository issues
- Server error logs
