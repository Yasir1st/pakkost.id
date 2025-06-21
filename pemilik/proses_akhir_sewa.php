<?php
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';

// 1. Validasi Input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Akses tidak valid. ID sewa tidak ditemukan.");
}
$rental_id = (int)$_GET['id'];

// 2. Keamanan: Pastikan rental yang akan diubah adalah milik properti si pemilik
$stmt_check = $conn->prepare(
    "SELECT r.id 
     FROM rentals r 
     JOIN kosts k ON r.kost_id = k.id 
     WHERE r.id = ? AND k.user_id = ?"
);
$stmt_check->bind_param("ii", $rental_id, $pemilik_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    // Jika tidak ada hasil, berarti pemilik mencoba mengakses data yang bukan miliknya
    die("Akses ditolak. Anda tidak berhak mengubah status sewa ini.");
}
$stmt_check->close();

// 3. Jika valid, update status dan tanggal selesai
// Menggunakan NOW() untuk mengisi tanggal_selesai dengan tanggal dan waktu saat ini
$stmt_update = $conn->prepare("UPDATE rentals SET status = 'selesai', tanggal_selesai = NOW() WHERE id = ?");
$stmt_update->bind_param("i", $rental_id);

if ($stmt_update->execute()) {
    // Jika berhasil, arahkan kembali ke halaman daftar penyewa aktif dengan pesan sukses
    header("Location: penyewa_aktif.php?status=akhir_sukses");
    exit();
} else {
    // Jika gagal, tampilkan pesan error
    die("Gagal memperbarui status sewa: " . $stmt_update->error);
}

$stmt_update->close();
$conn->close();
?>