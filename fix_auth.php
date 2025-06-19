<?php
/**
 * Auth Controller Fix for Login Issue
 * This script patches the Auth controller to prevent audit log errors from blocking login
 */

// First, check if we can access the file
$authFilePath = __DIR__ . '/app/Controllers/Auth.php';
if (!file_exists($authFilePath)) {
    echo "Error: Auth.php file not found at $authFilePath\n";
    exit(1);
}

// Read the Auth.php file
$authContent = file_get_contents($authFilePath);

// Create backup
$backupPath = $authFilePath . '.backup.' . date('YmdHis');
file_put_contents($backupPath, $authContent);
echo "Created backup of Auth.php at $backupPath\n";

// Check for references to AuditLogModel
$hasAuditLogInserts = preg_match('/\$auditLogModel->insert\(/', $authContent);

if ($hasAuditLogInserts) {
    echo "Found direct AuditLogModel->insert() calls in Auth.php\n";
    // Replace direct inserts with our safer method
    $authContent = preg_replace(
        '/\$auditLogModel->insert\(\[\s*\'user_id\'\s*=>\s*(.*?),\s*\'action\'\s*=>\s*(.*?),\s*\'description\'\s*=>\s*(.*?),/s',
        '$auditLogModel->logActivity($1, $2, $3);',
        $authContent
    );
    
    // Also fix inserts without user_id
    $authContent = preg_replace(
        '/\$auditLogModel->insert\(\[\s*\'action\'\s*=>\s*(.*?),\s*\'description\'\s*=>\s*(.*?),/s',
        '$auditLogModel->logActivity(null, $1, $2);',
        $authContent
    );
    
    echo "Updated AuditLogModel calls to use the safer logActivity method\n";
    
    // Write the updated file
    file_put_contents($authFilePath, $authContent);
    echo "Auth.php has been updated successfully\n";
} else {
    echo "No direct AuditLogModel->insert() calls found in Auth.php\n";
}

// Now let's try to re-run the database setup to ensure tables are correct
echo "\nRunning database setup script to fix any table issues...\n";
include 'fix_database.php';
?>
