<?php

$file = __DIR__ . '/bootstrap/cache/config.php';

if (file_exists($file)) {
    unlink($file);
    echo "✅ File 'config.php' has been deleted.";
} else {
    echo "⚠️ File 'config.php' does not exist.";
}
