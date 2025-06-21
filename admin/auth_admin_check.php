<?php
// Memulai session jika belum ada, penting untuk autentikasi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah pengguna sudah login dan rolenya adalah 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Jika tidak, tendang ke halaman login dengan pesan error
    header('Location: /klp1/login.php?error=Akses ditolak. Area khusus Admin.');
    exit();
}
?>