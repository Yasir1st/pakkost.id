<?php
require_once '../config/database.php';
require_once 'auth_penyewa_check.php';

// Validasi rental_id dari URL
if (!isset($_GET['rental_id']) || !is_numeric($_GET['rental_id'])) {
    die("ID Rental tidak valid.");
}
$rental_id = (int)$_GET['rental_id'];

// Query untuk verifikasi: Apakah rental ini ada, milik user ini, statusnya 'selesai', dan belum diulas?
$stmt = $conn->prepare(
    "SELECT r.id, r.kost_id, k.nama_kost 
     FROM rentals r
     JOIN kosts k ON r.kost_id = k.id
     WHERE r.id = ? 
       AND r.user_id = ? 
       AND r.status = 'selesai' 
       AND NOT EXISTS (SELECT 1 FROM reviews WHERE reviews.kost_id = r.kost_id AND reviews.user_id = r.user_id)"
);
$stmt->bind_param("ii", $rental_id, $penyewa_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika salah satu syarat tidak terpenuhi, tolak akses
    header("Location: dashboard.php?error=tidak_bisa_ulas");
    exit();
}
$data = $result->fetch_assoc();
$kost_id = $data['kost_id'];
$nama_kost = $data['nama_kost'];
$stmt->close();

include '../includes/header.php';
?>

<h3>Beri Ulasan untuk "<?php echo htmlspecialchars($nama_kost); ?>"</h3>
<p>Bagikan pengalaman Anda untuk membantu penyewa lainnya.</p>

<div class="card">
    <div class="card-body">
        <form action="/klp1/auth/proses_ulasan.php" method="POST">
            <input type="hidden" name="kost_id" value="<?php echo $kost_id; ?>">
            
            <div class="mb-3">
                <label for="rating" class="form-label"><strong>Rating Anda:</strong></label>
                <div id="rating" class="fs-3">
                    <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" title="Sangat Baik">5 stars</label>
                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Baik">4 stars</label>
                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Cukup">3 stars</label>
                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Buruk">2 stars</label>
                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1"title="Sangat Buruk">1 star</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="ulasan" class="form-label"><strong>Ulasan Tertulis:</strong></label>
                <textarea class="form-control" name="ulasan" id="ulasan" rows="5" placeholder="Ceritakan pengalaman Anda mengenai kebersihan, fasilitas, keamanan, dan pemilik kost..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
            <a href="dashboard.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<style>
#rating { display: inline-block; border: none; }
#rating > input { display: none; }
#rating > label {
    color: #ddd;
    float: right;
    font-size: 2rem;
    cursor: pointer;
}
#rating > input:checked ~ label,
#rating:not(:checked) > label:hover,
#rating:not(:checked) > label:hover ~ label { color: #f7d106; }
#rating > input:checked + label:hover,
#rating > input:checked ~ label:hover,
#rating > label:hover ~ input:checked ~ label,
#rating > input:checked ~ label:hover ~ label { color: #f7c206; }
</style>

<?php include '../includes/footer.php'; ?>