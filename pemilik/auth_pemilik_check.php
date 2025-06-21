<?php
// Mulai session jika belum ada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah pengguna sudah login dan rolenya 'pemilik'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
    // Jika tidak, redirect ke halaman login
    header('Location: /klp1/login.php?error=Akses ditolak');
    exit();
}

// Ambil ID pemilik dari session untuk digunakan di halaman lain
$pemilik_id = $_SESSION['user_id'];
?>