<?php
require_once '../config/database.php';
require_once 'auth_admin_check.php';

// Logika untuk UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $username, $email, $role, $user_id);
    $stmt->execute();
    header("Location: manage_users.php?status=edit_sukses");
    exit();
}

// Mengambil data user yang akan diedit
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("ID User tidak valid."); }
$user_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) { die("User tidak ditemukan."); }

include '../includes/header.php';
?>

<h3>Edit Pengguna: <?php echo htmlspecialchars($user['full_name']); ?></h3>

<div class="card">
    <div class="card-body">
        <form action="edit_user.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="penyewa" <?php echo ($user['role'] == 'penyewa') ? 'selected' : ''; ?>>Penyewa</option>
                    <option value="pemilik" <?php echo ($user['role'] == 'pemilik') ? 'selected' : ''; ?>>Pemilik</option>
                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="manage_users.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>