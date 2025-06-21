<?php
require_once '../config/database.php';
require_once 'auth_penyewa_check.php'; // Memastikan hanya penyewa

// --- LOGIKA PEMROSESAN FORM ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kost_id = $_POST['kost_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];

    if (empty($kost_id) || empty($tanggal_mulai)) {
        $error_message = "Harap pilih tanggal mulai sewa.";
    } else {
        // Masukkan data ke tabel rentals dengan status 'menunggu_konfirmasi'
        $stmt = $conn->prepare("INSERT INTO rentals (kost_id, user_id, tanggal_mulai, status) VALUES (?, ?, ?, 'menunggu_konfirmasi')");
        $stmt->bind_param("iis", $kost_id, $penyewa_id, $tanggal_mulai);
        
        if ($stmt->execute()) {
            header("Location: /klp1/penyewa/dashboard.php?status=ajukan_sukses");
            exit();
        } else {
            $error_message = "Gagal mengajukan sewa: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- Mengambil data untuk ditampilkan di halaman ---
if (!isset($_GET['kost_id']) || !is_numeric($_GET['kost_id'])) {
    die("ID Kost tidak valid.");
}
$kost_id = (int)$_GET['kost_id'];
$kost_result = $conn->query("SELECT nama_kost FROM kosts WHERE id = $kost_id");
if ($kost_result->num_rows === 0) {
    die("Kost tidak ditemukan.");
}
$kost = $kost_result->fetch_assoc();

include '../includes/header.php';
?>

<h3>Ajukan Sewa untuk "<?php echo htmlspecialchars($kost['nama_kost']); ?>"</h3>
<p>Silakan pilih tanggal Anda akan mulai menyewa. Pemilik akan segera mengonfirmasi ketersediaan.</p>
<hr>

<div class="card">
    <div class="card-body">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="ajukan_sewa.php" method="POST">
            <input type="hidden" name="kost_id" value="<?php echo $kost_id; ?>">
            <div class="mb-3">
                <label for="tanggal_mulai" class="form-label"><strong>Pilih Tanggal Mulai Sewa:</strong></label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
            <a href="/klp1/detail_kost.php?id=<?php echo $kost_id; ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>