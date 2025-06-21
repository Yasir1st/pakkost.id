<?php
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';
include '../includes/header.php';
?>
<?php if (isset($_GET['status']) && $_GET['status'] == 'akhir_sukses'): ?>
    <div class="alert alert-success">Sewa telah berhasil diakhiri. Ketersediaan kamar telah diperbarui.</div>
<?php endif; ?>

<h3>Daftar Penyewa Aktif</h3>
<p>Berikut adalah daftar penyewa yang saat ini sedang menyewa properti Anda (status "Aktif").</p>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Penyewa</th>
                        <th>No. Telepon</th>
                        <th>Properti Kost</th>
                        <th>Tanggal Mulai Sewa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil penyewa aktif di semua properti milik pemilik
                    $stmt = $conn->prepare(
                        "SELECT r.id as rental_id, u.full_name, u.phone_number, k.nama_kost, r.tanggal_mulai
                         FROM rentals r
                         JOIN users u ON r.user_id = u.id
                         JOIN kosts k ON r.kost_id = k.id
                         WHERE k.user_id = ? AND r.status = 'aktif'
                         ORDER BY k.nama_kost, r.tanggal_mulai"
                    );
                    $stmt->bind_param("i", $pemilik_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0):
                        while($penyewa = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($penyewa['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($penyewa['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($penyewa['nama_kost']); ?></td>
                        <td><?php echo date('d M Y', strtotime($penyewa['tanggal_mulai'])); ?></td>
                        <td>
                            <a href="proses_akhir_sewa.php?id=<?php echo $penyewa['rental_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin mengakhiri sewa untuk penyewa ini? Stok kamar akan otomatis bertambah.')">
                                Akhiri Sewa
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada penyewa yang sedang aktif saat ini.</td>
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