<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Buat Akun Baru</h3>
            </div>
            <div class="card-body">
                <?php
                // Cek apakah ada parameter 'error' di URL
                if (isset($_GET['error'])) {
                    $error_msg = '';
                    if ($_GET['error'] == 'duplicate') {
                        $error_msg = 'Registrasi gagal. Username atau Email sudah terdaftar.';
                    } else {
                        // Pesan error umum jika masalahnya bukan duplikat
                        $error_msg = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
                    }
                    // Tampilkan pesan dalam bentuk alert Bootstrap
                    echo '<div class="alert alert-danger">' . htmlspecialchars($error_msg) . '</div>';
                }
                ?>
                <form action="/klp1/auth/proses_register.php" method="POST">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                     <div class="mb-3">
                        <label for="phone_number" class="form-label">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Daftar sebagai:</label>
                        <select class="form-select" name="role" id="role" required>
                            <option value="penyewa">Penyewa Kost</option>
                            <option value="pemilik">Pemilik Kost</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Daftar</button>
                </form>
                 <div class="text-center mt-3">
                    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>