<?php include 'includes/header.php'; ?>

<div class="p-5 mb-4 bg-light rounded-3 text-center">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Temukan Kost Impian Anda di Majene</h1>
        <p class="fs-4">Cari dan temukan kost dengan mudah sesuai kebutuhan Anda.</p>
    </div>
</div>

<div class="card card-body mb-4">
    <form method="GET" action="index.php">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="lokasi" class="form-label">Lokasi (Kelurahan)</label>
                <input type="text" class="form-control" name="lokasi" placeholder="cth: Banggae, Totoli" value="<?php echo htmlspecialchars($_GET['lokasi'] ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <label for="tipe" class="form-label">Tipe Kost</label>
                <select name="tipe" class="form-select">
                    <option value="">Semua</option>
                    <option value="putra" <?php echo (($_GET['tipe'] ?? '') == 'putra') ? 'selected' : ''; ?>>Putra</option>
                    <option value="putri" <?php echo (($_GET['tipe'] ?? '') == 'putri') ? 'selected' : ''; ?>>Putri</option>
                    <option value="campur" <?php echo (($_GET['tipe'] ?? '') == 'campur') ? 'selected' : ''; ?>>Campur</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="harga_max" class="form-label">Harga Maks/Bulan (Rp)</label>
                <input type="number" class="form-control" name="harga_max" placeholder="cth: 500000" value="<?php echo htmlspecialchars($_GET['harga_max'] ?? ''); ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Cari Kost</button>
            </div>
        </div>
    </form>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php
    // Membangun Query Dinamis
    $sql = "SELECT k.*, (SELECT image_path FROM kost_images WHERE kost_id = k.id LIMIT 1) as main_image FROM kosts k WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($_GET['lokasi'])) {
        $sql .= " AND k.kelurahan LIKE ?";
        $params[] = '%' . $_GET['lokasi'] . '%';
        $types .= 's';
    }
    if (!empty($_GET['tipe'])) {
        $sql .= " AND k.tipe_kost = ?";
        $params[] = $_GET['tipe'];
        $types .= 's';
    }
    if (!empty($_GET['harga_max'])) {
        $sql .= " AND k.harga_bulanan <= ?";
        $params[] = $_GET['harga_max'];
        $types .= 'i';
    }
    
    $sql .= " ORDER BY k.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($kost = $result->fetch_assoc()) {
    ?>
    <div class="col">
        <div class="card h-100">
            <img src="<?php echo htmlspecialchars($kost['main_image'] ? '/klp1/assets/uploads/' . $kost['main_image'] : 'https://via.placeholder.com/300x200.png?text=Foto+Kost'); ?>" class="card-img-top" alt="Foto <?php echo htmlspecialchars($kost['nama_kost']); ?>" style="height: 200px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($kost['nama_kost']); ?></h5>
                <p class="card-text text-muted"><small><?php echo htmlspecialchars($kost['alamat']); ?>, Kel. <?php echo htmlspecialchars($kost['kelurahan']); ?></small></p>
                <span class="badge bg-info text-dark mb-2"><?php echo ucfirst($kost['tipe_kost']); ?></span>
                <p class="card-text fw-bold fs-5">Rp <?php echo number_format($kost['harga_bulanan'], 0, ',', '.'); ?> / bulan</p>
                <a href="detail_kost.php?id=<?php echo $kost['id']; ?>" class="btn btn-outline-primary stretched-link">Lihat Detail</a>
            </div>
        </div>
    </div>
    <?php
        }
    } else {
        echo "<p class='text-center'>Tidak ada kost yang ditemukan dengan kriteria pencarian Anda.</p>";
    }
    $stmt->close();
    ?>
</div>

<?php include 'includes/footer.php'; ?>