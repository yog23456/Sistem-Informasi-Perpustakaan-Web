<?php
/**
 * Fungsi untuk mengambil data laporan dengan filter dinamis
 * Disesuaikan dengan struktur database Perpustakaan Yogi
 */

function getDataLaporan($tabel, $params = []) {
    global $connection;
    
    $where = [];
    $query = "SELECT * FROM `$tabel`";

    // Filter berdasarkan ID Spesifik
    if (!empty($params['id'])) {
        $id = mysqli_real_escape_string($connection, $params['id']);
        $where[] = "`id` = '$id'";
    }

    // Tentukan nama kolom tanggal berdasarkan tabel
    // Di database Yogi: tabel peminjaman pakai 'tanggal_pinjam', pengembalian pakai 'tanggal_kembali_aktual'
    $kolom_tgl = ($tabel == 'peminjaman') ? 'tanggal_pinjam' : 'tanggal_kembali_aktual';

    // Filter berdasarkan Rentang Tanggal (Kalender)
    if (!empty($params['tgl_mulai']) && !empty($params['tgl_selesai'])) {
        $mulai = mysqli_real_escape_string($connection, $params['tgl_mulai']);
        $selesai = mysqli_real_escape_string($connection, $params['tgl_selesai']);
        $where[] = "`$kolom_tgl` BETWEEN '$mulai' AND '$selesai'";
    }

    // Filter berdasarkan Bulan
    if (!empty($params['bulan'])) {
        $bulan = mysqli_real_escape_string($connection, $params['bulan']);
        $where[] = "MONTH(`$kolom_tgl`) = '$bulan'";
    }

    // Filter berdasarkan Tahun
    if (!empty($params['tahun'])) {
        $tahun = mysqli_real_escape_string($connection, $params['tahun']);
        $where[] = "YEAR(`$kolom_tgl`) = '$tahun'";
    }

    if (count($where) > 0) {
        $query .= " WHERE " . implode(" AND ", $where);
    }

    $query .= " ORDER BY `id` DESC";
    return mysqli_query($connection, $query);
}


function getDataPengembalian($params = []) {
    return getDataLaporan('pengembalian', $params);
}
?>