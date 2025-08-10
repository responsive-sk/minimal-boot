# 🔧 Theme Switching Issue - Session Cookie Problem

## 🚨 **Identifikovaný Problém**

### **Symptómy:**
- Theme switching nefunguje - téma sa vráti na default po refresh
- Session ID sa mení medzi requestami
- Session data sa neukladajú persistentne

### **Root Cause:**
**PHP Built-in Development Server má známe problémy so session handling**

## 🔍 **Analýza Problému**

### **Čo sme zistili:**
1. **Theme switching logika funguje správne** ✅
   - Debug logy potvrdzujú: `DEBUG: Theme set successfully to: bootstrap`
   - ThemeService.setTheme() funguje
   - Session sa nastaví v rámci requestu

2. **Session sa resetuje medzi requestami** ❌
   - Session ID: `5dd59b58503399830fb47d5957241406` → `a21893fc22b50d5b6f939ab73633b20b`
   - Session súbory sa nevytvárajú v `var/sessions/`
   - Cookie sa neukladá správne

3. **PHP Built-in Server Limitation** ❌
   - Jednoduchý test potvrdil problém aj s natívnym PHP session
   - Session counter sa resetuje na 1 pri každom requeste
   - Známy problém s `php -S` serverom

## 🛠️ **Riešenia**

### **1. Použiť Správny Web Server (Odporúčané)**

#### **Apache s mod_php:**
```bash
# Inštalácia Apache
sudo apt-get install apache2 libapache2-mod-php8.4

# Konfigurácia virtual host
sudo nano /etc/apache2/sites-available/minimal-boot.conf
```

```apache
<VirtualHost *:80>
    ServerName minimal-boot.local
    DocumentRoot /path/to/minimal-boot/public
    
    <Directory /path/to/minimal-boot/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Session configuration
    php_value session.save_path "/path/to/minimal-boot/var/sessions"
    php_value session.cookie_secure "0"
    php_value session.cookie_samesite "Lax"
</VirtualHost>
```

#### **Nginx s PHP-FPM:**
```bash
# Inštalácia
sudo apt-get install nginx php8.4-fpm

# Konfigurácia
sudo nano /etc/nginx/sites-available/minimal-boot
```

```nginx
server {
    listen 80;
    server_name minimal-boot.local;
    root /path/to/minimal-boot/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### **2. Docker Riešenie (Najjednoduchšie)**

```dockerfile
# Dockerfile
FROM php:8.4-apache

# Enable Apache modules
RUN a2enmod rewrite

# Install extensions
RUN docker-php-ext-install pdo pdo_sqlite

# Copy application
COPY . /var/www/html/
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html/var
```

```bash
# Spustenie
docker build -t minimal-boot .
docker run -p 8080:80 minimal-boot
```

### **3. Workaround pre Development**

Pre rýchle testovanie môžeme použiť file-based session storage:

```php
// V SessionFactory
ini_set('session.save_handler', 'files');
ini_set('session.save_path', realpath('./var/sessions'));
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
```

### **4. Alternative - Memory Based Sessions**

```php
// Pre development môžeme použiť array-based session
class ArraySessionStorage implements SessionInterface {
    private static array $data = [];
    private string $sessionId;
    
    public function __construct() {
        $this->sessionId = uniqid('sess_', true);
    }
    
    public function get(string $key, mixed $default = null): mixed {
        return self::$data[$this->sessionId][$key] ?? $default;
    }
    
    public function set(string $key, mixed $value): void {
        self::$data[$this->sessionId][$key] = $value;
    }
}
```

## 🎯 **Odporúčané Riešenie**

### **Pre Development:**
1. **Docker** - najjednoduchšie a najbližšie k produkcii
2. **Apache/Nginx** - ak máte lokálne nainštalované

### **Pre Production:**
- Apache alebo Nginx s PHP-FPM
- Proper session configuration
- Redis/Memcached pre session storage

## 📝 **Testovanie**

### **Overenie že session funguje:**
```bash
# Navštívte
http://localhost/theme/switch

# Prepnite tému
http://localhost/theme/switch?theme=bootstrap

# Overte že sa zachovala
http://localhost/theme/switch
# Malo by vrátiť: "current":"bootstrap"
```

### **Debug Session:**
```bash
# Skontrolujte session súbory
ls -la var/sessions/

# Skontrolujte session ID konzistenciu
curl -c cookies.txt http://localhost/theme/switch
curl -b cookies.txt http://localhost/theme/switch?theme=bootstrap
curl -b cookies.txt http://localhost/theme/switch
```

## 🔧 **Status**

- ✅ **Theme switching logika**: Funguje správne
- ✅ **Session handling kód**: Implementovaný správne  
- ❌ **PHP Built-in Server**: Nefunguje so session
- ✅ **Riešenia**: Identifikované a dokumentované

**Záver**: Problém nie je v kóde, ale v použití PHP built-in servera pre session-dependent funkcionalitu.
