<?php

declare(strict_types=1);

namespace Minimal\Shared\Service;

/**
 * Theme-aware template service.
 * 
 * Provides theme-specific template paths using the new organized structure.
 */
class ThemeAwareTemplateService
{
    public function __construct(
        private ThemeService $themeService
    ) {
    }

    /**
     * Get layout template path for current theme.
     */
    public function getLayoutTemplate(string $layout = 'app'): string
    {
        $theme = $this->themeService->getCurrentTheme();
        return "{$theme}_layouts::{$layout}";
    }

    /**
     * Get page template path for current theme.
     */
    public function getPageTemplate(string $page): string
    {
        $theme = $this->themeService->getCurrentTheme();
        return "{$theme}_pages::{$page}";
    }

    /**
     * Get partial template path for current theme.
     */
    public function getPartialTemplate(string $partial): string
    {
        $theme = $this->themeService->getCurrentTheme();
        return "{$theme}_partials::{$partial}";
    }

    /**
     * Get module template path.
     */
    public function getModuleTemplate(string $module, string $template): string
    {
        return "{$module}::{$template}";
    }

    /**
     * Get shared template path.
     */
    public function getSharedTemplate(string $type, string $template): string
    {
        return "{$type}::{$template}";
    }

    /**
     * Get component template path.
     */
    public function getComponentTemplate(string $type, string $component): string
    {
        return "{$type}::{$component}";
    }

    /**
     * Get all available themes.
     */
    public function getAvailableThemes(): array
    {
        return ['bootstrap', 'tailwind'];
    }

    /**
     * Check if theme has specific template.
     */
    public function hasThemeTemplate(string $theme, string $type, string $template): bool
    {
        // This would check if template file exists
        // Implementation depends on your template renderer
        return true; // Simplified for now
    }

    /**
     * Get fallback template if theme-specific doesn't exist.
     */
    public function getFallbackTemplate(string $type, string $template): string
    {
        // Default to bootstrap theme as fallback
        return "bootstrap_{$type}::{$template}";
    }
}
