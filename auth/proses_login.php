<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: /klp1/login.php?error=Harap isi semua field");
        exit();
    }

    // Cek user berdasarkan username atau email
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil, simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: /klp1/admin/index.php");
            } elseif ($user['role'] == 'pemilik') {
                header("Location: /klp1/pemilik/index.php");
            } else { // 'penyewa'
                header("Location: /klp1/index.php");
            }
            exit();

        } else {
            header("Location: /klp1/login.php?error=Password salah");
            exit();
        }
    } else {
        header("Location: /klp1/login.php?error=Username atau Email tidak ditemukan");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>