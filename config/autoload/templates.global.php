<?php

declare(strict_types=1);

return [
    'templates' => [
        'extension' => 'phtml',
        'paths'     => [
            // Theme-based templates (using Paths service)
            'bootstrap' => ['@templates_bootstrap'],
            'tailwind' => ['@templates_tailwind'],
            'svelte' => ['@templates_svelte'],
            'vue' => ['@templates_vue'],
            'react' => ['@templates_react'],

            // Theme layouts
            'bootstrap_layouts' => ['@templates_bootstrap_layouts'],
            'tailwind_layouts' => ['@templates_tailwind_layouts'],
            'svelte_layouts' => ['@templates_svelte_layouts'],
            'vue_layouts' => ['@templates_vue_layouts'],
            'react_layouts' => ['@templates_react_layouts'],

            // Theme pages
            'bootstrap_pages' => ['@templates_bootstrap_pages'],
            'tailwind_pages' => ['@templates_tailwind_pages'],
            'svelte_pages' => ['@templates_svelte_pages'],
            'vue_pages' => ['@templates_vue_pages'],
            'react_pages' => ['@templates_react_pages'],

            // Theme partials
            'bootstrap_partials' => ['@templates_bootstrap_partials'],
            'tailwind_partials' => ['@templates_tailwind_partials'],
            'svelte_partials' => ['@templates_svelte_partials'],
            'vue_partials' => ['@templates_vue_partials'],
            'react_partials' => ['@templates_react_partials'],

            // Module templates (using Paths service)
            'auth' => ['@templates_auth'],
            'contact' => ['@templates_contact'],
            'user' => ['@templates_user'],
            'page' => ['@templates_page'],
            'session' => ['@templates_session'],

            // Shared templates
            'shared' => ['@templates_shared'],
            'error' => ['@templates_error'],
            'email' => ['@templates_email'],

            // Component templates
            'components' => ['@templates_components'],
            'forms' => ['@templates_forms'],
            'ui' => ['@templates_ui'],

            // Backward compatibility (will be removed after migration)
            'layout' => ['@templates_tailwind_layouts'], // Default to ...
        ],
    ],
];
