<?php
// Letakkan di /home/pawondbi/public_html/debug-pwa.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require '/home/pawondbi/repositories/pawon3d/vendor/autoload.php';
    $app = require_once '/home/pawondbi/repositories/pawon3d/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

    header('Content-Type: text/plain');

    echo "--- ENVIRONMENT CHECK ---\n";
    echo "APP_URL (env): " . env('APP_URL') . "\n";
    echo "APP_URL (config): " . config('app.url') . "\n";
    echo "PUBLIC_PATH (env): " . env('PUBLIC_PATH') . "\n";
    echo "Laravel Public Path: " . public_path() . "\n";
    echo "Laravel Base Path: " . base_path() . "\n";

    echo "\n--- PWA ROUTE TEST ---\n";
    $url = rtrim(config('app.url'), '/') . '/manifest.json';
    echo "Testing URL: $url\n";
    
    $context = stream_context_create(['http' => ['ignore_errors' => true]]);
    $content = @file_get_contents($url, false, $context);
    
    if ($content !== false) {
        echo "Response starts with: " . substr(trim($content), 0, 50) . "...\n";
        if (strpos($content, '<!DOCTYPE html>') !== false) {
            echo "RESULT: manifest.json returned an HTML page (likely 404 or Error).\n";
        } elseif (strpos($content, '{') === 0) {
            echo "RESULT: manifest.json returned JSON! (Success?)\n";
        } else {
            echo "RESULT: Unknown content type.\n";
        }
    } else {
        echo "RESULT: FAILED to reach manifest.json URL.\n";
    }

    echo "\n--- ICON FILE CHECK ---\n";
    $iconPath = public_path('images/icons/icon-72x72.png');
    echo "Checking icon at: $iconPath\n";
    if (file_exists($iconPath)) {
        echo "Icon file exists: YES\n";
    } else {
        echo "Icon file exists: NO\n";
        // Coba cek di repository juga
        $repoIcon = '/home/pawondbi/repositories/pawon3d/public/images/icons/icon-72x72.png';
        if (file_exists($repoIcon)) echo "Found icon in repository: YES\n";
    }

} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
