<?php
// Letakkan file ini di folder 'public' di hosting (repositories/pawon3d/public/debug.php)

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

header('Content-Type: text/plain');

echo "--- LARAVEL DIAGNOSTICS ---\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "Base Path: " . base_path() . "\n";
echo "Public Path: " . public_path() . "\n";
echo "Storage Path (app/public): " . storage_path('app/public') . "\n";
echo "Storage Link Path: " . public_path('storage') . "\n";

echo "\n--- SYMLINK STATUS ---\n";
$target = public_path('storage');
if (file_exists($target)) {
    if (is_link($target)) {
        echo "Symlink 'storage' EXISTS and is a VALID link.\n";
        echo "Pointing to: " . readlink($target) . "\n";
    } else {
        echo "WARNING: 'storage' exists but is a DIRECTORY, not a symlink!\n";
    }
} else {
    echo "ERROR: 'storage' link/folder does NOT exist in public path.\n";
}

echo "\n--- FILE CHECK ---\n";
$testFile = 'user_images/peZz16VwZZy9jphvxHFu63AexgwryBFu4a2h4jL3.png';
$fullPath = storage_path('app/public/' . $testFile);
if (file_exists($fullPath)) {
    echo "Image file EXISTS in storage: $fullPath\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms($fullPath)), -4) . "\n";
} else {
    echo "ERROR: Image file NOT FOUND in storage: $fullPath\n";
}
