<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];

    // Validasi dasar
    if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($role)) {
        die("Semua field wajib diisi!");
    }

    // Hash password dengan aman
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Gunakan prepared statements untuk keamanan
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, phone_number, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $username, $email, $hashed_password, $phone_number, $role);

    if ($stmt->execute()) {
        // Registrasi berhasil, arahkan ke halaman login
        header("Location: /klp1/login.php?status=reg_success");
        exit();
    } else {
        // Cek jika username atau email sudah ada
        if ($conn->errno == 1062) {
             die("Registrasi gagal: Username atau Email sudah terdaftar.");
        } else {
             die("Registrasi gagal: " . $stmt->error);
        }
    }

    $stmt->close();
    $conn->close();
}
?>