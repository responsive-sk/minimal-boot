<?php

/**
 * Paths configuration for slim4-paths package.
 *
 * This configuration follows the official slim4-paths documentation format.
 * See: https://github.com/responsive-sk/slim4-paths
 */

declare(strict_types=1);

return [
    'paths' => [
        // Base path - project root directory
        'base_path' => __DIR__ . '/../..', // Fixed for shared hosting compatibility

        // Custom paths configuration (relative to base_path)
        'custom_paths' => [
            // Core directories
            'shared_templates' => 'src/Shared/templates',
            'content'   => 'content',
            'data'      => 'var/data',
            'db'        => 'var/db',
            'logs'      => 'var/logs',
            'cache'     => 'var/cache',
            'storage'   => 'var/storage',
            'migrations' => 'var/migrations',
            'uploads'   => 'public/uploads',
            'downloads' => 'public/downloads',

            // Development directories
            'tests' => 'test',
            'docs'  => 'docs',
            'bin'   => 'bin',

            // Asset directories
            'css'    => 'public/css',
            'js'     => 'public/js',
            'images' => 'public/images',
            'fonts'  => 'public/fonts',

            // Module directories
            'modules'     => 'modules',
            'user_module' => 'modules/User',
            'mark_module' => 'modules/Mark',
            'blog_module' => 'modules/Blog',

            // Template root directory
            'templates' => 'templates',

            // Theme-based templates
            'templates_themes' => 'templates/themes',
            'templates_bootstrap' => 'templates/themes/bootstrap',
            'templates_tailwind' => 'templates/themes/tailwind',
            'templates_svelte' => 'templates/themes/svelte',
            'templates_vue' => 'templates/themes/vue',
            'templates_react' => 'templates/themes/react',

            // Theme layouts
            'templates_bootstrap_layouts' => 'templates/themes/bootstrap/layouts',
            'templates_tailwind_layouts' => 'templates/themes/tailwind/layouts',
            'templates_svelte_layouts' => 'templates/themes/svelte/layouts',
            'templates_vue_layouts' => 'templates/themes/vue/layouts',
            'templates_react_layouts' => 'templates/themes/react/layouts',

            // Theme pages
            'templates_bootstrap_pages' => 'templates/themes/bootstrap/pages',
            'templates_tailwind_pages' => 'templates/themes/tailwind/pages',
            'templates_svelte_pages' => 'templates/themes/svelte/pages',
            'templates_vue_pages' => 'templates/themes/vue/pages',
            'templates_react_pages' => 'templates/themes/react/pages',

            // Theme partials
            'templates_bootstrap_partials' => 'templates/themes/bootstrap/partials',
            'templates_tailwind_partials' => 'templates/themes/tailwind/partials',
            'templates_svelte_partials' => 'templates/themes/svelte/partials',
            'templates_vue_partials' => 'templates/themes/vue/partials',
            'templates_react_partials' => 'templates/themes/react/partials',

            // Module templates
            'templates_modules' => 'templates/modules',
            'templates_auth' => 'templates/modules/auth',
            'templates_contact' => 'templates/modules/contact',
            'templates_user' => 'templates/modules/user',
            'templates_page' => 'templates/modules/page',
            'templates_session' => 'templates/modules/session',

            // Shared templates
            'templates_shared' => 'templates/shared',
            'templates_error' => 'templates/shared/error',
            'templates_email' => 'templates/shared/email',

            // Component templates
            'templates_components' => 'templates/components',
            'templates_forms' => 'templates/components/forms',
            'templates_ui' => 'templates/components/ui',
        ],

        // Template namespace paths for ConfigBasedTemplatePathProvider
        'templates' => [
            // Theme-based templates
            'bootstrap' => 'templates/themes/bootstrap',
            'tailwind' => 'templates/themes/tailwind',

            // Theme layouts
            'bootstrap_layouts' => 'templates/themes/bootstrap/layouts',
            'tailwind_layouts' => 'templates/themes/tailwind/layouts',

            // Theme pages
            'bootstrap_pages' => 'templates/themes/bootstrap/pages',
            'tailwind_pages' => 'templates/themes/tailwind/pages',

            // Theme partials
            'bootstrap_partials' => 'templates/themes/bootstrap/partials',
            'tailwind_partials' => 'templates/themes/tailwind/partials',

            // Module templates
            'auth' => 'templates/modules/auth',
            'contact' => 'templates/modules/contact',
            'user' => 'templates/modules/user',
            'page' => 'templates/modules/page',
            'session' => 'templates/modules/session',

            // Shared templates
            'shared' => 'templates/shared',
            'error' => 'templates/shared/error',
            'email' => 'templates/shared/email',

            // Component templates
            'components' => 'templates/components',
            'forms' => 'templates/components/forms',
            'ui' => 'templates/components/ui',

            // Backward compatibility
            'layout' => 'templates/themes/bootstrap/layouts', // Default to Bootstrap

            // Cache subdirectories
            'config_cache' => 'var/cache/config',
            'twig_cache'   => 'var/cache/twig',
            'routes_cache' => 'var/cache/routes',

            // Runtime directories
            'tmp'      => 'var/tmp',
            'sessions' => 'var/sessions',
        ],
    ],
];
