# Script PowerShell untuk membersihkan log files
# Jalankan dengan hak admin: powershell.exe -ExecutionPolicy Bypass -File clean_logs.ps1

# Lokasi direktori log
$logDir = ".\writable\logs\"
$logFiles = Get-ChildItem -Path $logDir -Filter "*.log"

Write-Host "Membersihkan file log..."

if ($logFiles.Count -gt 0) {
    # Hapus semua log file kecuali file log hari ini
    $today = Get-Date -Format "yyyy-MM-dd"
    $todayLogFile = "log-$today.log"
    
    foreach ($file in $logFiles) {
        if ($file.Name -ne $todayLogFile) {
            Remove-Item $file.FullName -Force
            Write-Host "Menghapus file: $($file.Name)"
        }
        else {
            # Untuk log hari ini, kosongkan isinya dengan menyimpan header CI4
            $content = "<?php defined('SYSTEMPATH') || exit('No direct script access allowed'); ?>`n`n"
            Set-Content -Path $file.FullName -Value $content
            Write-Host "Mengosongkan file log hari ini: $($file.Name)"
        }
    }
    
    Write-Host "Pembersihan log selesai."
}
else {
    Write-Host "Tidak ada file log ditemukan."
}

# Beri tahu pengguna bahwa proses selesai
Write-Host "Proses pembersihan log selesai. Tekan Enter untuk keluar..."
Read-Host
