---
layout: page
title: "Core Compatibility Layer"
description: "Shared hosting environment support and function detection"
---

# Core Compatibility Layer

The compatibility layer provides safe fallbacks and detection mechanisms for shared hosting environments where certain PHP functions may be disabled.

## Overview

Many shared hosting providers disable potentially dangerous PHP functions for security reasons. The compatibility layer helps your application work reliably across different hosting environments.

**Common Disabled Functions:**
- `exec`, `shell_exec`, `system`, `passthru`
- `file_get_contents` (URL access)
- `curl_exec`
- `chmod`, `chown`
- `ini_set`

## FunctionChecker

### Basic Usage

```php
use Minimal\Core\Compatibility\FunctionChecker;

// Initialize (reads disabled_functions from php.ini)
FunctionChecker::init();

// Check if specific function is available
if (FunctionChecker::isAvailable('exec')) {
    exec('ls -la', $output);
} else {
    // Use alternative approach
    $output = ['Function exec is disabled'];
}
```

### Function Availability Detection

```php
// Check individual functions
$hasExec = FunctionChecker::isAvailable('exec');
$hasShellExec = FunctionChecker::isAvailable('shell_exec');
$hasCurl = FunctionChecker::isAvailable('curl_exec');

// Check capabilities
$canExecuteCommands = FunctionChecker::hasExecCapability();
$canAccessUrls = FunctionChecker::canAccessUrls();
$hasCurlSupport = FunctionChecker::hasCurlSupport();

// Get list of disabled functions
$disabledFunctions = FunctionChecker::getDisabledFunctions();
```

### Safe Command Execution

```php
// Safe execution with automatic fallbacks
$result = FunctionChecker::safeExec('git --version');

if ($result !== null) {
    echo "Git version: " . trim($result);
} else {
    echo "Cannot execute commands on this server";
}

// The safeExec method tries in order:
// 1. exec()
// 2. shell_exec()
// 3. system()
// 4. Returns null if none available
```

### URL Access Detection

```php
// Check if file_get_contents can access URLs
if (FunctionChecker::canAccessUrls()) {
    $content = file_get_contents('https://api.example.com/data');
} else {
    // Use cURL as fallback
    if (FunctionChecker::hasCurlSupport()) {
        $ch = curl_init('https://api.example.com/data');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
    } else {
        throw new RuntimeException('No method available to access URLs');
    }
}
```

### Practical Examples

```php
class SystemInfoService
{
    public function getPhpVersion(): string
    {
        return PHP_VERSION;
    }
    
    public function getServerInfo(): array
    {
        $info = [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'disabled_functions' => FunctionChecker::getDisabledFunctions()
        ];
        
        // Try to get additional system info if possible
        if (FunctionChecker::hasExecCapability()) {
            $info['system_info'] = [
                'uname' => FunctionChecker::safeExec('uname -a'),
                'disk_usage' => FunctionChecker::safeExec('df -h'),
                'memory_info' => FunctionChecker::safeExec('free -m')
            ];
        }
        
        return $info;
    }
    
    public function canSendEmails(): bool
    {
        // Check if mail function is available
        return FunctionChecker::isAvailable('mail');
    }
    
    public function canProcessImages(): bool
    {
        // Check if GD extension is loaded
        return extension_loaded('gd') && FunctionChecker::isAvailable('imagecreate');
    }
}
```

## SafeFileOperations

### Directory Creation

```php
use Minimal\Core\Compatibility\SafeFileOperations;

// Safe directory creation with fallbacks
$success = SafeFileOperations::createDirectory('var/cache', 0755);

if ($success) {
    echo "Directory created successfully";
} else {
    echo "Failed to create directory";
}

// The method handles:
// - Checking if directory already exists
// - Creating parent directories if needed
// - Setting permissions (where possible)
// - Graceful failure on permission errors
```

### File Writing Operations

