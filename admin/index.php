<?php
require_once '../config/database.php';
require_once 'auth_admin_check.php';

// Menghitung statistik untuk ditampilkan
$total_users = $conn->query("SELECT COUNT(id) as total FROM users")->fetch_assoc()['total'];
$total_kost = $conn->query("SELECT COUNT(id) as total FROM kosts")->fetch_assoc()['total'];
$total_rentals_aktif = $conn->query("SELECT COUNT(id) as total FROM rentals WHERE status = 'aktif'")->fetch_assoc()['total'];

include '../includes/header.php';
?>

<h3>Dashboard Administrator</h3>
<p>Selamat datang, Admin <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!</p>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-users"></i> Total Pengguna</h5>
                <p class="card-text fs-2 fw-bold"><?php echo $total_users; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-building"></i> Total Properti Kost</h5>
                <p class="card-text fs-2 fw-bold"><?php echo $total_kost; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-key"></i> Total Sewa Aktif</h5>
                <p class="card-text fs-2 fw-bold"><?php echo $total_rentals_aktif; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Menu Manajemen
    </div>
    <div class="card-body">
        <div class="list-group">
            <a href="manage_users.php" class="list-group-item list-group-item-action">
                <i class="fas fa-user-cog me-2"></i> Kelola Semua Pengguna
            </a>
            <a href="manage_kost.php" class="list-group-item list-group-item-action">
                <i class="fas fa-building-user me-2"></i> Kelola Semua Properti Kost
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>