<?php

declare(strict_types=1);

return [
    'templates' => [
        'extension' => 'phtml',
        'paths'     => [
            // Theme-based templates (using Paths service)
            'bootstrap' => ['@templates_bootstrap'],
            'tailwind' => ['@templates_tailwind'],

            // Theme layouts
            'bootstrap_layouts' => ['@templates_bootstrap_layouts'],
            'tailwind_layouts' => ['@templates_tailwind_layouts'],

            // Theme pages
            'bootstrap_pages' => ['@templates_bootstrap_pages'],
            'tailwind_pages' => ['@templates_tailwind_pages'],

            // Theme partials
            'bootstrap_partials' => ['@templates_bootstrap_partials'],
            'tailwind_partials' => ['@templates_tailwind_partials'],

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
