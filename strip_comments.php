<?php

$dir = new RecursiveDirectoryIterator('app/Filament/Resources');
$iterator = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    
    // Remove Laravel/Filament style block comments
    $content = preg_replace('/\/\*\s*\|-{10,}[\s\S]*?\|-{10,}\s*\*\//', '', $content);
    
    // Remove standard multi-line comments /* ... */
    $content = preg_replace('/\/\*(?!\*).*?\*\//s', '', $content);
    
    // Remove single line comments // ...
    $content = preg_replace('/^\s*\/\/.*$/m', '', $content);

    file_put_contents($path, $content);
}
echo "Comments removed.\n";
