<?php 
require_once '../config/database.php';
require_once 'auth_penyewa_check.php';
include '../includes/header.php';
?>

<h3>Dashboard Penyewa</h3>
<p>Selamat datang, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>! Berikut adalah riwayat sewa Anda.</p>

<?php if (isset($_GET['status']) && $_GET['status'] == 'ulasan_sukses'): ?>
    <div class="alert alert-success">Terima kasih, ulasan Anda berhasil dikirim!</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Riwayat dan Status Sewa
    </div>
    <div class="card-body">
        <?php
        // Query untuk mengambil data sewa milik penyewa yang login, di-join dengan data kost
        $stmt = $conn->prepare(
            "SELECT r.id as rental_id, r.tanggal_mulai, r.tanggal_selesai, r.status, 
                    k.id as kost_id, k.nama_kost, k.alamat,
                    (SELECT COUNT(*) FROM reviews WHERE kost_id = k.id AND user_id = ?) as ulasan_exists
             FROM rentals r
             JOIN kosts k ON r.kost_id = k.id
             WHERE r.user_id = ?
             ORDER BY r.tanggal_mulai DESC"
        );
        $stmt->bind_param("ii", $penyewa_id, $penyewa_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
            while($sewa = $result->fetch_assoc()):
        ?>
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title"><?php echo htmlspecialchars($sewa['nama_kost']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($sewa['alamat']); ?></p>
                    </div>
                    <div>
                        <?php
                        $status_class = 'bg-secondary';
                        if ($sewa['status'] == 'aktif') $status_class = 'bg-success';
                        if ($sewa['status'] == 'selesai') $status_class = 'bg-primary';
                        if ($sewa['status'] == 'dibatalkan') $status_class = 'bg-danger';
                        ?>
                        <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($sewa['status']); ?></span>
                    </div>
                </div>
                <hr>
                <p>
                    <strong>Tanggal Mulai:</strong> <?php echo date('d M Y', strtotime($sewa['tanggal_mulai'])); ?><br>
                    <strong>Tanggal Selesai:</strong> <?php echo $sewa['tanggal_selesai'] ? date('d M Y', strtotime($sewa['tanggal_selesai'])) : '-'; ?>
                </p>

                <?php 
                // Tampilkan tombol "Beri Ulasan" HANYA jika status sewa 'selesai' DAN belum pernah memberi ulasan
                if ($sewa['status'] == 'selesai' && $sewa['ulasan_exists'] == 0): 
                ?>
                    <a href="beri_ulasan.php?rental_id=<?php echo $sewa['rental_id']; ?>" class="btn btn-info btn-sm">Beri Ulasan & Rating</a>
                <?php elseif ($sewa['status'] == 'selesai' && $sewa['ulasan_exists'] > 0): ?>
                    <p class="text-success fst-italic"><small>✓ Anda sudah memberikan ulasan untuk kost ini.</small></p>
                <?php endif; ?>
            </div>
        </div>
        <?php 
            endwhile;
        else:
        ?>
        <div class="alert alert-info">Anda belum memiliki riwayat sewa.</div>
        <?php
        endif;
        $stmt->close();
        ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>