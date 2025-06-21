<?php
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';

// 1. Validasi ID Kost dari URL dan pastikan itu milik pemilik yang login
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Akses tidak valid.");
}
$kost_id = (int)$_GET['id'];

// Ambil data kost yang akan diedit
$stmt_check = $conn->prepare("SELECT * FROM kosts WHERE id = ?");
$stmt_check->bind_param("i", $kost_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows === 0) { die("Kost tidak ditemukan."); }

$kost = $result_check->fetch_assoc();
// Keamanan: Pastikan yang akses adalah admin atau pemilik asli
if ($_SESSION['role'] !== 'admin' && $kost['user_id'] != $_SESSION['user_id']) {
    die("Akses ditolak. Anda tidak memiliki hak untuk mengedit kost ini.");
}

// --- LOGIKA PEMROSESAN FORM UPDATE ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mulai transaksi database untuk memastikan semua query berhasil
    $conn->begin_transaction();

    try {
        // 1. UPDATE data utama di tabel 'kosts'
        $stmt_kost = $conn->prepare(
            "UPDATE kosts SET nama_kost = ?, deskripsi = ?, tipe_kost = ?, alamat = ?, kelurahan = ?, 
             latitude = ?, longitude = ?, harga_harian = ?, harga_bulanan = ?, harga_tahunan = ?, jumlah_total_kamar = ?
             WHERE id = ? AND user_id = ?"
        );
        $harga_harian = !empty($_POST['harga_harian']) ? $_POST['harga_harian'] : NULL;
        $harga_tahunan = !empty($_POST['harga_tahunan']) ? $_POST['harga_tahunan'] : NULL;
        $stmt_kost->bind_param("sssssssiisiii",
            $_POST['nama_kost'], $_POST['deskripsi'], $_POST['tipe_kost'], $_POST['alamat'], $_POST['kelurahan'],
            $_POST['latitude'], $_POST['longitude'], $harga_harian, $_POST['harga_bulanan'], $harga_tahunan,
            $_POST['jumlah_total_kamar'], $kost_id, $pemilik_id
        );
        $stmt_kost->execute();
        $stmt_kost->close();

        // 2. UPDATE fasilitas (Cara termudah: hapus semua lalu masukkan lagi yang baru)
        $conn->query("DELETE FROM kost_facilities WHERE kost_id = $kost_id");
        if (!empty($_POST['fasilitas'])) {
            $stmt_fac = $conn->prepare("INSERT INTO kost_facilities (kost_id, facility_id) VALUES (?, ?)");
            foreach ($_POST['fasilitas'] as $facility_id) {
                $stmt_fac->bind_param("ii", $kost_id, $facility_id);
                $stmt_fac->execute();
            }
            $stmt_fac->close();
        }
        
        // 3. HAPUS gambar yang dicentang
        if (!empty($_POST['delete_images'])) {
            $stmt_get_img_path = $conn->prepare("SELECT image_path FROM kost_images WHERE id = ?");
            $stmt_del_img = $conn->prepare("DELETE FROM kost_images WHERE id = ?");
            foreach ($_POST['delete_images'] as $image_id) {
                // Ambil path file untuk dihapus dari server
                $stmt_get_img_path->bind_param("i", $image_id);
                $stmt_get_img_path->execute();
                $img_result = $stmt_get_img_path->get_result()->fetch_assoc();
                if ($img_result) {
                    $file_to_delete = '../assets/uploads/' . $img_result['image_path'];
                    if (file_exists($file_to_delete)) {
                        unlink($file_to_delete); // Hapus file fisik
                    }
                }
                // Hapus record dari database
                $stmt_del_img->bind_param("i", $image_id);
                $stmt_del_img->execute();
            }
            $stmt_get_img_path->close();
            $stmt_del_img->close();
        }

        // 4. TAMBAH gambar baru
        if (!empty(array_filter($_FILES['gambar']['name']))) {
            $stmt_img = $conn->prepare("INSERT INTO kost_images (kost_id, image_path) VALUES (?, ?)");
            $upload_dir = '../assets/uploads/';
            foreach ($_FILES['gambar']['name'] as $key => $name) {
                if ($_FILES['gambar']['tmp_name'][$key]) {
                    $tmp_name = $_FILES['gambar']['tmp_name'][$key];
                    $file_name = uniqid() . '-' . basename($name);
                    $target_file = $upload_dir . $file_name;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $stmt_img->bind_param("is", $kost_id, $file_name);
                        $stmt_img->execute();
                    }
                }
            }
            $stmt_img->close();
        }

        // Jika semua proses berhasil, commit transaksi
        $conn->commit();
        header("Location: index.php?status=edit_sukses");
        exit();

    } catch (Exception $e) {
        // Jika terjadi kesalahan, batalkan semua perubahan
        $conn->rollback();
        die("Error saat mengupdate data: " . $e->getMessage());
    }
}

