<?php
// Mulai session jika belum ada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah pengguna sudah login dan rolenya 'penyewa'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    // Jika tidak, redirect ke halaman login
    header('Location: /klp1/login.php?error=Akses ditolak. Harap login sebagai penyewa.');
    exit();
}

// Ambil ID penyewa dari session
$penyewa_id = $_SESSION['user_id'];
?>