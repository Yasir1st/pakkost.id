<?php
// File: /klp1/auth/proses_reset_password.php (Versi Baru)
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Keamanan: Cek lagi apakah pengguna diizinkan untuk reset
    if (!isset($_SESSION['reset_allowed']) || $_SESSION['reset_allowed'] !== true || !isset($_SESSION['reset_user_id'])) {
        die("Akses tidak sah atau sesi telah berakhir.");
    }
    
    $user_id = $_SESSION['reset_user_id'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Validasi dasar
    if (empty($password) || empty($password_confirm)) {
        die("Semua field harus diisi.");
    }
    if ($password !== $password_confirm) {
        // Redirect kembali jika password tidak cocok
        header("Location: /klp1/reset_password.php?error=mismatch");
        exit();
    }

    // Hash password baru
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Update password di tabel users berdasarkan ID dari session
    $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt_update->bind_param("si", $hashed_password, $user_id);
    $stmt_update->execute();

    // Hancurkan session reset agar tidak bisa dipakai lagi
    unset($_SESSION['reset_allowed']);
    unset($_SESSION['reset_user_id']);

    // Arahkan ke halaman login dengan pesan sukses
    header("Location: /klp1/login.php?status=reset_success");
    exit();
}
?>