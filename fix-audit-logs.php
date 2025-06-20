<?php
/**
 * Script untuk memperbaiki struktur tabel audit_logs
 * 
 * Script ini akan memperbaiki kontraint foreign key pada tabel audit_logs
 * untuk menghindari error pada proses seeding.
 */

echo "====================================================================\n";
echo "         SWALAYAN CI4 - PERBAIKAN TABEL AUDIT_LOGS                  \n";
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

// Try to connect to MySQL
echo "Connecting to database... ";
try {
    $pdo = new PDO(
        "mysql:host={$db_host};port={$db_port};dbname={$db_name}", 
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

// Check if the audit_logs table exists
echo "Checking audit_logs table... ";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'audit_logs'");
    if ($stmt->rowCount() == 0) {
        echo "Table doesn't exist. No need to modify.\n";
        exit(0);
    }
    echo "Found\n";
} catch (PDOException $e) {
    echo "Failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Check if the foreign key exists
echo "Checking foreign key constraints... ";
try {
    $stmt = $pdo->query("
        SELECT * FROM information_schema.TABLE_CONSTRAINTS 
        WHERE CONSTRAINT_NAME = 'audit_logs_user_id_foreign' 
        AND TABLE_NAME = 'audit_logs' 
        AND TABLE_SCHEMA = '{$db_name}'
    ");
    
    if ($stmt->rowCount() > 0) {
        echo "Found foreign key constraint.\n";
        
        // Drop the foreign key constraint
        echo "Dropping foreign key constraint... ";
        $pdo->exec("ALTER TABLE audit_logs DROP FOREIGN KEY audit_logs_user_id_foreign");
        echo "Done\n";
    } else {
        echo "No foreign key constraint found.\n";
    }
} catch (PDOException $e) {
    echo "Failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    // Continue anyway
}

// Make user_id column nullable
echo "Modifying user_id column to allow NULL values... ";
try {
    $pdo->exec("ALTER TABLE audit_logs MODIFY user_id VARCHAR(36) NULL");
    echo "Done\n";
} catch (PDOException $e) {
    echo "Failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    // Continue anyway
}

// Add back the foreign key with SET NULL
echo "Adding new foreign key constraint with SET NULL action... ";
try {
    $pdo->exec("ALTER TABLE audit_logs ADD CONSTRAINT audit_logs_user_id_foreign FOREIGN KEY (user_id) REFERENCES karyawan(karyawan_id) ON DELETE SET NULL ON UPDATE CASCADE");
    echo "Done\n";
} catch (PDOException $e) {
    echo "Failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    // Continue anyway
}

// Check if any records in audit_logs have invalid user_id
echo "Checking for records with invalid user_id... ";
try {
    $stmt = $pdo->query("
        SELECT a.* FROM audit_logs a 
        LEFT JOIN karyawan k ON a.user_id = k.karyawan_id 
        WHERE a.user_id IS NOT NULL AND k.karyawan_id IS NULL
    ");
    
    $invalidCount = $stmt->rowCount();
    if ($invalidCount > 0) {
        echo "Found {$invalidCount} records with invalid user_id.\n";
        
        echo "Setting invalid user_id values to NULL... ";
        $pdo->exec("
            UPDATE audit_logs a 
            LEFT JOIN karyawan k ON a.user_id = k.karyawan_id 
            SET a.user_id = NULL 
            WHERE a.user_id IS NOT NULL AND k.karyawan_id IS NULL
        ");
        echo "Done\n";
    } else {
        echo "No records with invalid user_id found.\n";
    }
} catch (PDOException $e) {
    echo "Failed\n";
    echo "Error: " . $e->getMessage() . "\n";
    // Continue anyway
}

echo "\nPerbaikan tabel audit_logs selesai.\n";
echo "Sekarang proses seeding seharusnya bisa berjalan tanpa error.\n";
echo "\n====================================================================\n";
