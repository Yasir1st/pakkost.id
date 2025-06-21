<?php
// File: /klp1/auth/proses_verifikasi_user.php (File Baru)
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Cek apakah ada user yang cocok dengan ketiga data tersebut
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ? AND phone_number = ?");
    $stmt->bind_param("sss", $username, $email, $phone_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Jika data cocok, simpan ID user ke session dan beri izin reset
        $user = $result->fetch_assoc();
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_allowed'] = true; // Penanda bahwa verifikasi berhasil

        // Arahkan ke halaman untuk membuat password baru
        header("Location: /klp1/reset_password.php");
        exit();
    } else {
        // Jika data tidak cocok, kembalikan ke halaman verifikasi dengan pesan error
        header("Location: /klp1/forgot_password.php?error=data_tidak_cocok");
        exit();
    }
}
?>