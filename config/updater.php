<?php

return [
    // GitHub repository that publishes releases (tags like v1.2.30).
    'owner' => env('GITHUB_OWNER', 'asenov78'),
    'repo'  => env('GITHUB_REPO', 'Dravion-SaaS-Starter-Kit'),

    // Personal access token — required only for private repos / higher rate limits.
    'token' => env('GITHUB_TOKEN', ''),

    // Where downloaded release archives are staged before extraction.
    'work_dir' => storage_path('app/updates'),

    // HMAC-SHA256 secret for GitHub webhook signature verification.
    // Set in GitHub repo → Settings → Webhooks → Secret. Leave empty to skip verification (dev only).
    'webhook_secret' => env('GITHUB_WEBHOOK_SECRET', ''),

    // Paths never overwritten by an update (relative to project root).
    'protected_paths' => [
        '.env',
        'config/dravion.php',
        'config/google2fa.php',
        'storage',
        'vendor',
        'node_modules',
        'public/storage',
        'public/build',
    ],
];
