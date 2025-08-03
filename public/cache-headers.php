<?php
/**
 * Cache Headers Override for Static Assets
 * This file handles cache headers when .htaccess is not working properly
 */

// Get the requested file path
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$filePath = parse_url($requestUri, PHP_URL_PATH);

// Determine file extension
$extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

// Define cache durations (in seconds)
$cacheRules = [
    // Static assets - 1 year
    'css' => 31536000,
    'js' => 31536000,
    'woff' => 31536000,
    'woff2' => 31536000,
    'eot' => 31536000,
    'ttf' => 31536000,
    'otf' => 31536000,
    
    // Images - 1 year
    'png' => 31536000,
    'jpg' => 31536000,
    'jpeg' => 31536000,
    'gif' => 31536000,
    'svg' => 31536000,
    'webp' => 31536000,
    'ico' => 31536000,
    
    // Documents - 1 month
    'pdf' => 2592000,
    
    // Default - 1 hour
    'default' => 3600
];

// Get cache duration for this file type
$cacheDuration = $cacheRules[$extension] ?? $cacheRules['default'];

// Set cache headers
if (in_array($extension, ['css', 'js', 'woff', 'woff2', 'eot', 'ttf', 'otf', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'ico'])) {
    // Static assets - aggressive caching
    header('Cache-Control: public, max-age=' . $cacheDuration . ', immutable');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheDuration) . ' GMT');
    header('Pragma: public');
    
    // Remove any existing no-cache headers
    header_remove('Pragma');
    if (function_exists('header_remove')) {
        header_remove('Cache-Control');
        header_remove('Expires');
    }
    
    // Set the corrected headers
    header('Cache-Control: public, max-age=' . $cacheDuration . ', immutable');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheDuration) . ' GMT');
    
} else {
    // Dynamic content - short cache
    header('Cache-Control: public, max-age=' . $cacheDuration . ', must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheDuration) . ' GMT');
}

// Set additional performance headers
header('Vary: Accept-Encoding');

// Check if file exists and serve it
$documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
$fullPath = $documentRoot . $filePath;

if (file_exists($fullPath) && is_file($fullPath)) {
    // Set appropriate content type
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'eot' => 'application/vnd.ms-fontobject',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'pdf' => 'application/pdf'
    ];
    
    $contentType = $mimeTypes[$extension] ?? 'application/octet-stream';
    header('Content-Type: ' . $contentType);
    
    // Set content length
    $fileSize = filesize($fullPath);
    if ($fileSize !== false) {
        header('Content-Length: ' . $fileSize);
    }
    
    // Output the file
    readfile($fullPath);
    exit;
} else {
    // File not found
    http_response_code(404);
    echo '404 Not Found';
    exit;
}
?>
