<?php
// File: /klp1/includes/header.php

// Memulai session jika belum ada, penting untuk autentikasi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mengambil nama file saat ini untuk menandai link navigasi yang aktif
$current_page = basename($_SERVER['PHP_SELF']);

// Memanggil file koneksi database hanya jika diperlukan (opsional, tapi baik untuk ada)
require_once __DIR__ . '/../config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAKKOST - Solusi Cari Kost Terbaik</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <link rel="stylesheet" href="/klp1/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/klp1/index.php">
            <i class="fas fa-home me-2"></i>PAKKOST
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="/klp1/index.php">Cari Kost</a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <?php
                    // Logika untuk menentukan link dashboard dan folder aktif
                    $dashboard_link = '';
                    $dashboard_folder = '';
                    if ($_SESSION['role'] == 'pemilik') {
                        $dashboard_link = '/klp1/pemilik/index.php';
                        $dashboard_folder = 'pemilik';
                    } elseif ($_SESSION['role'] == 'penyewa') {
                        $dashboard_link = '/klp1/penyewa/dashboard.php';
                        $dashboard_folder = 'penyewa';
                    } elseif ($_SESSION['role'] == 'admin') {
                        $dashboard_link = '/klp1/admin/index.php';
                        $dashboard_folder = 'admin';
                    }
                    // Cek apakah halaman saat ini berada di dalam folder dashboard pengguna
                    $is_in_dashboard = (strpos($_SERVER['PHP_SELF'], "/$dashboard_folder/") !== false);
                    ?>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $is_in_dashboard ? 'active' : ''; ?>" href="<?php echo $dashboard_link; ?>">Dashboard Saya</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item text-danger" href="/klp1/auth/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="/klp1/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="/klp1/register.php">Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4 flex-grow-1">