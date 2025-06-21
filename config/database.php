<?php
mysqli_report(MYSQLI_REPORT_ERROR);
// Pengaturan koneksi database
$host = 'localhost';
$db_user = 'root';
$db_pass = ''; 
$db_name = 'db_majene_kost';

// Membuat koneksi
$conn = new mysqli($host, $db_user, $db_pass, $db_name, 3307);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Memulai session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>