<?php



if (!function_exists('format_indo')) {
    
    function format_indo($tanggal_sql, $dengan_waktu = false, $dengan_hari = false) {
        if (empty($tanggal_sql) || $tanggal_sql === '0000-00-00' || $tanggal_sql === '0000-00-00 00:00:00') {
            return ''; 
        }

        $timestamp = strtotime($tanggal_sql);
        if ($timestamp === false) {
            return $tanggal_sql; 
        }

        $hari_indo = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulan_indo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $nama_hari = $dengan_hari ? $hari_indo[date('w', $timestamp)] . ', ' : '';
        $tanggal = date('d', $timestamp);
        $bulan = $bulan_indo[(int)date('n', $timestamp)];
        $tahun = date('Y', $timestamp);
        $waktu = $dengan_waktu ? ', ' . date('H:i', $timestamp) : '';

        return $nama_hari . $tanggal . ' ' . $bulan . ' ' . $tahun . $waktu;
    }
}