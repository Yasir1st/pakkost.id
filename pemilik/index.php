<?php
// File: /pemilik/index.php (Versi Perbaikan)

// Panggil file koneksi dan pengecekan akses
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';

// Hitung jumlah permintaan sewa yang menunggu konfirmasi untuk notifikasi
$stmt_count = $conn->prepare(
    "SELECT COUNT(r.id) as total 
     FROM rentals r JOIN kosts k ON r.kost_id = k.id 
     WHERE k.user_id = ? AND r.status = 'menunggu_konfirmasi'"
);
$stmt_count->bind_param("i", $pemilik_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result()->fetch_assoc();
$permintaan_baru = $count_result['total'];
$stmt_count->close();

// Include header standar
include '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h3>Dashboard Pemilik Kost</h3>
    <div class="d-flex gap-2">
        <a href="penyewa_aktif.php" class="btn btn-secondary">Penyewa Aktif</a>
        <a href="permintaan_sewa.php" class="btn btn-info position-relative">
            Permintaan Sewa
            <?php if ($permintaan_baru > 0) : ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo $permintaan_baru; ?>
                    <span class="visually-hidden">permintaan baru</span>
                </span>
            <?php endif; ?>
        </a>
        <a href="tambah_kost.php" class="btn btn-primary">Tambah Kost</a>
    </div>
</div>

<p>Selamat datang, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>! Di bawah ini adalah daftar kost yang Anda kelola.</p>

<?php
// Menampilkan notifikasi sukses jika ada
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'hapus_sukses') {
        echo '<div class="alert alert-success">Properti kost telah berhasil dihapus secara permanen.</div>';
    } elseif ($_GET['status'] == 'edit_sukses') {
        echo '<div class="alert alert-success">Perubahan berhasil disimpan.</div>';
    } elseif ($_GET['status'] == 'tambah_sukses') {
        echo '<div class="alert alert-success">Properti kost baru berhasil ditambahkan.</div>';
    }
}
?>

<div class="card">
    <div class="card-header">
        Daftar Properti Kost Anda
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nama Kost</th>
                        <th>Alamat</th>
                        <th>Tipe</th>
                        <th>Harga/Bulan</th>
                        <th>Kamar Tersedia</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil semua kost DAN menghitung ketersediaan kamar otomatis
                    $stmt = $conn->prepare(
                        "SELECT 
                            k.id, k.nama_kost, k.alamat, k.tipe_kost, k.harga_bulanan,
                            (k.jumlah_total_kamar - (SELECT COUNT(*) FROM rentals WHERE kost_id = k.id AND status = 'aktif')) AS kamar_tersedia
                         FROM kosts k
                         WHERE k.user_id = ? 
                         ORDER BY k.created_at DESC"
                    );
                    $stmt->bind_param("i", $pemilik_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) :
                        while ($kost = $result->fetch_assoc()) :
                    ?>
                            <tr>
                                <td><?php echo htmlspecialchars($kost['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($kost['alamat']); ?></td>
                                <td>
                                    <?php
                                    // Logika untuk menentukan warna badge tipe kost
                                    $tipe_kost = $kost['tipe_kost'];
                                    $badge_class = '';
                                    $text_class = 'text-white';

                                    if ($tipe_kost == 'putra') {
                                        $badge_class = 'bg-info';
                                        $text_class = 'text-dark';
                                    } elseif ($tipe_kost == 'putri') {
                                        $badge_class = 'bg-pink';
                                    } elseif ($tipe_kost == 'campur') {
                                        $badge_class = 'bg-success';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?> <?php echo $text_class; ?>"><?php echo ucfirst($tipe_kost); ?></span>
                                </td>
                                <td>Rp <?php echo number_format($kost['harga_bulanan']); ?></td>
                                <td><b><?php echo $kost['kamar_tersedia']; ?></b> kamar</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="edit_kost.php?id=<?php echo $kost['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="notifikasi.php?kost_id=<?php echo $kost['id']; ?>" class="btn btn-sm btn-info">Notif</a>
                                        <a href="proses_hapus_kost.php?id=<?php echo $kost['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('PERINGATAN: Menghapus kost ini akan menghilangkan SEMUA data terkait (penyewa, ulasan, foto, dll.) secara permanen. Anda yakin?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else :
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">Anda belum menambahkan properti kost.</td>
                        </tr>
                    <?php
                    endif;
                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>