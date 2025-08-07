<?php

/**
 * Critical CSS Injection Helper
 * Inlines critical CSS based on current theme
 */

function getCriticalCSS(string $theme = 'bootstrap'): string
{
    $criticalFile = __DIR__ . "/{$theme}-critical.css";

    if (file_exists($criticalFile)) {
        $content = file_get_contents($criticalFile);
        return $content !== false ? $content : '';
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
