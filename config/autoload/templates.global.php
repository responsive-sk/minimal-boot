<?php

declare(strict_types=1);

return [
    'templates' => [
        'extension' => 'phtml',
        'paths'     => [
            // App Templates
            'app' => ['src/App/templates'],

            // Contact Templates
            'contact' => [getcwd() . '/src/Contact/templates'],

            // Auth Templates
            'auth' => ['src/Auth/templates'],

            // Session Templates
            'session' => ['src/Session/templates'],

            // Layout Templates (Shared)
            'layout' => [getcwd() . '/src/Shared/templates/layout'],

            // Error Templates (Shared)
            'error' => ['src/Shared/templates/error'],

            // Partial Templates (Shared)
            'partial' => ['src/Shared/templates/partial'],

            // Page Templates
            'page' => ['src/Page/templates/page'],
        ],
    ],
];
