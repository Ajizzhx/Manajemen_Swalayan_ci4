<?php

/**
 * Swalayan CI4 Owner Email Update Script
 * This script updates the owner's email address in the database for OTP delivery
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

echo "Connected to database successfully!\n\n";

// Find the owner account
$sql = "SELECT karyawan_id, email FROM karyawan WHERE role = 'pemilik' OR role = 'owner' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $owner = $result->fetch_assoc();
    echo "Owner account found. Current email: " . $owner['email'] . "\n\n";

    // Prompt for new email
    echo "Enter new email address for Owner OTP delivery: ";
    
    if (PHP_SAPI === 'cli') {
        // If run from command line
        $newEmail = trim(fgets(STDIN));
    } else {
        // If run from browser, show an input form
        echo '<form method="POST">';
        echo '<input type="email" name="new_email" placeholder="Enter valid email address" required>';
        echo '<input type="submit" value="Update Email">';
        echo '</form>';
        
        if (isset($_POST['new_email'])) {
            $newEmail = $_POST['new_email'];
        } else {
            exit();
        }
    }
    
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }
    
    // Update the owner's email
    $updateSql = "UPDATE karyawan SET email = ? WHERE karyawan_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ss", $newEmail, $owner['karyawan_id']);
    
    if ($stmt->execute()) {
        echo "Owner's email updated successfully to: " . $newEmail . "\n";
        echo "This email will now be used for receiving OTP codes when logging in as owner.\n";
    } else {
        echo "Error updating email: " . $stmt->error . "\n";
    }
    
    $stmt->close();
} else {
    echo "Error: No owner account found in the database.\n";
}

// Close connection
$conn->close();

?>
