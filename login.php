<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <h3>Login</h3>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['status']) && $_GET['status'] == 'reg_success'): ?>
                    <div class="alert alert-success">Registrasi berhasil! Silakan login.</div>
                <?php endif; ?>
                <?php if(isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>

                <form action="/klp1/auth/proses_login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username atau Email</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                    <p><a href="forgot_password.php">Lupa Password?</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>