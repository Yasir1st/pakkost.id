<?php
require_once '../config/database.php';
require_once 'auth_admin_check.php';

if (!isset($_GET['kost_id']) || !is_numeric($_GET['kost_id'])) { die("ID Kost tidak valid."); }
$kost_id = $_GET['kost_id'];
$kost = $conn->query("SELECT nama_kost FROM kosts WHERE id = $kost_id")->fetch_assoc();

include '../includes/header.php';
?>

<h3>Daftar Penghuni untuk "<?php echo htmlspecialchars($kost['nama_kost']); ?>"</h3>
<a href="manage_kost.php" class="btn btn-secondary btn-sm mb-3">Kembali ke Kelola Kost</a>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Penghuni</th>
                        <th>No. Telepon</th>
                        <th>Tgl Mulai Sewa</th>
                        <th>Tgl Selesai Sewa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT u.full_name, u.phone_number, r.tanggal_mulai, r.tanggal_selesai, r.status FROM rentals r JOIN users u ON r.user_id = u.id WHERE r.kost_id = ? ORDER BY r.status, r.tanggal_mulai DESC");
                    $stmt->bind_param("i", $kost_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0):
                        while($tenant = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tenant['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['phone_number']); ?></td>
                        <td><?php echo date('d M Y', strtotime($tenant['tanggal_mulai'])); ?></td>
                        <td><?php echo $tenant['tanggal_selesai'] ? date('d M Y', strtotime($tenant['tanggal_selesai'])) : '-'; ?></td>
                        <td><?php echo ucfirst($tenant['status']); ?></td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada riwayat penyewa untuk kost ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>