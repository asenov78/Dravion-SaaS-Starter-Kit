<?php
$dirs = [
    __DIR__ . '/../storage/framework/views',
    __DIR__ . '/../storage/framework/cache/data',
];
$deleted = 0;
foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    foreach (glob($dir . '/*') as $file) {
        if (is_file($file)) { unlink($file); $deleted++; }
    }
}
echo "OK — deleted $deleted cached files.";
