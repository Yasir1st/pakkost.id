<?php
// File: /klp1/reset_password.php (Versi Baru)
include 'includes/header.php';

// Keamanan: Cek apakah pengguna sudah melewati halaman verifikasi
if (!isset($_SESSION['reset_allowed']) || $_SESSION['reset_allowed'] !== true) {
    // Jika mencoba akses langsung tanpa verifikasi, tolak.
    echo "<div class='alert alert-danger'>Akses tidak sah. Harap lakukan verifikasi akun terlebih dahulu.</div>";
    include 'includes/footer.php';
    exit();
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Buat Password Baru</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['error']) && $_GET['error'] == 'mismatch'): ?>
                    <div class="alert alert-danger">Password dan konfirmasi password tidak cocok.</div>
                <?php endif; ?>

                <form action="/klp1/auth/proses_reset_password.php" method="POST">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="password_confirm" id="password_confirm" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>