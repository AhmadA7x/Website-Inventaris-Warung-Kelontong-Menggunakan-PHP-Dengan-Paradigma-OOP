<?php
// ============================================================
// FILE: index.php (VERSI OOP)
// PENERAPAN KONSEP: OBJECT, CLASS, CONSTRUCTOR, INHERITANCE, ENCAPSULATION
// Halaman utama dashboard inventaris warung
// ============================================================

session_start();

// Cek apakah user sudah login, jika belum redirect ke login.php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Memuat file class yang dibutuhkan
require_once 'Database.php';
require_once 'BarangManager.php';
require_once 'Pengguna.php';

// ============================================================
// OBJECT & CONSTRUCTOR — Membuat object dari class Database
// Saat 'new Database()' dipanggil, CONSTRUCTOR __construct() berjalan otomatis
// dan langsung membuat koneksi ke database
// ============================================================
$db = new Database();                    // OBJECT & CONSTRUCTOR — Instansiasi class Database
$koneksi = $db->getKoneksi();            // ENCAPSULATION — Mengakses koneksi via getter method

// ============================================================
// OBJECT & CONSTRUCTOR — Membuat object dari class BarangManager
// CONSTRUCTOR menerima parameter $koneksi dan menyimpannya ke property private
// ============================================================
$manager = new BarangManager($koneksi);  // OBJECT & CONSTRUCTOR — Instansiasi class BarangManager

// ============================================================
// INHERITANCE — Membuat object sesuai ROLE yang tersimpan di SESSION
// Jika role = 'admin', buat OBJECT dari CHILD CLASS Admin
// Jika role = 'kasir', buat OBJECT dari CHILD CLASS Kasir
// Kedua class ini mewarisi (extends) class Pengguna (PARENT CLASS)
// ============================================================
if ($_SESSION['role'] == 'admin') {
    $user = new Admin($koneksi);         // OBJECT & INHERITANCE — Instansiasi child class Admin
} else {
    $user = new Kasir($koneksi);         // OBJECT & INHERITANCE — Instansiasi child class Kasir
}

// ============================================================
// Menggunakan METHOD dari OBJECT $manager untuk mengambil data
// Ini menggantikan pemanggilan fungsi prosedural sebelumnya
// ============================================================
$keyword = '';
if (isset($_GET['cari'])) {
    $keyword = $_GET['cari'];
}

$daftar_barang = $manager->ambilSemuaBarang($keyword);  // Memanggil method dari OBJECT
$total_jenis   = $manager->hitungBarang();               // Memanggil method dari OBJECT
$total_unit    = $manager->hitungTotalUnit();             // Memanggil method dari OBJECT
$total_aman    = $manager->hitungStokAman();              // Memanggil method dari OBJECT
$total_menipis = $manager->hitungStokMenipis();           // Memanggil method dari OBJECT

