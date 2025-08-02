<?php

declare(strict_types=1);

return [
    'templates' => [
        'extension' => 'phtml',
        'paths'     => [
            // Contact Templates
            'contact' => ['src/Contact/templates'],

            // Auth Templates
            'auth' => ['src/Auth/templates'],

            // Session Templates
            'session' => ['src/Session/templates'],

            // Layout Templates (Shared) - Bootstrap by default
            'layout' => ['src/Shared/templates/bootstrap/layout'],

            // Error Templates (Shared)
            'error' => ['src/Shared/templates/error'],

            // Partial Templates (Shared)
            'partial' => ['src/Shared/templates/partial'],

            // Page Templates (includes index and demo)
            'page' => ['src/Page/templates'],

            // User Templates
            'user' => ['src/User/templates'],
        ],
    ],
];
