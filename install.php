<?php
/**
 * Swalayan CI4 Installation Script
 * 
 * This script checks required dependencies and installs required libraries
 */

echo "====================================================================\n";
echo "             SWALAYAN CI4 INSTALLATION SCRIPT                       \n";
echo "====================================================================\n\n";

// Check PHP version
$required_php_version = '8.1.0';
$current_php_version = PHP_VERSION;
echo "Checking PHP version... ";
if (version_compare($current_php_version, $required_php_version, '>=')) {
    echo "OK (v{$current_php_version})\n";
} else {
    echo "FAIL (v{$current_php_version})\n";
    echo "This application requires PHP {$required_php_version} or higher. Please upgrade your PHP installation.\n";
    exit(1);
}

// Check required extensions
echo "\nChecking required PHP extensions...\n";
$required_extensions = ['intl', 'mbstring', 'json', 'mysqlnd', 'curl'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    echo "  - {$ext}: ";
    if (extension_loaded($ext)) {
        echo "OK\n";
    } else {
        echo "MISSING\n";
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "\nThe following PHP extensions are missing: " . implode(', ', $missing_extensions) . "\n";
    echo "Please install them before continuing.\n";
    exit(1);
}

// Check if Composer is installed
echo "\nChecking for Composer... ";
exec('composer --version 2>&1', $composer_output, $composer_exit_code);
if ($composer_exit_code !== 0) {
    echo "MISSING\n";
    echo "Composer is not installed or not in your PATH. Please install Composer: https://getcomposer.org/download/\n";
    exit(1);
} else {
    echo "OK (" . trim($composer_output[0]) . ")\n";
}

// Install dependencies using Composer
echo "\nInstalling PHP dependencies using Composer...\n";
passthru('composer update', $composer_update_status);
if ($composer_update_status !== 0) {
    echo "\nFailed to update Composer dependencies.\n";
    exit(1);
}

// Create .env file if it doesn't exist
echo "\nSetting up .env file... ";
if (!file_exists('.env')) {
    if (copy('env', '.env')) {
        echo "Created successfully\n";
    } else {
        echo "Failed to create .env file\n";
        exit(1);
    }
} else {
    echo "Already exists\n";
}

// Configure the app URL
echo "\nPlease enter your base URL (default: http://localhost:8080/): ";
$base_url = trim(fgets(STDIN));
if (empty($base_url)) {
    $base_url = 'http://localhost:8080/';
}
$env_content = file_get_contents('.env');
$env_content = preg_replace('/app\.baseURL = .*/', "app.baseURL = '{$base_url}'", $env_content);
file_put_contents('.env', $env_content);

// Email configuration will be handled during database setup

// Create a default Email configuration
$email_config = file_get_contents('app/Config/Email.php');
file_put_contents('app/Config/Email.php', $email_config);

echo "\nInstallation setup complete!\n";
echo "You may now run 'php setup-database.php' to set up the database.\n";
echo "\n====================================================================\n";