// Ambil data untuk ditampilkan di form
$facilities_all = $conn->query("SELECT * FROM facilities ORDER BY tipe_fasilitas, nama_fasilitas")->fetch_all(MYSQLI_ASSOC);
$current_facilities_res = $conn->query("SELECT facility_id FROM kost_facilities WHERE kost_id = $kost_id");
$current_facilities = array_column($current_facilities_res->fetch_all(MYSQLI_ASSOC), 'facility_id');
$current_images = $conn->query("SELECT id, image_path FROM kost_images WHERE kost_id = $kost_id")->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<h3>Form Edit Kost: <?php echo htmlspecialchars($kost['nama_kost']); ?></h3>
<hr>
<div class="card">
    <div class="card-body">
        <form action="edit_kost.php?id=<?php echo $kost_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nama Kost</label>
                <input type="text" name="nama_kost" class="form-control" value="<?php echo htmlspecialchars($kost['nama_kost']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"><?php echo htmlspecialchars($kost['deskripsi']); ?></textarea>
            </div>
             <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe Kost</label>
                    <select name="tipe_kost" class="form-select" required>
                        <option value="putra" <?php echo ($kost['tipe_kost'] == 'putra') ? 'selected' : ''; ?>>Putra</option>
                        <option value="putri" <?php echo ($kost['tipe_kost'] == 'putri') ? 'selected' : ''; ?>>Putri</option>
                        <option value="campur" <?php echo ($kost['tipe_kost'] == 'campur') ? 'selected' : ''; ?>>Campur</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jumlah Total Kamar</label>
                    <input type="number" name="jumlah_total_kamar" class="form-control" value="<?php echo htmlspecialchars($kost['jumlah_total_kamar']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <input type="text" name="alamat" class="form-control" value="<?php echo htmlspecialchars($kost['alamat']); ?>" required>
            </div>
             <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" name="kelurahan" class="form-control" value="<?php echo htmlspecialchars($kost['kelurahan']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" value="<?php echo htmlspecialchars($kost['latitude']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" value="<?php echo htmlspecialchars($kost['longitude']); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Harga Harian (Opsional)</label>
                    <input type="number" name="harga_harian" class="form-control" value="<?php echo htmlspecialchars($kost['harga_harian']); ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Harga Bulanan</label>
                    <input type="number" name="harga_bulanan" class="form-control" value="<?php echo htmlspecialchars($kost['harga_bulanan']); ?>" required>
                </div>
                 <div class="col-md-4 mb-3">
                    <label class="form-label">Harga Tahunan (Opsional)</label>
                    <input type="number" name="harga_tahunan" class="form-control" value="<?php echo htmlspecialchars($kost['harga_tahunan']); ?>">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Fasilitas</label>
                <div class="p-3 border rounded">
                    <?php 
                    $current_type = '';
                    $col_open = false;
                    foreach($facilities_all as $facility):
                        // Logika untuk membuat kolom baru saat tipe fasilitas berubah
                        if ($current_type != $facility['tipe_fasilitas']) {
                            if ($col_open) { echo '</div>'; } // Tutup div.col-md-6 sebelumnya jika ada
                            $current_type = $facility['tipe_fasilitas'];
                            echo '<div class="mb-3"><h5>Fasilitas '.ucfirst($current_type).'</h5>';
                            $col_open = true;
                        }
                    ?>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="fasilitas[]" 
                               value="<?php echo $facility['id']; ?>" id="fac<?php echo $facility['id']; ?>"
                               <?php echo in_array($facility['id'], $current_facilities) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="fac<?php echo $facility['id']; ?>"><?php echo htmlspecialchars($facility['nama_fasilitas']); ?></label>
                    </div>
                    <?php endforeach; 
                    if ($col_open) { echo '</div>'; } // Tutup div terakhir
                    ?>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Kelola Gambar Saat Ini</label>
                <div class="p-3 border rounded">
                    <?php if (empty($current_images)): ?>
                        <p>Belum ada gambar yang diupload.</p>
                    <?php else: ?>
                        <div class="row">
                        <?php foreach($current_images as $image): ?>
                            <div class="col-md-3 text-center mb-3">
                                <img src="../assets/uploads/<?php echo htmlspecialchars($image['image_path']); ?>" class="img-thumbnail mb-2" alt="Foto Kost">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="delete_images[]" value="<?php echo $image['id']; ?>" id="del_img_<?php echo $image['id']; ?>">
                                    <label class="form-check-label text-danger" for="del_img_<?php echo $image['id']; ?>">Hapus</label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="formFileMultiple" class="form-label">Tambah Foto Baru (opsional)</label>
                <input class="form-control" type="file" name="gambar[]" id="formFileMultiple" multiple>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>