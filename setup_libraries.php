<?php
/**
 * Swalayan CI4 Library Setup Script
 * This script installs all required libraries for the application
 */

echo "==============================================\n";
echo "Swalayan POS - Library Installation Script\n";
echo "==============================================\n\n";

// Check if Composer is installed
echo "Checking Composer installation...\n";
$composerExists = false;

// Windows
exec('where composer 2>NUL', $output, $returnVar);
if ($returnVar === 0) {
    $composerExists = true;
    $composerCommand = 'composer';
}

// Linux/Mac (if Windows check failed)
if (!$composerExists) {
    exec('which composer 2>/dev/null', $output, $returnVar);
    if ($returnVar === 0) {
        $composerExists = true;
        $composerCommand = 'composer';
    }
}

// Try with composer.phar
if (!$composerExists) {
    if (file_exists('composer.phar')) {
        $composerExists = true;
        $composerCommand = 'php composer.phar';
    } else {
        // Try to download composer.phar
        echo "Composer not found. Attempting to download composer.phar...\n";
        $url = 'https://getcomposer.org/composer.phar';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Failed to download composer: " . curl_error($ch) . "\n";
            curl_close($ch);
            die("Please install Composer first: https://getcomposer.org/download/\n");
        }
        curl_close($ch);
        file_put_contents('composer.phar', $data);
        chmod('composer.phar', 0755);
        $composerExists = true;
        $composerCommand = 'php composer.phar';
    }
}

if ($composerExists) {
    echo "Composer is installed and ready.\n\n";
} else {
    die("Composer is not installed. Please install Composer first: https://getcomposer.org/download/\n");
}

// Check PHP version
echo "Checking PHP version...\n";
$requiredPhpVersion = '8.1.0';
$currentPhpVersion = PHP_VERSION;
if (version_compare($currentPhpVersion, $requiredPhpVersion, '<')) {
    die("PHP $requiredPhpVersion or higher is required. Current version is $currentPhpVersion\n");
}
echo "PHP version $currentPhpVersion is compatible.\n\n";

// Install all libraries from composer.json
echo "Installing required libraries...\n";
$command = "$composerCommand install --no-dev";
echo "Running: $command\n";

if (PHP_SAPI === 'cli') {
    // If running from command line
    passthru($command, $returnCode);
    if ($returnCode !== 0) {
        die("Composer installation failed with return code $returnCode\n");
    }
} else {
    // If running from browser
    echo "<pre>";
    passthru($command, $returnCode);
    echo "</pre>";
    if ($returnCode !== 0) {
        die("<p style='color:red'>Composer installation failed with return code $returnCode</p>");
    }
}

echo "\n==============================================\n";
echo "Library installation completed successfully!\n";
echo "The following libraries have been installed:\n";
echo "- QR Code Generator (endroid/qr-code)\n";
echo "- Barcode Generator (picqer/php-barcode-generator)\n";
echo "- Excel Library (phpoffice/phpspreadsheet)\n";
echo "==============================================\n";

echo "\nExecuting database setup script...\n";
include 'setup_database.php';

?>
