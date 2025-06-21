<?php
require_once '../config/database.php';
require_once 'auth_admin_check.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("ID User tidak valid."); }

$user_id = $_GET['id'];

// Keamanan: Admin tidak bisa menghapus dirinya sendiri
if ($user_id == $_SESSION['user_id']) { die("Anda tidak bisa menghapus akun Anda sendiri."); }

// Hapus user dari database. Semua data terkait (kost, rental, ulasan) akan ikut terhapus karena ON DELETE CASCADE
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    header("Location: manage_users.php?status=hapus_sukses");
} else {
    die("Gagal menghapus user: " . $stmt->error);
}
?>