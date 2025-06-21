<?php 
require_once '../config/database.php';
require_once 'auth_pemilik_check.php';

// Logika untuk memproses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mulai transaksi
    $conn->begin_transaction();

    try {
        // 1. Insert data ke tabel 'kosts'
        $stmt_kost = $conn->prepare(
            "INSERT INTO kosts (user_id, nama_kost, deskripsi, tipe_kost, alamat, kelurahan, latitude, longitude, harga_harian, harga_bulanan, harga_tahunan, jumlah_total_kamar) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $harga_harian = !empty($_POST['harga_harian']) ? $_POST['harga_harian'] : NULL;
        $harga_tahunan = !empty($_POST['harga_tahunan']) ? $_POST['harga_tahunan'] : NULL;
        $stmt_kost->bind_param("isssssssiiii", 
            $pemilik_id, $_POST['nama_kost'], $_POST['deskripsi'], $_POST['tipe_kost'], $_POST['alamat'], 
            $_POST['kelurahan'], $_POST['latitude'], $_POST['longitude'], $harga_harian, $_POST['harga_bulanan'], 
            $harga_tahunan, $_POST['jumlah_total_kamar']
        );
        $stmt_kost->execute();
        $new_kost_id = $conn->insert_id; // Ambil ID kost yang baru dibuat

        // 2. Insert data ke tabel 'kost_facilities'
        if (!empty($_POST['fasilitas'])) {
            $stmt_fac = $conn->prepare("INSERT INTO kost_facilities (kost_id, facility_id) VALUES (?, ?)");
            foreach ($_POST['fasilitas'] as $facility_id) {
                $stmt_fac->bind_param("ii", $new_kost_id, $facility_id);
                $stmt_fac->execute();
            }
            $stmt_fac->close();
        }

        // 3. Proses upload gambar dan insert ke 'kost_images'
        if (!empty(array_filter($_FILES['gambar']['name']))) {
            $stmt_img = $conn->prepare("INSERT INTO kost_images (kost_id, image_path) VALUES (?, ?)");
            $upload_dir = '../assets/uploads/';

            foreach ($_FILES['gambar']['name'] as $key => $name) {
                $tmp_name = $_FILES['gambar']['tmp_name'][$key];
                $file_name = uniqid() . '-' . basename($name);
                $target_file = $upload_dir . $file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $stmt_img->bind_param("is", $new_kost_id, $file_name);
                    $stmt_img->execute();
                }
            }
            $stmt_img->close();
        }

        // Jika semua berhasil, commit transaksi
        $conn->commit();
        header("Location: index.php?status=tambah_sukses");
        exit();

    } catch (Exception $e) {
        // Jika ada error, rollback transaksi
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

// Ambil daftar fasilitas dari DB untuk ditampilkan di form
$facilities = $conn->query("SELECT * FROM facilities ORDER BY tipe_fasilitas, nama_fasilitas")->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<h3>Form Tambah Kost Baru</h3>
<hr>
<div class="card">
    <div class="card-body">
        <form action="tambah_kost.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nama Kost</label>
                <input type="text" name="nama_kost" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"></textarea>
            </div>
             <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe Kost</label>
                    <select name="tipe_kost" class="form-select" required>
                        <option value="putra">Putra</option>
                        <option value="putri">Putri</option>
                        <option value="campur">Campur</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jumlah Total Kamar</label>
                    <input type="number" name="jumlah_total_kamar" class="form-control" value="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <input type="text" name="alamat" class="form-control" required>
            </div>
             <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kelurahan</label>
                    <input type="text" name="kelurahan" class="form-control" placeholder="cth: Banggae" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Latitude (untuk Peta)</label>
                    <input type="text" name="latitude" class="form-control" placeholder="-3.53xxxx">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Longitude (untuk Peta)</label>
                    <input type="text" name="longitude" class="form-control" placeholder="118.88xxxx">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Harga Harian (Opsional)</label>
                    <input type="number" name="harga_harian" class="form-control" placeholder="cth: 50000">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Harga Bulanan</label>
                    <input type="number" name="harga_bulanan" class="form-control" placeholder="cth: 450000" required>
                </div>
                 <div class="col-md-4 mb-3">
                    <label class="form-label">Harga Tahunan (Opsional)</label>
                    <input type="number" name="harga_tahunan" class="form-control" placeholder="cth: 5000000">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Fasilitas</label>
                <div class="row">
                    <?php 
                    $current_type = '';
                    foreach($facilities as $facility):
                        if ($current_type != $facility['tipe_fasilitas']) {
                            $current_type = $facility['tipe_fasilitas'];
                            echo '</div><div class="col-md-6"><h5>Fasilitas '.ucfirst($current_type).'</h5>';
                        }
                    ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="fasilitas[]" value="<?php echo $facility['id']; ?>" id="fac<?php echo $facility['id']; ?>">
                        <label class="form-check-label" for="fac<?php echo $facility['id']; ?>"><?php echo $facility['nama_fasilitas']; ?></label>
                    </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="formFileMultiple" class="form-label">Upload Foto Kost (bisa pilih lebih dari satu)</label>
                <input class="form-control" type="file" name="gambar[]" id="formFileMultiple" multiple>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Kost</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>