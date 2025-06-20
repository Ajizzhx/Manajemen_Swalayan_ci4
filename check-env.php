<?php
// Temporary script to check .env file for syntax issues
$envFile = file_get_contents('.env');
$lines = explode("\n", $envFile);

echo "Checking .env file for syntax errors...\n";

foreach ($lines as $lineNumber => $line) {
    $lineNumber++; // Make it 1-indexed
    $line = trim($line);
    
    // Skip empty lines and comments
    if (empty($line) || $line[0] === '#') {
        continue;
    }
    
    // Check for invalid characters or syntax issues
    if (strpos($line, '=') === false) {
        echo "Line {$lineNumber}: Missing equals sign: {$line}\n";
        continue;
    }
    
    [$key, $value] = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value);
    
    // Check for $ at beginning of value (might cause PHP issues)
    if (!empty($value) && $value[0] === '$') {
        echo "Line {$lineNumber}: Value starts with $ which might cause syntax errors: {$line}\n";
    }
}

echo "Check completed.\n";
