<?php

/**
 * Local configuration.
 *
 * Duplicate this file as `local.php` and change its settings as required.
 * `local.php` is ignored by git and safe to use for local and sensitive data like usernames and passwords.
 */

declare(strict_types=1);

$baseUrl = 'http://localhost';

return [
    'application' => [
        'url' => $baseUrl,
    ],
    'routes'      => [
        'page' => [
            'about'      => 'about',
            'who-we-are' => 'who-we-are',
        ],
    ],
];
