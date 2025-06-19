<?php

/**
 * Swalayan CI4 - Show Owner Credentials
 * This script displays the actual owner email used during setup
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'swalayan_db';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get owner email from the database
$sql = "SELECT email FROM karyawan WHERE role = 'pemilik' OR role = 'owner' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ownerEmail = $row['email'];
    
    echo "\n";
    echo "===========================================\n";
    echo "INFORMASI KREDENSIAL LOGIN PEMILIK (OWNER)\n";
    echo "===========================================\n";
    echo "Email: " . $ownerEmail . "\n";
    echo "Password: owner123\n";
    echo "===========================================\n";
    echo "PENTING: Email ini digunakan untuk menerima kode OTP saat login sebagai owner.\n";
    echo "         Mohon pastikan email yang digunakan adalah email aktif.\n";
} else {
    echo "Tidak dapat menemukan informasi akun pemilik.\n";
}

// Close connection
$conn->close();
?>