```php
// Safe file writing
$content = "Configuration data\nLine 2\nLine 3";
$success = SafeFileOperations::writeFile('var/config/app.conf', $content);

if ($success) {
    echo "File written successfully";
} else {
    echo "Failed to write file";
}

// With file locking
$success = SafeFileOperations::writeFile(
    'var/logs/app.log', 
    $logEntry, 
    FILE_APPEND | LOCK_EX
);
```

### Permission Handling

```php
// Check if we can set permissions
if (SafeFileOperations::canSetPermissions()) {
    SafeFileOperations::setPermissions('var/cache', 0755);
    SafeFileOperations::setPermissions('var/logs/app.log', 0644);
} else {
    // Log that permissions couldn't be set
    error_log('Cannot set file permissions on this server');
}
```

### Practical Usage Examples

```php
class CacheManager
{
    private string $cacheDir;
    
    public function __construct(string $cacheDir = 'var/cache')
    {
        $this->cacheDir = $cacheDir;
        $this->ensureCacheDirectory();
    }
    
    private function ensureCacheDirectory(): void
    {
        if (!SafeFileOperations::createDirectory($this->cacheDir, 0755)) {
            throw new RuntimeException("Cannot create cache directory: {$this->cacheDir}");
        }
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $filename = $this->getCacheFilename($key);
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        $content = serialize($data);
        return SafeFileOperations::writeFile($filename, $content);
    }
    
    public function get(string $key): mixed
    {
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $content = file_get_contents($filename);
        if ($content === false) {
            return null;
        }
        
        $data = unserialize($content);
        
        if ($data['expires'] < time()) {
            unlink($filename);
            return null;
        }
        
        return $data['value'];
    }
    
    private function getCacheFilename(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }
}
```

## Environment Detection

### Hosting Environment Detection

```php
class HostingEnvironmentDetector
{
    public function getEnvironmentType(): string
    {
        // Check for common shared hosting indicators
        if ($this->isSharedHosting()) {
            return 'shared';
        }
        
        if ($this->isVPS()) {
            return 'vps';
        }
        
        if ($this->isDedicated()) {
            return 'dedicated';
        }
        
        return 'unknown';
    }
    
    private function isSharedHosting(): bool
    {
        $indicators = [
            // Many functions disabled
            count(FunctionChecker::getDisabledFunctions()) > 10,
            
            // Cannot execute commands
            !FunctionChecker::hasExecCapability(),
            
            // Limited file permissions
            !SafeFileOperations::canSetPermissions(),
            
            // Common shared hosting paths
            strpos(__DIR__, '/home/') === 0,
            strpos(__DIR__, '/public_html/') !== false
        ];
        
        return count(array_filter($indicators)) >= 2;
    }
    
    private function isVPS(): bool
    {
        return FunctionChecker::hasExecCapability() && 
               !$this->isSharedHosting();
    }
    
    private function isDedicated(): bool
    {
        // Full access to system functions
        $fullAccess = [
            FunctionChecker::isAvailable('exec'),
            FunctionChecker::isAvailable('shell_exec'),
            FunctionChecker::isAvailable('system'),
            SafeFileOperations::canSetPermissions()
        ];
        
        return count(array_filter($fullAccess)) === count($fullAccess);
    }
    
    public function getCapabilities(): array
    {
        return [
            'exec_capability' => FunctionChecker::hasExecCapability(),
            'url_access' => FunctionChecker::canAccessUrls(),
            'curl_support' => FunctionChecker::hasCurlSupport(),
            'file_permissions' => SafeFileOperations::canSetPermissions(),
            'disabled_functions' => FunctionChecker::getDisabledFunctions(),
            'environment_type' => $this->getEnvironmentType()
        ];
    }
}
```

## Best Practices

### Graceful Degradation

