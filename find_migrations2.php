<?php
$files = glob('database/migrations/*.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    if (!str_contains($content, 'getConnection()->getName()')) {
        preg_match('/Schema::(create|table)\(\'([^\']+)\'/', $content, $matches);
        if ($matches) {
            echo basename($file) . ' -> ' . $matches[1] . ' ' . $matches[2] . PHP_EOL;
        } else {
            echo basename($file) . ' -> No table found' . PHP_EOL;
        }
    }
}
