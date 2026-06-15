<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$result = \Illuminate\Support\Facades\Auth::attempt([
    'email'    => 'admin@dravion.local',
    'password' => 'admin123',
]);
echo "Auth::attempt result: " . ($result ? 'TRUE' : 'FALSE') . "\n";
echo "Session driver: " . config('session.driver') . "\n";
echo "APP_URL: " . config('app.url') . "\n";

// Check form action URL
echo "route('login'): " . route('login') . "\n";
