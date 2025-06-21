<?php 
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';

// Validasi ID Kost dari URL
if (!isset($_GET['kost_id']) || !is_numeric($_GET['kost_id'])) {
    die("ID Kost tidak valid.");
}
$kost_id = (int)$_GET['kost_id'];

// Cek apakah kost ini benar milik si pemilik
$stmt_check = $conn->prepare("SELECT nama_kost FROM kosts WHERE id = ? AND user_id = ?");
$stmt_check->bind_param("ii", $kost_id, $pemilik_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) {
    die("Akses ditolak. Anda bukan pemilik kost ini.");
}
$kost = $result_check->fetch_assoc();

include '../includes/header.php';
?>

<h3>Kotak Masuk Notifikasi</h3>
<h5>Untuk Properti: <b><?php echo htmlspecialchars($kost['nama_kost']); ?></b></h5>
<a href="index.php" class="btn btn-sm btn-secondary mb-3">Kembali ke Dashboard</a>

<div class="accordion" id="accordionInquiries">
    <?php
    $stmt = $conn->prepare("SELECT * FROM inquiries WHERE kost_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $kost_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0):
        while($inquiry = $result->fetch_assoc()):
    ?>
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?php echo $inquiry['id']; ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $inquiry['id']; ?>">
                <b><?php echo htmlspecialchars($inquiry['nama_pengirim']); ?></b>&nbsp; (<?php echo htmlspecialchars($inquiry['email_pengirim']); ?>) - 
                <span class="ms-2 fst-italic text-muted"><?php echo date('d M Y, H:i', strtotime($inquiry['created_at'])); ?></span>
            </button>
        </h2>
        <div id="collapse<?php echo $inquiry['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionInquiries">
            <div class="accordion-body">
                <p><?php echo nl2br(htmlspecialchars($inquiry['pertanyaan'])); ?></p>
                <hr>
                <button class="btn btn-sm btn-primary">Balas (Fitur akan datang)</button>
            </div>
        </div>
    </div>
    <?php 
        endwhile;
    else:
    ?>
    <div class="alert alert-info">Belum ada pertanyaan atau notifikasi untuk kost ini.</div>
    <?php
    endif;
    $stmt->close();
    ?>
</div>


<?php include '../includes/footer.php'; ?>