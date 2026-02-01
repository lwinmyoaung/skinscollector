<?php
// debug_images.php - Place this in your 'public' folder
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Server Image Path Diagnostic</h1>";
echo "<pre style='background:#f5f5f5; padding:15px; border-radius:5px;'>";

$baseDir = __DIR__; // This is the public folder
$linkPath = $baseDir . '/adminimages';

echo "<strong>1. Checking Paths</strong>\n";
echo "Public Path: " . $baseDir . "\n";
echo "Checking Link: " . $linkPath . "\n";

// Check if public/adminimages exists
if (file_exists($linkPath)) {
    echo "[PASS] 'public/adminimages' exists.\n";
} else {
    echo "[FAIL] 'public/adminimages' does NOT exist.\n";
}

echo "\n<strong>2. Checking Link Status</strong>\n";
// Check if it is a symbolic link
if (is_link($linkPath)) {
    echo "[INFO] 'public/adminimages' is a SYMBOLIC LINK.\n";
    $target = readlink($linkPath);
    echo "       Target: " . $target . "\n";
    
    // Check if target exists
    if (file_exists($target) || file_exists(dirname($linkPath) . '/' . $target)) {
         echo "       [PASS] Target path exists.\n";
    } else {
         echo "       [FAIL] Target path does NOT exist.\n";
    }
} else {
    if (is_dir($linkPath)) {
        echo "[WARN] 'public/adminimages' is a REAL DIRECTORY (not a link).\n";
        echo "       This is likely the problem! It should be a link to your storage.\n";
    } else {
        echo "[FAIL] 'public/adminimages' is neither a link nor a directory.\n";
    }
}

echo "\n<strong>3. Checking Specific Missing File</strong>\n";
// Check specific file from your error log
$testFile = 'photo/x5YEQ3jjn7X1n2w5wSpgjeYiu7LprhNYZD41xfLB.jpg';
$fullPath = $linkPath . '/' . $testFile;

echo "Looking for: " . $testFile . "\n";
echo "Full path: " . $fullPath . "\n";

if (file_exists($fullPath)) {
    echo "[PASS] File found!\n";
    echo "       Size: " . filesize($fullPath) . " bytes\n";
    echo "       Perms: " . substr(sprintf('%o', fileperms($fullPath)), -4) . "\n";
} else {
    echo "[FAIL] File NOT found at this path.\n";
}

echo "\n<strong>4. Directory Listing (adminimages/photo)</strong>\n";
$photoDir = $linkPath . '/photo';
if (file_exists($photoDir)) {
    if (is_dir($photoDir)) {
        $files = scandir($photoDir);
        $count = 0;
        echo "Found files:\n";
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            echo " - " . $file . "\n";
            $count++;
            if ($count >= 5) {
                echo " ... and more\n";
                break;
            }
        }
        if ($count == 0) echo " (Directory is empty)\n";
    } else {
        echo "[FAIL] 'adminimages/photo' exists but is not a directory.\n";
    }
} else {
    echo "[FAIL] Directory 'adminimages/photo' does not exist.\n";
}

echo "</pre>";
