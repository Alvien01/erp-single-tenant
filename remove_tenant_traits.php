<?php

$dir = __DIR__ . '/app/Models';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        if (strpos($content, 'BelongsToTenant') !== false) {
            $lines = explode("\n", $content);
            $newLines = [];
            foreach ($lines as $line) {
                if (strpos($line, 'use App\\Traits\\BelongsToTenant;') !== false) continue;
                if (trim($line) === 'use BelongsToTenant;') continue;
                $newLines[] = $line;
            }
            file_put_contents($path, implode("\n", $newLines));
            echo "Removed BelongsToTenant from " . basename($path) . "\n";
        }
    }
}
