<?php
$files = glob('database/migrations/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (!str_contains($content, 'getConnection()->getName()')) {
        echo basename($file) . PHP_EOL;
    }
}
