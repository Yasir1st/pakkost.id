<?php
require_once '../config/database.php';
require_once 'auth_admin_check.php';
include '../includes/header.php';
?>

<h3>Kelola Semua Properti Kost</h3>
<a href="index.php" class="btn btn-secondary btn-sm mb-3">Kembali ke Dashboard</a>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kost</th>
                        <th>Pemilik</th>
                        <th>Alamat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT k.id, k.nama_kost, k.alamat, u.full_name as nama_pemilik FROM kosts k JOIN users u ON k.user_id = u.id ORDER BY k.id DESC");
                    while($kost = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kost['nama_kost']); ?></td>
                        <td><?php echo htmlspecialchars($kost['nama_pemilik']); ?></td>
                        <td><?php echo htmlspecialchars($kost['alamat']); ?></td>
                        <td>
                            <a href="view_kost_tenants.php?kost_id=<?php echo $kost['id']; ?>" class="btn btn-sm btn-success">Lihat Penghuni</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>