// Proses pesan notifikasi
$pesan = '';
$tipe_pesan = '';
if (isset($_GET['pesan'])) {
    switch ($_GET['pesan']) {
        case 'tambah_sukses':
            $pesan = 'Data barang berhasil ditambahkan!';
            $tipe_pesan = 'success';
            break;
        case 'update_sukses':
            $pesan = 'Data barang berhasil diperbarui!';
            $tipe_pesan = 'success';
            break;
        case 'hapus_sukses':
            $pesan = 'Data barang berhasil dihapus!';
            $tipe_pesan = 'success';
            break;
        case 'gagal':
            $pesan = 'Terjadi kesalahan! Silakan coba lagi.';
            $tipe_pesan = 'danger';
            break;
        case 'tidak_ditemukan':
            $pesan = 'Data barang tidak ditemukan!';
            $tipe_pesan = 'warning';
            break;
        case 'akses_ditolak':
            $pesan = 'Anda tidak memiliki akses untuk melakukan aksi tersebut!';
            $tipe_pesan = 'danger';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventaris Warung Kelontong Bu Tutik - Sistem Manajemen Stok Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; color: #1a1a2e; min-height: 100vh; }

        /* NAVBAR */
        .navbar-custom { background: var(--primary-gradient); padding: 1rem 0; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3); }
        .navbar-custom .navbar-brand { font-weight: 800; font-size: 1.4rem; color: #fff !important; letter-spacing: -0.5px; }
        .navbar-custom .navbar-brand i { background: rgba(255,255,255,0.2); padding: 8px 10px; border-radius: 12px; margin-right: 10px; }
        .nav-link-custom { color: rgba(255,255,255,0.85) !important; font-weight: 500; padding: 8px 16px !important; border-radius: 10px; transition: var(--transition); }
        .nav-link-custom:hover, .nav-link-custom.active { color: #fff !important; background: rgba(255,255,255,0.15); }

        /* USER INFO */
        .user-info {
            display: flex; align-items: center; gap: 10px;
            color: #fff; font-size: 0.85rem; font-weight: 500;
        }
        .user-badge {
            background: rgba(255,255,255,0.2); padding: 4px 12px;
            border-radius: 8px; font-size: 0.75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .btn-logout {
            background: rgba(220, 38, 38, 0.85); border: 1px solid rgba(220, 38, 38, 0.5);
            color: #fff; font-weight: 600; padding: 6px 16px; border-radius: 10px;
            transition: var(--transition); text-decoration: none; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 6px;
        }
        .btn-logout:hover { background: #b91c1c; color: #fff; }

        /* HERO */
        .hero-section { background: var(--primary-gradient); padding: 0 0 80px 0; margin-top: -1px; }
        .hero-content { text-align: center; padding-top: 20px; }
        .hero-content h1 { font-weight: 800; font-size: 2rem; color: #fff; margin-bottom: 8px; }
        .hero-content p { color: rgba(255,255,255,0.8); font-size: 1.05rem; }

        /* STAT CARDS */
        .stats-container { margin-top: -50px; position: relative; z-index: 10; }
        .stat-card { background: #fff; border-radius: var(--border-radius); padding: 24px; box-shadow: var(--card-shadow); transition: var(--transition); border: 1px solid rgba(0,0,0,0.04); height: 100%; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--card-hover-shadow); }
        .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 16px; }
        .stat-icon.blue { background: #e8f0fe; color: #4285f4; }
        .stat-icon.green { background: #e6f7ed; color: #34a853; }
        .stat-icon.purple { background: #f3e8fd; color: #9334e6; }
        .stat-icon.red { background: #fce8e6; color: #ea4335; }
        .stat-number { font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 4px; }
        .stat-label { font-size: 0.85rem; color: #5f6368; font-weight: 500; }

        /* MAIN CARD */
        .main-card { background: #fff; border-radius: var(--border-radius); box-shadow: var(--card-shadow); border: 1px solid rgba(0,0,0,0.04); overflow: hidden; }
        .main-card-header { padding: 24px 28px; border-bottom: 1px solid #f1f3f4; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
        .main-card-header h5 { font-weight: 700; margin: 0; font-size: 1.05rem; color: #1a1a2e; }
        .main-card-body { padding: 0; }

        /* TABLE */
        .table-custom { margin: 0; }
        .table-custom thead th { background: #f8f9fa; font-weight: 600; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; color: #5f6368; border: none; padding: 14px 20px; }
        .table-custom tbody td { padding: 16px 20px; vertical-align: middle; border-color: #f1f3f4; font-size: 0.9rem; }
        .table-custom tbody tr:hover { background: #fafbff; }
        .nama-barang { font-weight: 600; color: #1a1a2e; }

        /* SEARCH */
        .search-box { position: relative; display: flex; align-items: center; }
        .search-box .form-control { padding-left: 40px; border-radius: 12px; border: 2px solid #e9ecef; font-size: 0.9rem; width: 260px; transition: var(--transition); }
        .search-box .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); width: 300px; }
        .search-icon { position: absolute; left: 14px; color: #9ca3af; font-size: 0.9rem; z-index: 1; }

        /* BUTTONS */
        .btn-primary-gradient { background: var(--primary-gradient); border: none; color: #fff; font-weight: 600; padding: 10px 20px; border-radius: 12px; transition: var(--transition); display: inline-flex; align-items: center; gap: 8px; text-decoration: none; font-size: 0.9rem; }
        .btn-primary-gradient:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); color: #fff; }
        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.95rem; transition: var(--transition); text-decoration: none; border: none; cursor: pointer; }
        .btn-edit { background: #e8f0fe; color: #4285f4; }
        .btn-edit:hover { background: #4285f4; color: #fff; transform: scale(1.1); }
        .btn-delete { background: #fce8e6; color: #ea4335; }
        .btn-delete:hover { background: #ea4335; color: #fff; transform: scale(1.1); }
        .btn-disabled { background: #f1f3f4; color: #c4c4c4; cursor: not-allowed; pointer-events: none; }

        /* BADGES */
        .badge { font-weight: 600; font-size: 0.75rem; padding: 5px 10px; border-radius: 8px; }

        /* EMPTY STATE */
        .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
        .empty-state i { font-size: 3.5rem; margin-bottom: 16px; display: block; opacity: 0.4; }
        .empty-state h5 { font-weight: 700; color: #374151; margin-bottom: 8px; }

        /* ALERT */
        .custom-alert { border: none; border-radius: 12px; padding: 14px 20px; font-weight: 500; display: flex; align-items: center; gap: 10px; }

        /* FOOTER */
        .footer { text-align: center; padding: 30px 0; color: #9ca3af; font-size: 0.85rem; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-shop"></i>Inventaris Warung Kelontong Bu Tutik</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: rgba(255,255,255,0.3);">
            <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item"><a class="nav-link nav-link-custom active" href="index.php"><i class="bi bi-house-door me-1"></i>Beranda</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="tambah.php"><i class="bi bi-plus-circle me-1"></i>Tambah Barang</a></li>
            </ul>
            <!-- Info user yang sedang login -->
            <div class="user-info">
                <i class="bi bi-person-circle" style="font-size: 1.3rem;"></i>
                <span><?= htmlspecialchars($_SESSION['username']); ?></span>
                <!-- INHERITANCE — Menampilkan role dari OBJECT $user (Admin/Kasir) -->
                <span class="user-badge"><?= htmlspecialchars($_SESSION['role']); ?></span>
                <a href="logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i>Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1><i class="bi bi-clipboard-data me-2"></i>Dashboard Inventaris</h1>
            <p>Kelola stok barang warung dengan mudah dan rapi</p>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="container">

    <!-- Statistik -->
    <div class="stats-container mb-4">
        <div class="row g-3">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="bi bi-box-seam"></i></div>
                    <div class="stat-number"><?= $total_jenis; ?></div>
                    <div class="stat-label">Jenis Barang</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon purple"><i class="bi bi-stack"></i></div>
                    <div class="stat-number"><?= $total_unit; ?></div>
                    <div class="stat-label">Total Unit</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon green"><i class="bi bi-check-circle"></i></div>
                    <div class="stat-number"><?= $total_aman; ?></div>
                    <div class="stat-label">Stok Aman</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon red"><i class="bi bi-exclamation-triangle"></i></div>
                    <div class="stat-number"><?= $total_menipis; ?></div>
                    <div class="stat-label">Stok Menipis</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert -->
    <?php if ($pesan != ''): ?>
        <div class="alert custom-alert alert-<?= $tipe_pesan; ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-<?= ($tipe_pesan == 'success') ? 'check-circle-fill' : 'exclamation-circle-fill'; ?>"></i>
            <?= $pesan; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Tabel -->
    <div class="main-card mb-4">
        <div class="main-card-header">
            <h5><i class="bi bi-table me-2"></i>Daftar Stok Barang</h5>
            <div class="d-flex gap-3 align-items-center flex-wrap">
                <form action="index.php" method="GET" class="search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="cari" class="form-control" placeholder="Cari barang..." value="<?= htmlspecialchars($keyword); ?>">
                </form>
                <a href="tambah.php" class="btn-primary-gradient">
                    <i class="bi bi-plus-lg"></i>Tambah Barang
                </a>
            </div>
        </div>
        <div class="main-card-body">
            <?php if (count($daftar_barang) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Status Stok</th>
                                <th>Tanggal Masuk</th>
                                <th>Keterangan</th>
                                <th style="text-align:center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $nomor = 1; foreach ($daftar_barang as $barang): ?>
                            <tr>
                                <td><strong><?= $nomor++; ?></strong></td>
                                <td class="nama-barang"><?= htmlspecialchars($barang['nama_barang']); ?></td>
                                <td>
                                    <!-- Memanggil method dari OBJECT $manager -->
                                    <span class="badge <?= $manager->badgeKategori($barang['kategori']); ?>">
                                        <?= htmlspecialchars($barang['kategori']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= $barang['jumlah']; ?></strong> unit
                                    <small class="text-muted d-block">min. <?= $barang['stok_minimum']; ?></small>
                                </td>
                                <td>
                                    <!-- Memanggil method dari OBJECT $manager -->
                                    <span class="badge <?= $manager->badgeStok($barang['jumlah'], $barang['stok_minimum']); ?>">
                                        <?= $manager->labelStok($barang['jumlah'], $barang['stok_minimum']); ?>
                                    </span>
                                </td>
                                <!-- Memanggil method dari OBJECT $manager -->
                                <td><?= $manager->formatTanggal($barang['tanggal_masuk']); ?></td>
                                <td>
                                    <small class="text-muted">
                                        <?php
                                        $ket = $barang['keterangan'];
                                        echo strlen($ket) > 40
                                            ? htmlspecialchars(substr($ket, 0, 40)) . '...'
                                            : htmlspecialchars($ket);
                                        ?>
                                    </small>
                                </td>
                                <td style="text-align:center;">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <?php
                                        // ============================================================
                                        // INHERITANCE — Menggunakan method bisaEdit() dari OBJECT $user
                                        // Jika $user adalah Admin → bisaEdit() return true
                                        // Jika $user adalah Kasir → bisaEdit() return false
                                        // ============================================================
                                        if ($user->bisaEdit()): ?>
                                            <a href="edit.php?id=<?= $barang['id']; ?>" class="btn-action btn-edit" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        <?php else: ?>
                                            <!-- Kasir: tombol edit dinonaktifkan -->
                                            <span class="btn-action btn-disabled" title="Tidak ada akses">
                                                <i class="bi bi-pencil-square"></i>
                                            </span>
                                        <?php endif; ?>

                                        <?php
                                        // ============================================================
                                        // INHERITANCE — Menggunakan method bisaHapus() dari OBJECT $user
                                        // Jika $user adalah Admin → bisaHapus() return true
                                        // Jika $user adalah Kasir → bisaHapus() return false
                                        // ============================================================
                                        if ($user->bisaHapus()): ?>
                                            <a href="hapus.php?id=<?= $barang['id']; ?>" class="btn-action btn-delete" title="Hapus"
                                               onclick="return confirm('Yakin ingin menghapus barang ini?');">
                                                <i class="bi bi-trash3"></i>
                                            </a>
                                        <?php else: ?>
                                            <!-- Kasir: tombol hapus dinonaktifkan -->
                                            <span class="btn-action btn-disabled" title="Tidak ada akses">
                                                <i class="bi bi-trash3"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <h5>Belum Ada Data Barang</h5>
                    <p>Mulai tambahkan data stok barang warung Anda</p>
                    <a href="tambah.php" class="btn-primary-gradient mt-3">
                        <i class="bi bi-plus-lg"></i>Tambah Barang Pertama
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y'); ?> Inventaris Warung Kelontong &mdash; Sistem Manajemen Stok Barang</p>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
