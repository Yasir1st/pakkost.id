<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kost_id = $_POST['kost_id'];
    $nama = $_POST['nama_pengirim'];
    $email = $_POST['email_pengirim'];
    $pertanyaan = $_POST['pertanyaan'];

    if (empty($kost_id) || empty($nama) || empty($email) || empty($pertanyaan)) {
        die("Semua field wajib diisi.");
    }

    $stmt = $conn->prepare("INSERT INTO inquiries (kost_id, nama_pengirim, email_pengirim, pertanyaan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $kost_id, $nama, $email, $pertanyaan);

    if ($stmt->execute()) {
        header("Location: /klp1/detail_kost.php?id=" . $kost_id . "&status=tanya_sukses");
    } else {
        header("Location: /klp1/detail_kost.php?id=" . $kost_id . "&status=tanya_gagal");
    }
    $stmt->close();
    $conn->close();
}
?>