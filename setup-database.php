<?php
/**
 * Swalayan CI4 Database Setup Script
 * 
 * This script creates the database and runs migrations
 */

echo "====================================================================\n";
echo "             SWALAYAN CI4 DATABASE SETUP SCRIPT                     \n";
echo "====================================================================\n\n";

// Check if .env file exists
if (!file_exists('.env')) {
    echo "Error: .env file does not exist. Please run install.php first.\n";
    exit(1);
}

// Load database configuration from .env
$env = parse_ini_file('.env');
$db_host = $env['database.default.hostname'] ?? 'localhost';
$db_name = $env['database.default.database'] ?? 'swalayan_db';
$db_user = $env['database.default.username'] ?? 'root';
$db_pass = $env['database.default.password'] ?? '';
$db_port = $env['database.default.port'] ?? '3306';

// Confirm database information
echo "Database Configuration:\n";
echo "  Host: {$db_host}\n";
echo "  Database: {$db_name}\n";
echo "  Username: {$db_user}\n";
echo "  Password: " . (empty($db_pass) ? "(empty)" : "****") . "\n";
echo "  Port: {$db_port}\n\n";

echo "Is this database information correct? (y/n): ";
$confirm = strtolower(trim(fgets(STDIN)));
if ($confirm !== 'y') {
    echo "\nPlease enter the correct database information:\n";
    
    echo "Database Host (default: {$db_host}): ";
    $input = trim(fgets(STDIN));
    $db_host = !empty($input) ? $input : $db_host;
    
    echo "Database Port (default: {$db_port}): ";
    $input = trim(fgets(STDIN));
    $db_port = !empty($input) ? $input : $db_port;
    
    echo "Database Name (default: {$db_name}): ";
    $input = trim(fgets(STDIN));
    $db_name = !empty($input) ? $input : $db_name;
    
    echo "Database Username (default: {$db_user}): ";
    $input = trim(fgets(STDIN));
    $db_user = !empty($input) ? $input : $db_user;
    
    echo "Database Password: ";
    $db_pass = trim(fgets(STDIN));

    // Update .env file
    $env_content = file_get_contents('.env');
    $env_content = preg_replace('/database\.default\.hostname = .*/', "database.default.hostname = {$db_host}", $env_content);
    $env_content = preg_replace('/database\.default\.database = .*/', "database.default.database = {$db_name}", $env_content);
    $env_content = preg_replace('/database\.default\.username = .*/', "database.default.username = {$db_user}", $env_content);
    $env_content = preg_replace('/database\.default\.password = .*/', "database.default.password = {$db_pass}", $env_content);
    $env_content = preg_replace('/database\.default\.port = .*/', "database.default.port = {$db_port}", $env_content);
    file_put_contents('.env', $env_content);
}

// Try to connect to MySQL
echo "\nConnecting to MySQL... ";
try {
    $pdo = new PDO(
        "mysql:host={$db_host};port={$db_port}", 
        $db_user, 
        $db_pass
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully\n";
} catch (PDOException $e) {
    echo "Failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Create database if it doesn't exist
echo "\nChecking database {$db_name}... ";
try {
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$db_name}'");
    if (!$stmt->fetch()) {
        echo "Not found, creating... ";
        $pdo->exec("CREATE DATABASE `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "Created successfully\n";
    } else {
        echo "Already exists\n";
        
        echo "\nWARNING: The database '{$db_name}' already exists. Running migrations may overwrite existing data.\n";
        echo "Do you want to continue? (y/n): ";
        $confirm = strtolower(trim(fgets(STDIN)));
        if ($confirm !== 'y') {
            echo "\nDatabase setup aborted.\n";
            exit(0);
        }
    }
} catch (PDOException $e) {
    echo "Failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Run migrations using CodeIgniter's spark command
echo "\nRunning database migrations... ";
passthru('php spark migrate', $migrate_status);
if ($migrate_status !== 0) {
    echo "Failed\n";
    echo "Attempting to fix common migration issues...\n";
    
    // Try to fix audit_logs table structure
    passthru('php fix-audit-logs.php', $fix_status);
    
    // Try migration again
    echo "\nRetrying migrations... ";
    passthru('php spark migrate', $migrate_status);
    if ($migrate_status !== 0) {
        echo "Failed again. Please check database structure manually.\n";
        exit(1);
    } else {
        echo "Migrations completed successfully after fixes\n";
    }
} else {
    echo "Completed successfully\n";
}

// Seed initial data
echo "\nSeeding initial data... ";
passthru('php spark db:seed InitialDataSeeder', $seed_status);
if ($seed_status !== 0) {
    echo "Failed\n";
    echo "Attempting to fix audit_logs table structure to resolve foreign key issues...\n";
    
    // Try to fix audit_logs table structure
    passthru('php fix-audit-logs.php', $fix_status);
    
    // Try seeding again
    echo "\nRetrying seeding... ";
    passthru('php spark db:seed InitialDataSeeder', $seed_status);
    if ($seed_status !== 0) {
        echo "Failed again. Some setup steps may not have completed properly.\n";
        echo "You may need to manually check and fix the database structure.\n";
        // Continue anyway, don't exit
    } else {
        echo "Seeding completed successfully after fixes\n";
    }
} else {
    echo "Completed successfully\n";
}



// Account creation will be handled by the KaryawanSeeder
echo "\nOwner account will be created with the following details:";
echo "\nName: {$owner_name}";
echo "\nEmail: {$owner_email}";
echo "\nDefault password: owner123 (Please change this after first login)";
echo "\nThis email will be used for OTP login authentication.\n";

echo "\nDatabase setup complete!\n";
echo "\n====================================================================\n";
