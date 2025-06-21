<?php
// File: /klp1/forgot_password.php (Versi Baru)
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Verifikasi Akun untuk Reset Password</h3>
            </div>
            <div class="card-body">
                <p>Untuk mereset password, silakan masukkan Username, Email, dan Nomor Telepon yang sesuai dengan akun Anda.</p>
                
                <?php if (isset($_GET['error']) && $_GET['error'] == 'data_tidak_cocok'): ?>
                    <div class="alert alert-danger">
                        Informasi yang Anda masukkan tidak cocok. Pastikan semua data benar dan coba lagi.
                    </div>
                <?php endif; ?>

                <form action="/klp1/auth/proses_verifikasi_user.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verifikasi Akun Saya</button>
                </form>
                 <div class="text-center mt-3">
                    <p><a href="login.php">Kembali ke halaman Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>