```php
class FeatureManager
{
    public function isFeatureAvailable(string $feature): bool
    {
        return match($feature) {
            'git_integration' => FunctionChecker::hasExecCapability(),
            'image_processing' => extension_loaded('gd'),
            'email_sending' => FunctionChecker::isAvailable('mail'),
            'file_uploads' => ini_get('file_uploads') === '1',
            'url_fetching' => FunctionChecker::canAccessUrls() || FunctionChecker::hasCurlSupport(),
            default => false
        };
    }
    
    public function getAlternativeForFeature(string $feature): ?string
    {
        if ($this->isFeatureAvailable($feature)) {
            return null;
        }
        
        return match($feature) {
            'git_integration' => 'Manual file upload for deployments',
            'image_processing' => 'Use external image service',
            'email_sending' => 'Use SMTP with external service',
            'url_fetching' => 'Manual data entry or file upload',
            default => 'Feature not available'
        };
    }
}
```

### Error Handling

```php
class SafeOperationWrapper
{
    public static function executeWithFallback(callable $primary, callable $fallback = null): mixed
    {
        try {
            return $primary();
        } catch (Exception $e) {
            error_log("Primary operation failed: " . $e->getMessage());
            
            if ($fallback) {
                try {
                    return $fallback();
                } catch (Exception $fallbackError) {
                    error_log("Fallback operation also failed: " . $fallbackError->getMessage());
                    throw $e; // Throw original exception
                }
            }
            
            throw $e;
        }
    }
}

// Usage
$result = SafeOperationWrapper::executeWithFallback(
    // Primary: Use exec if available
    fn() => FunctionChecker::safeExec('git rev-parse HEAD'),
    
    // Fallback: Read from file
    fn() => file_exists('.git/HEAD') ? file_get_contents('.git/HEAD') : null
);
```

### Configuration Adaptation

```php
// config/autoload/compatibility.local.php
return [
    'compatibility' => [
        'features' => [
            'exec_commands' => FunctionChecker::hasExecCapability(),
            'url_access' => FunctionChecker::canAccessUrls(),
            'file_permissions' => SafeFileOperations::canSetPermissions(),
        ],
        'fallbacks' => [
            'image_processing' => !extension_loaded('gd') ? 'external_service' : 'local',
            'email_delivery' => !FunctionChecker::isAvailable('mail') ? 'smtp' : 'local',
            'cache_storage' => !SafeFileOperations::canSetPermissions() ? 'database' : 'file',
        ]
    ]
];
```

## Testing

### Compatibility Testing

```php
class CompatibilityTest extends TestCase
{
    public function testFunctionAvailability(): void
    {
        FunctionChecker::init();
        
        // Test that checker works
        $this->assertIsBool(FunctionChecker::isAvailable('strlen'));
        $this->assertTrue(FunctionChecker::isAvailable('strlen')); // Should always be available
    }
    
    public function testSafeFileOperations(): void
    {
        $testDir = sys_get_temp_dir() . '/test_' . uniqid();
        
        $this->assertTrue(SafeFileOperations::createDirectory($testDir));
        $this->assertTrue(is_dir($testDir));
        
        $testFile = $testDir . '/test.txt';
        $this->assertTrue(SafeFileOperations::writeFile($testFile, 'test content'));
        $this->assertEquals('test content', file_get_contents($testFile));
        
        // Cleanup
        unlink($testFile);
        rmdir($testDir);
    }
}
```

## Troubleshooting

### Common Issues

**Functions Unexpectedly Disabled:**
```php
// Check current disabled functions
$disabled = FunctionChecker::getDisabledFunctions();
if (in_array('exec', $disabled)) {
    error_log('exec function is disabled by hosting provider');
}
```

**Permission Denied Errors:**
```php
// Check if directory is writable
if (!is_writable('var/cache')) {
    error_log('Cache directory is not writable');
    // Try alternative cache location
    $alternativeCache = sys_get_temp_dir() . '/app_cache';
    SafeFileOperations::createDirectory($alternativeCache);
}
```

**URL Access Blocked:**
```php
// Test URL access capability
if (!FunctionChecker::canAccessUrls()) {
    error_log('URL access is disabled - allow_url_fopen is off');
    
    if (!FunctionChecker::hasCurlSupport()) {
        error_log('cURL is also not available');
        // Use alternative data source or manual input
    }
}
```
