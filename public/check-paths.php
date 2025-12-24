<?php
header('Content-Type: text/plain');

echo "--- DIRECTORY CHECK ---\n";
echo "Current File: " . __FILE__ . "\n";
echo "Current Dir: " . __DIR__ . "\n";

$public_html = '/home/pawondbi/public_html';
$storage_link = $public_html . '/storage';
$repo_storage = '/home/pawondbi/repositories/pawon3d/storage/app/public';

echo "\n--- PUBLIC_HTML CHECK ---\n";
if (is_dir($public_html)) {
    echo "public_html exists: YES\n";
} else {
    echo "public_html exists: NO (Is the path correct?)\n";
}

echo "\n--- STORAGE LINK STATUS ---\n";
if (file_exists($storage_link)) {
    if (is_link($storage_link)) {
        echo "storage link exists: YES (It is a Symlink)\n";
        echo "Points to: " . readlink($storage_link) . "\n";
    } else {
        echo "storage exists: YES (BUT IT IS A REGULAR DIRECTORY, NOT A LINK!)\n";
    }
} else {
    echo "storage link exists: NO\n";
}

echo "\n--- REPO STORAGE CHECK ---\n";
if (is_dir($repo_storage)) {
    echo "Repo storage path exists: YES\n";
    echo "Contents of repo storage:\n";
    $files = scandir($repo_storage);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "  - $file\n";
            if (is_dir($repo_storage . '/' . $file)) {
                $subfiles = scandir($repo_storage . '/' . $file);
                foreach ($subfiles as $sf) {
                    if ($sf != '.' && $sf != '..') echo "    - $sf\n";
                }
            }
        }
    }
} else {
    echo "Repo storage path exists: NO (Check path: $repo_storage)\n";
}

echo "\n--- TEST ACCESS VIA LINK ---\n";
$testFile = $storage_link . '/user_images/peZz16VwZZy9jphvxHFu63AexgwryBFu4a2h4jL3.png';
if (file_exists($testFile)) {
    echo "Access via symlink SUCCESS: File found!\n";
} else {
    echo "Access via symlink FAILED: File NOT found at $testFile\n";
}
