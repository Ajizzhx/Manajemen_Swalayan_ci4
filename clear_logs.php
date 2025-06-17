<?php
/**
 * File untuk membersihkan log files di aplikasi Swalayan CI4
 * Script ini dapat dijalankan secara manual atau melalui cron job/task scheduler
 */

// Direktori log
$logDir = __DIR__ . '/writable/logs/';
$logFiles = glob($logDir . '*.log');

// Jika ada file log
if ($logFiles) {
    echo "Cleaning log files...\n";
    
    // Parameter untuk penyimpanan: jumlah file log terakhir yang dipertahankan
    $keepLatestFiles = 5;
    
    // Urutkan berdasarkan waktu modifikasi (terbaru terakhir)
    usort($logFiles, function($a, $b) {
        return filemtime($a) - filemtime($b);
    });
    
    // Hapus file log lama, pertahankan yang terbaru
    if (count($logFiles) > $keepLatestFiles) {
        $filesToDelete = array_slice($logFiles, 0, count($logFiles) - $keepLatestFiles);
        
        foreach ($filesToDelete as $fileToDelete) {
            if (unlink($fileToDelete)) {
                echo "Deleted: " . basename($fileToDelete) . "\n";
            } else {
                echo "Failed to delete: " . basename($fileToDelete) . "\n";
            }
        }
    }
    
    // Untuk file yang tersisa, bersihkan isinya jika melebihi ukuran tertentu (1MB)
    $remainingFiles = glob($logDir . '*.log');
    foreach ($remainingFiles as $file) {
        $maxSize = 1024 * 1024; // 1MB
        
        if (filesize($file) > $maxSize) {
            // Cara aman untuk mengosongkan file tanpa menghapusnya
            $handle = fopen($file, 'w');
            fwrite($handle, "<?php defined('SYSTEMPATH') || exit('No direct script access allowed'); ?>\n\n");
            fclose($handle);
            echo "Cleared contents of large file: " . basename($file) . "\n";
        }
    }
    
    echo "Log cleaning completed.\n";
} else {
    echo "No log files found.\n";
}
