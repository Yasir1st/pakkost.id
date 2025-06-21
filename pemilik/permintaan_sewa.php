<?php
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';
include '../includes/header.php';
?>

<h3>Permintaan Sewa Masuk</h3>
<p>Berikut adalah daftar calon penyewa yang mengajukan sewa untuk properti Anda.</p>

<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-success">Aksi berhasil dilakukan.</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>Nama Penyewa</th>
                        <th>Properti Kost</th>
                        <th>Tgl Mulai Sewa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil permintaan dengan status 'menunggu_konfirmasi'
                    $stmt = $conn->prepare(
                        "SELECT r.id as rental_id, r.created_at, r.tanggal_mulai, u.full_name, k.nama_kost
                         FROM rentals r
                         JOIN users u ON r.user_id = u.id
                         JOIN kosts k ON r.kost_id = k.id
                         WHERE k.user_id = ? AND r.status = 'menunggu_konfirmasi'
                         ORDER BY r.created_at DESC"
                    );
                    $stmt->bind_param("i", $pemilik_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0):
                        while($req = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($req['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($req['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($req['nama_kost']); ?></td>
                        <td><?php echo date('d M Y', strtotime($req['tanggal_mulai'])); ?></td>
                        <td>
                            <a href="proses_permintaan.php?id=<?php echo $req['rental_id']; ?>&aksi=setujui" class="btn btn-sm btn-success" onclick="return confirm('Anda yakin ingin menyetujui sewa ini?')">Setujui</a>
                            <a href="proses_permintaan.php?id=<?php echo $req['rental_id']; ?>&aksi=tolak" class="btn btn-sm btn-danger" onclick="return confirm('Anda yakin ingin menolak sewa ini?')">Tolak</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada permintaan sewa baru.</td>
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