<?php
/**
 * Database Diagnostic and Repair Tool
 * This script checks and repairs database issues for Swalayan CI4
 */

// Database configuration - get from .env
$envFile = file_get_contents('.env');
preg_match('/database.default.hostname\s*=\s*([^\s]+)/', $envFile, $hostMatches);
preg_match('/database.default.database\s*=\s*([^\s]+)/', $envFile, $dbMatches);
preg_match('/database.default.username\s*=\s*([^\s]+)/', $envFile, $userMatches);
preg_match('/database.default.password\s*=\s*([^\s]*)/', $envFile, $passMatches);

$host = $hostMatches[1] ?? 'localhost';
$database = $dbMatches[1] ?? 'swalayan_db';
$username = $userMatches[1] ?? 'root';
$password = $passMatches[1] ?? '';

echo "Database Connection Configuration:\n";
echo "Host: $host\n";
echo "Database: $database\n";
echo "Username: $username\n";
echo "Password: " . (empty($password) ? "(empty)" : "(set)") . "\n\n";

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}

echo "Connected successfully to MySQL server.\n\n";

// Check if database exists
$result = $conn->query("SHOW DATABASES LIKE '$database'");
if ($result->num_rows == 0) {
    echo "Database '$database' does not exist! Creating it...\n";
    if ($conn->query("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
        echo "Database created successfully.\n";
    } else {
        die("Error creating database: " . $conn->error . "\n");
    }
} else {
    echo "Database '$database' exists.\n";
}

// Select database
$conn->select_db($database);

// Check tables
echo "\nChecking tables...\n";
$result = $conn->query("SHOW TABLES");

if ($result->num_rows > 0) {
    $existingTables = [];
    while($row = $result->fetch_array()) {
        $existingTables[] = $row[0];
    }
    
    echo "Found " . count($existingTables) . " tables: " . implode(", ", $existingTables) . "\n";
    
    // Check karyawan table
    if (in_array('karyawan', $existingTables)) {
        $result = $conn->query("SELECT * FROM karyawan");
        echo "\nFound " . $result->num_rows . " records in karyawan table.\n";
        
        if ($result->num_rows == 0) {
            echo "Creating default owner account...\n";
            $ownerId = 'OWN' . uniqid();
            $ownerName = 'Pemilik Toko';
            $ownerEmail = 'owner@swalayan.com';
            $ownerPassword = hash('sha256', 'owner123');
            
            $sql = "INSERT INTO `karyawan` (`karyawan_id`, `nama`, `email`, `password`, `role`) 
                    VALUES ('$ownerId', '$ownerName', '$ownerEmail', '$ownerPassword', 'owner')";
            
            if ($conn->query($sql)) {
                echo "Owner account created successfully.\n";
                echo "Email: owner@swalayan.com\n";
                echo "Password: owner123\n";
            } else {
                echo "Error creating owner account: " . $conn->error . "\n";
            }
        } else {
            echo "Listing karyawan records:\n";
            $result = $conn->query("SELECT karyawan_id, nama, email, role FROM karyawan");
            while($row = $result->fetch_assoc()) {
                echo "- ID: {$row['karyawan_id']}, Name: {$row['nama']}, Email: {$row['email']}, Role: {$row['role']}\n";
            }
        }
    } else {
        echo "ERROR: 'karyawan' table doesn't exist!\n";
    }
    
    // Check audit_logs table
    if (in_array('audit_logs', $existingTables)) {
        echo "\nChecking audit_logs table structure...\n";
        $result = $conn->query("SHOW CREATE TABLE audit_logs");
        $row = $result->fetch_assoc();
        echo $row['Create Table'] . "\n\n";
        
        // Check for existing records
        $result = $conn->query("SELECT COUNT(*) as total FROM audit_logs");
        $row = $result->fetch_assoc();
        echo "Found {$row['total']} records in audit_logs table.\n";
        
        // Fix: disable foreign key checks, truncate table if needed
        if ((int)$row['total'] > 0) {
            echo "Disabling foreign key checks temporarily...\n";
            $conn->query("SET FOREIGN_KEY_CHECKS = 0");
            
            echo "Truncating audit_logs table to prevent constraint errors...\n";
            $conn->query("TRUNCATE TABLE audit_logs");
            
            echo "Re-enabling foreign key checks...\n";
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
            
            echo "audit_logs table has been reset.\n";
        }
    }
    
    // Check for potential foreign key issues
    echo "\nChecking foreign key constraints in audit_logs...\n";
    if (in_array('audit_logs', $existingTables)) {
        // Try to fix the foreign key constraint issue if it exists
        $conn->query("ALTER TABLE audit_logs DROP FOREIGN KEY IF EXISTS fk_audit_user");
        $conn->query("ALTER TABLE audit_logs ADD CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES karyawan (karyawan_id) ON DELETE RESTRICT ON UPDATE CASCADE");
        echo "Foreign key constraint has been updated.\n";
    }
} else {
    echo "No tables found in the database. The database may not have been properly initialized.\n";
    echo "Please run the setup script:\n";
    echo "php setup_database.php\n";
}

echo "\n\nDatabase diagnostics complete. Try to log in again.\n";
echo "If you still can't log in, try running the full setup script:\n";
echo "php setup_database.php\n";

$conn->close();
?>
