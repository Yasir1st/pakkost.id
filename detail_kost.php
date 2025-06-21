<?php
// File: /klp1/detail_kost.php

include 'includes/header.php';

// 1. Validasi dan Ambil ID Kost dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Kost tidak valid.</div>";
    include 'includes/footer.php';
    exit;
}
$kost_id = (int)$_GET['id'];

// 2. Query untuk mengambil data utama kost dan info pemilik
$stmt_kost = $conn->prepare(
    "SELECT 
        k.*, 
        u.full_name, 
        u.phone_number,
        (k.jumlah_total_kamar - (SELECT COUNT(*) FROM rentals WHERE kost_id = k.id AND status = 'aktif')) AS kamar_tersedia
     FROM kosts k 
     JOIN users u ON k.user_id = u.id 
     WHERE k.id = ?"
);
$stmt_kost->bind_param("i", $kost_id);
$stmt_kost->execute();
$result_kost = $stmt_kost->get_result();

if ($result_kost->num_rows === 0) {
    echo "<div class='alert alert-danger'>Kost tidak ditemukan.</div>";
    include 'includes/footer.php';
    exit;
}
$kost = $result_kost->fetch_assoc();

// 3. Query untuk mengambil gambar-gambar kost
$stmt_images = $conn->prepare("SELECT image_path FROM kost_images WHERE kost_id = ?");
$stmt_images->bind_param("i", $kost_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
$images = $result_images->fetch_all(MYSQLI_ASSOC);

// 4. Query untuk mengambil fasilitas
$stmt_facilities = $conn->prepare(
    "SELECT f.nama_fasilitas, f.tipe_fasilitas
     FROM facilities f
     JOIN kost_facilities kf ON f.id = kf.facility_id
     WHERE kf.kost_id = ?"
);
$stmt_facilities->bind_param("i", $kost_id);
$stmt_facilities->execute();
$result_facilities = $stmt_facilities->get_result();
$facilities_kamar = [];
$facilities_umum = [];
while ($facility = $result_facilities->fetch_assoc()) {
    if ($facility['tipe_fasilitas'] == 'kamar') {
        $facilities_kamar[] = $facility['nama_fasilitas'];
    } else {
        $facilities_umum[] = $facility['nama_fasilitas'];
    }
}

// 5. Query untuk data ulasan dan rating
$stmt_rating = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE kost_id = ?");
$stmt_rating->bind_param("i", $kost_id);
$stmt_rating->execute();
$rating_data = $stmt_rating->get_result()->fetch_assoc();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total_reviews'] ?? 0;
$stmt_rating->close();

$stmt_reviews = $conn->prepare(
    "SELECT r.*, u.full_name
     FROM reviews r
     JOIN users u ON r.user_id = u.id
     WHERE r.kost_id = ?
     ORDER BY r.created_at DESC"
);
$stmt_reviews->bind_param("i", $kost_id);
$stmt_reviews->execute();
$reviews = $stmt_reviews->get_result();

?>

<div class="row">
    <div class="col-lg-8">
        <div id="kostCarousel" class="carousel slide shadow-sm rounded mb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php if (!empty($images)) : ?>
                    <?php foreach ($images as $index => $img) : ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <img src="/klp1/assets/uploads/<?php echo htmlspecialchars($img['image_path']); ?>" class="d-block w-100" style="height: 450px; object-fit: cover;" alt="Foto Kost <?php echo $index + 1; ?>">
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="carousel-item active">
                        <img src="https://via.placeholder.com/800x450.png?text=Belum+Ada+Foto" class="d-block w-100" alt="Foto tidak tersedia">
                    </div>
                <?php endif; ?>
            </div>
            <?php if (count($images) > 1) : ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#kostCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#kostCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            <?php endif; ?>
        </div>

        <h1><?php echo htmlspecialchars($kost['nama_kost']); ?></h1>
        <?php
        // Logika untuk menentukan warna badge tipe kost
        $tipe_kost = $kost['tipe_kost'];
        $badge_class = '';
        $text_class = 'text-white';

        if ($tipe_kost == 'putra') {
            $badge_class = 'bg-info';
            $text_class = 'text-dark';
        } elseif ($tipe_kost == 'putri') {
            $badge_class = 'bg-pink';
        } elseif ($tipe_kost == 'campur') {
            $badge_class = 'bg-success';
        }
        ?>
        <p class="text-muted"><span class="badge <?php echo $badge_class; ?> <?php echo $text_class; ?>"><?php echo ucfirst($tipe_kost); ?></span> - <?php echo htmlspecialchars($kost['alamat']); ?></p>

        <hr>

        <h4>Deskripsi Kost</h4>
        <p><?php echo nl2br(htmlspecialchars($kost['deskripsi'])); ?></p>

        <hr>

        <h4>Fasilitas</h4>
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-bed"></i> Fasilitas Kamar</h5>
                <ul>
                    <?php foreach ($facilities_kamar as $f) : ?>
                        <li><?php echo htmlspecialchars($f); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6">
                <h5><i class="fas fa-home"></i> Fasilitas Umum</h5>
                <ul>
                    <?php foreach ($facilities_umum as $f) : ?>
                        <li><?php echo htmlspecialchars($f); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 80px;">
            <div class="card-body">
                <h5 class="card-title">Harga Sewa</h5>
                <ul class="list-group list-group-flush">
                    <?php if ($kost['harga_harian']) : ?>
                        <li class="list-group-item">Harian: <b>Rp <?php echo number_format($kost['harga_harian']); ?></b></li>
                    <?php endif; ?>
                    <li class="list-group-item">Bulanan: <b>Rp <?php echo number_format($kost['harga_bulanan']); ?></b></li>
                    <?php if ($kost['harga_tahunan']) : ?>
                        <li class="list-group-item">Tahunan: <b>Rp <?php echo number_format($kost['harga_tahunan']); ?></b></li>
                    <?php endif; ?>
                </ul>
                <p class="mt-3">Ketersediaan: <b><?php echo $kost['kamar_tersedia']; ?> Kamar Tersisa</b></p>
                <hr>

                <h5 class="card-title">Info Pemilik</h5>
                <p><b><?php echo htmlspecialchars($kost['full_name']); ?></b><br>
                    <a href="tel:<?php echo htmlspecialchars($kost['phone_number']); ?>" class="btn btn-primary mt-2 w-100">
                        <i class="fab fa-whatsapp"></i> Hubungi via Telepon/WA
                    </a>
                </p>

                <?php
                // Tampilkan tombol Ajukan Sewa HANYA jika user login sebagai 'penyewa'
                if (isset($_SESSION['role']) && $_SESSION['role'] == 'penyewa') : ?>
                    <a href="/klp1/penyewa/ajukan_sewa.php?kost_id=<?php echo $kost_id; ?>" class="btn btn-lg btn-success w-100 my-3">
                        Ajukan Sewa Sekarang
                    </a>
                <?php elseif (!isset($_SESSION['user_id'])) : ?>
                    <a href="/klp1/login.php" class="btn btn-lg btn-success w-100 my-3">
                        Login untuk Mengajukan Sewa
                    </a>
                <?php endif; ?>

                <hr>

                <h5 class="card-title">Tanya Pemilik</h5>
                <form action="/klp1/auth/proses_inquiry.php" method="POST">
                    <input type="hidden" name="kost_id" value="<?php echo $kost_id; ?>">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="nama_pengirim" placeholder="Nama Anda" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" name="email_pengirim" placeholder="Email Anda" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="pertanyaan" rows="3" placeholder="Tulis pertanyaan Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary w-100">Kirim Pertanyaan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <h4>Lokasi di Peta</h4>
    <?php
    // Pastikan data koordinat ada di database
    if (!empty($kost['latitude']) && !empty($kost['longitude'])) :
    ?>
        <div class="ratio ratio-16x9 rounded shadow-sm">
            <iframe
                src="https://maps.google.com/maps?q=<?php echo $kost['latitude']; ?>,<?php echo $kost['longitude']; ?>&hl=id&z=16&output=embed"
                width="100%"
                height="450"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>
    <?php else : ?>
        <div class="alert alert-warning">
            Peta tidak dapat ditampilkan karena data koordinat (latitude/longitude) untuk kost ini belum diatur oleh pemilik.
        </div>
    <?php endif; ?>
</div>

<hr class="my-4">
<div class="row">
    <div class="col-md-12">
        <h4>Ulasan & Rating (<?php echo $total_reviews; ?> ulasan)</h4>
        <?php if ($total_reviews > 0) : ?>
            <div class="card bg-light p-3 mb-3">
                <div class="d-flex align-items-center">
                    <span class="fs-1 fw-bold me-3"><?php echo $avg_rating; ?></span>
                    <div>
                        <div class="text-warning">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= floor($avg_rating) ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <span>Dari 5 bintang</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($reviews->num_rows > 0) : ?>
            <?php while ($review = $reviews->fetch_assoc()) : ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                            <span class="text-muted"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                        </div>
                        <div class="text-warning my-1">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $review['rating'] ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <p class="card-text"><?php echo nl2br(htmlspecialchars($review['ulasan'])); ?></p>

                        <?php if (!empty($review['balasan_pemilik'])) : ?>
                            <div class="card mt-3 bg-light border-start border-primary border-3">
                                <div class="card-body">
                                    <h6 class="card-title">Balasan dari Pemilik</h6>
                                    <p class="card-text fst-italic"><?php echo nl2br(htmlspecialchars($review['balasan_pemilik'])); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p>Belum ada ulasan untuk kost ini. Jadilah yang pertama memberikan ulasan!</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Menutup semua statement dan koneksi
$stmt_kost->close();
$stmt_images->close();
$stmt_facilities->close();
$stmt_reviews->close();
include 'includes/footer.php';
?>