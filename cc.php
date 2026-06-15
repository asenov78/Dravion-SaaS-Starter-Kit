<?php
// Manual .env parser
$env = [];
foreach (file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\n\r\"'");
}

$licenseKey = $env['DRAVION_LICENSE_KEY'] ?? '';
$appUrl     = $env['APP_URL'] ?? '';
$domain     = parse_url($appUrl, PHP_URL_HOST) ?? '';

echo "license_key: [{$licenseKey}]\n";
echo "domain: [{$domain}]\n\n";

// Try verify (old endpoint name)
foreach (['validate', 'verify'] as $ep) {
    $url = 'https://apsbg.com/dravion-server/api/router.php?endpoint=' . $ep;
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['license_key' => $licenseKey, 'domain' => $domain]),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "$ep → HTTP $code: $raw\n";
}
