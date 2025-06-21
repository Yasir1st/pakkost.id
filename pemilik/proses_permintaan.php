<?php
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';

if (!isset($_GET['id']) || !isset($_GET['aksi'])) {
    die("Akses tidak valid.");
}

$rental_id = (int)$_GET['id'];
$aksi = $_GET['aksi'];
$new_status = '';

if ($aksi == 'setujui') {
    $new_status = 'aktif';
} elseif ($aksi == 'tolak') {
    $new_status = 'dibatalkan';
} else {
    die("Aksi tidak dikenal.");
}

// Keamanan: Pastikan rental yang diubah statusnya adalah milik properti si pemilik
$stmt_check = $conn->prepare(
    "SELECT r.id FROM rentals r JOIN kosts k ON r.kost_id = k.id WHERE r.id = ? AND k.user_id = ?"
);
$stmt_check->bind_param("ii", $rental_id, $pemilik_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows === 0) {
    die("Akses ditolak. Anda tidak berhak mengubah status sewa ini.");
}
$stmt_check->close();

// --- BLOK KODE BARU UNTUK PENGECEKAN KETERSEDIAAN ---
if ($aksi == 'setujui') {
    // 1. Ambil kost_id dari rental yang akan disetujui
    $stmt_get_kost_id = $conn->prepare("SELECT kost_id FROM rentals WHERE id = ?");
    $stmt_get_kost_id->bind_param("i", $rental_id);
    $stmt_get_kost_id->execute();
    $kost_id_result = $stmt_get_kost_id->get_result()->fetch_assoc();
    $kost_id = $kost_id_result['kost_id'];
    $stmt_get_kost_id->close();

    // 2. Hitung ketersediaan saat ini
    $stmt_calc = $conn->prepare(
        "SELECT (jumlah_total_kamar - (SELECT COUNT(*) FROM rentals WHERE kost_id = ? AND status = 'aktif')) AS kamar_tersedia 
         FROM kosts WHERE id = ?"
    );
    $stmt_calc->bind_param("ii", $kost_id, $kost_id);
    $stmt_calc->execute();
    $ketersediaan_result = $stmt_calc->get_result()->fetch_assoc();
    $kamar_tersedia = $ketersediaan_result['kamar_tersedia'];
    $stmt_calc->close();

    // 3. Jika kamar sudah habis, hentikan proses dan beri pesan error
    if ($kamar_tersedia <= 0) {
        die(" Gagal Menyetujui: Kamar di properti ini sudah penuh. Tidak ada kamar yang tersedia.");
    }
}
// --- AKHIR BLOK KODE BARU ---

// Jika valid, update statusnya
$stmt_update = $conn->prepare("UPDATE rentals SET status = ? WHERE id = ?");
$stmt_update->bind_param("si", $new_status, $rental_id);

if ($stmt_update->execute()) {
    header("Location: permintaan_sewa.php?status=sukses");
} else {
    die("Gagal memperbarui status: " . $stmt_update->error);
}
$stmt_update->close();
$conn->close();
?>