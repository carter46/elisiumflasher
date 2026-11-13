<?php
declare(strict_types=1);

/**
 * Multi-site Elysium hosts for Institutional Tracker fan-out.
 * Add another product by appending a block — no code rewrite required.
 *
 * Prefer env vars for secrets in production:
 *   TRACKER_KEY_FLASHER, TRACKER_KEY_SERVER, TRACKER_BASE_FLASHER, TRACKER_BASE_SERVER
 */
return [
    'sites' => [
        [
            'id' => 'flasher',
            'name' => 'Elysium Flasher',
            'base_url' => rtrim((string) (getenv('TRACKER_BASE_FLASHER') ?: 'http://localhost:8000'), '/'),
            'api_key' => (string) (getenv('TRACKER_KEY_FLASHER') ?: ''),
            'enabled' => true,
            'timeout_ms' => 4000,
        ],
        [
            'id' => 'server',
            'name' => 'Elysium Server',
            'base_url' => rtrim((string) (getenv('TRACKER_BASE_SERVER') ?: 'http://localhost:8001'), '/'),
            'api_key' => (string) (getenv('TRACKER_KEY_SERVER') ?: ''),
            'enabled' => true,
            'timeout_ms' => 4000,
        ],
        // Example third site (disabled until configured):
        // [
        //     'id' => 'region_b',
        //     'name' => 'Elysium Region B',
        //     'base_url' => 'https://region-b.example.com',
        //     'api_key' => getenv('TRACKER_KEY_REGION_B') ?: '',
        //     'enabled' => false,
        //     'timeout_ms' => 4000,
        // ],
    ],
    'otp' => [
        'loading_ms' => 5000,
        'post_email_wait_ms' => 2000,
    ],
];
