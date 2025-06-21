<?php
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';

// 1. Validasi Input ID dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Akses tidak valid. ID kost tidak ditemukan.");
}
$kost_id = (int)$_GET['id'];

// 2. Keamanan: Pastikan kost yang akan dihapus adalah milik pemilik yang sedang login
$stmt_check = $conn->prepare("SELECT id FROM kosts WHERE id = ? AND user_id = ?");
$stmt_check->bind_param("ii", $kost_id, $pemilik_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) {
    die("Akses ditolak. Anda tidak berhak menghapus properti ini.");
}
$stmt_check->close();

// 3. Hapus File Gambar Fisik dari Server (Langkah Penting!)
// Ini harus dilakukan SEBELUM menghapus data dari database, agar kita tidak kehilangan nama filenya.
$stmt_get_images = $conn->prepare("SELECT image_path FROM kost_images WHERE kost_id = ?");
$stmt_get_images->bind_param("i", $kost_id);
$stmt_get_images->execute();
$images_result = $stmt_get_images->get_result();

while ($image = $images_result->fetch_assoc()) {
    $file_path = '../assets/uploads/' . $image['image_path'];
    if (file_exists($file_path)) {
        unlink($file_path); // Hapus file dari folder uploads
    }
}
$stmt_get_images->close();


// 4. Hapus Data dari Database
// Karena kita sudah mengatur ON DELETE CASCADE, kita hanya perlu menghapus data induk di tabel 'kosts'.
// Semua data anak (gambar, fasilitas, review, rental) akan terhapus otomatis.
$stmt_delete = $conn->prepare("DELETE FROM kosts WHERE id = ?");
$stmt_delete->bind_param("i", $kost_id);

if ($stmt_delete->execute()) {
    // Jika berhasil, arahkan kembali ke dashboard dengan pesan sukses
    header("Location: index.php?status=hapus_sukses");
    exit();
} else {
    // Jika gagal, tampilkan pesan error
    die("Gagal menghapus properti kost: " . $stmt_delete->error);
}

$stmt_delete->close();
$conn->close();
?>