<?php

declare(strict_types=1);

namespace Minimal\Shared\Service;

use ResponsiveSk\Slim4Session\SessionInterface;

/**
 * Theme management service.
 */
class ThemeService
{
    private const SESSION_THEME_KEY = 'selected_theme';
    private const DEFAULT_THEME = 'svelte';

    private const AVAILABLE_THEMES = [
        'bootstrap' => [
            'name' => 'Bootstrap 5',
            'description' => 'Modern Bootstrap 5 with custom styling',
            'css' => 'themes/bootstrap/assets/main.css',
            'js' => 'themes/bootstrap/assets/main.js',
            'build_dir' => 'src/Assets/bootstrap',
            'output_dir' => 'public/themes/bootstrap/assets',
            'template_path' => 'templates/themes/bootstrap',
        ],
        'tailwind' => [
            'name' => 'Tailwind CSS',
            'description' => 'Utility-first CSS framework with Alpine.js',
            'css' => 'themes/main/assets/main.css',
            'js' => 'themes/main/assets/main.js',
            'build_dir' => 'src/Assets/main',
            'output_dir' => 'public/themes/main/assets',
            'template_path' => 'templates/themes/tailwind',
        ],
        'svelte' => [
            'name' => 'Svelte',
            'description' => 'Modern interactive components with glassmorphism design',
            'css' => 'themes/svelte/assets/main.css',
            'js' => 'themes/svelte/assets/main.js',
            'build_dir' => 'src/Assets/svelte',
            'output_dir' => 'public/themes/svelte/assets',
            'template_path' => 'templates/themes/svelte',
        ],
        'vue' => [
            'name' => 'Vue Cyberpunk',
            'description' => 'Cyberpunk interface with neural network aesthetics and neon effects',
            'css' => 'themes/vue/assets/style.css',
            'js' => 'themes/vue/assets/main.js',
            'build_dir' => 'src/Assets/vue',
            'output_dir' => 'public/themes/vue/assets',
            'template_path' => 'templates/themes/vue',
        ],
        'react' => [
            'name' => 'Forest Calm',
            'description' => 'Peaceful digital forest with React and nature-inspired healing design',
            'css' => 'themes/react/assets/style.css',
            'js' => 'themes/react/assets/main.js',
            'build_dir' => 'src/Assets/react',
            'output_dir' => 'public/themes/react/assets',
            'template_path' => 'templates/themes/react',
        ],
    ];

    public function __construct(
        private SessionInterface $session
    ) {
    }

    /**
     * Get current active theme.
     */
    public function getCurrentTheme(): string
    {
        $theme = $this->session->get(self::SESSION_THEME_KEY, self::DEFAULT_THEME);
        $currentTheme = is_string($theme) ? $theme : self::DEFAULT_THEME;

        // Temporary fix: force bootstrap theme until template paths are fixed
        if ($currentTheme === 'svelte') {
            $this->session->set(self::SESSION_THEME_KEY, 'bootstrap');
            return 'bootstrap';
        }

        return $currentTheme;
    }

    /**
     * Set active theme.
     */
    public function setTheme(string $theme): void
    {
        if (!$this->isValidTheme($theme)) {
            throw new \InvalidArgumentException("Invalid theme: {$theme}");
        }

        $this->session->set(self::SESSION_THEME_KEY, $theme);
    }

    /**
     * Get theme configuration.
     *
     * @return array<string, mixed>
     */
    public function getThemeConfig(string $theme): array
    {
        if (!$this->isValidTheme($theme)) {
            throw new \InvalidArgumentException("Invalid theme: {$theme}");
        }

        return self::AVAILABLE_THEMES[$theme];
    }

    /**
     * Get current theme configuration.
     *
     * @return array<string, mixed>
     */
    public function getCurrentThemeConfig(): array
    {
        return $this->getThemeConfig($this->getCurrentTheme());
    }

    /**
     * Get all available themes.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getAvailableThemes(): array
    {
        return self::AVAILABLE_THEMES;
    }

    /**
     * Check if theme is valid.
     */
    public function isValidTheme(string $theme): bool
    {
        return array_key_exists($theme, self::AVAILABLE_THEMES);
    }

    /**
     * Switch to next theme.
     */
    public function switchToNextTheme(): string
    {
        $currentTheme = $this->getCurrentTheme();
        $themes = array_keys(self::AVAILABLE_THEMES);
        $currentIndex = array_search($currentTheme, $themes);

        $nextIndex = ($currentIndex + 1) % count($themes);
        $nextTheme = $themes[$nextIndex];

        $this->setTheme($nextTheme);

        return $nextTheme;
    }

    /**
     * Get theme CSS URL.
     */
    public function getThemeCssUrl(?string $theme = null): string
    {
        $theme = $theme ?? $this->getCurrentTheme();
        $config = $this->getThemeConfig($theme);

        return is_string($config['css']) ? $config['css'] : '';
    }

    /**
     * Get theme JS URL.
     */
    public function getThemeJsUrl(?string $theme = null): string
    {
        $theme = $theme ?? $this->getCurrentTheme();
        $config = $this->getThemeConfig($theme);

        return is_string($config['js']) ? $config['js'] : '';
    }

    /**
     * Check if current theme is Bootstrap.
     */
    public function isBootstrap(): bool
    {
        return $this->getCurrentTheme() === 'bootstrap';
    }

    /**
     * Check if current theme is Tailwind.
     */
    public function isTailwind(): bool
    {
        return $this->getCurrentTheme() === 'tailwind';
    }

    /**
     * Get theme template path.
     */
    public function getThemeTemplatePath(?string $theme = null): string
    {
        $theme = $theme ?? $this->getCurrentTheme();
        $config = $this->getThemeConfig($theme);

        return is_string($config['template_path']) ? $config['template_path'] : '';
    }

    /**
     * Get current theme template path.
     */
    public function getCurrentThemeTemplatePath(): string
    {
        return $this->getThemeTemplatePath();
    }
}
