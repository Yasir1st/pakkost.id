<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pastikan user sudah login
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
        die("Akses ditolak. Anda harus login sebagai penyewa.");
    }
    
    $user_id = $_SESSION['user_id'];
    $kost_id = $_POST['kost_id'];
    $rating = $_POST['rating'];
    $ulasan = $_POST['ulasan'];

    // Validasi dasar
    if (empty($kost_id) || empty($rating) || $rating < 1 || $rating > 5) {
        die("Data tidak valid. Harap isi rating dengan benar.");
    }

    // Cek lagi di server untuk mencegah ulasan ganda
    $stmt_check = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND kost_id = ?");
    $stmt_check->bind_param("ii", $user_id, $kost_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        die("Anda sudah pernah memberikan ulasan untuk kost ini.");
    }
    $stmt_check->close();

    // Jika semua valid, simpan ulasan
    $stmt = $conn->prepare("INSERT INTO reviews (kost_id, user_id, rating, ulasan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $kost_id, $user_id, $rating, $ulasan);
    
    if ($stmt->execute()) {
        // Berhasil, redirect ke dashboard penyewa
        header("Location: /klp1/penyewa/dashboard.php?status=ulasan_sukses");
        exit();
    } else {
        die("Terjadi kesalahan saat menyimpan ulasan: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: /klp1/index.php");
    exit();
}
?>