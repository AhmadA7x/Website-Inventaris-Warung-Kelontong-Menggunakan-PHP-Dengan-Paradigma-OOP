<?php
// ============================================================
// FILE: login.php
// PENERAPAN KONSEP: OBJECT, CLASS, CONSTRUCTOR, ENCAPSULATION
// Halaman login untuk sistem autentikasi berbasis OOP
// ============================================================

session_start();

// Jika sudah login, langsung redirect ke halaman utama
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'Pengguna.php';

$error = '';

// Proses login saat form di-submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi!';
    } else {
        // OBJECT — Membuat object dari class Database menggunakan keyword 'new'
        // CONSTRUCTOR — Saat object dibuat, __construct() di class Database otomatis dipanggil
        $db = new Database();                    // OBJECT & CONSTRUCTOR — Instansiasi Database
        $koneksi = $db->getKoneksi();            // ENCAPSULATION — Mengakses koneksi via getter

        // OBJECT — Membuat object dari PARENT CLASS Pengguna
        $pengguna = new Pengguna($koneksi);      // OBJECT & CONSTRUCTOR — Instansiasi Pengguna

        // Memanggil method login() dari object Pengguna
        if ($pengguna->login($username, $password)) {
            // Login berhasil, redirect ke halaman utama
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventaris Warung Kelontong Bu Tutik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 440px;
        }
        .login-card {
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
            padding: 48px 40px;
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-icon {
            width: 80px; height: 80px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px auto;
            font-size: 2rem; color: #fff;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .login-title {
            text-align: center;
            font-weight: 800;
            font-size: 1.5rem;
            color: #1a1a2e;
            margin-bottom: 6px;
        }
        .login-subtitle {
            text-align: center;
            color: #9ca3af;
            font-size: 0.9rem;
            margin-bottom: 32px;
        }
        .form-label { font-weight: 600; font-size: 0.9rem; color: #374151; margin-bottom: 6px; }
        .form-control {
            border: 2px solid #e9ecef; border-radius: 12px;
            padding: 12px 16px; font-size: 0.95rem;
            transition: var(--transition);
        }
        .form-control:focus { border-color: #667eea; box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); }
        .input-group-text {
            background: #f8f9fa; border: 2px solid #e9ecef;
            border-right: none; border-radius: 12px 0 0 12px;
            color: #9ca3af;
        }
        .input-group .form-control {
            border-left: none; border-radius: 0 12px 12px 0;
        }
        .input-group:focus-within .input-group-text {
            border-color: #667eea; color: #667eea;
        }
        .btn-login {
            background: var(--primary-gradient);
            border: none; color: #fff; font-weight: 700;
            padding: 14px; border-radius: 12px; width: 100%;
            font-size: 1rem; transition: var(--transition);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: #fff;
        }
        .custom-alert {
            border: none; border-radius: 12px;
            padding: 14px 20px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            font-size: 0.9rem;
        }
        .demo-info {
            background: #f0f2f5; border-radius: 12px;
            padding: 16px 20px; margin-top: 24px;
            font-size: 0.82rem; color: #6b7280;
        }
        .demo-info strong { color: #374151; }
        .demo-info .badge {
            font-size: 0.7rem; font-weight: 600;
            padding: 3px 8px; border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="login-icon">
            <i class="bi bi-shop"></i>
        </div>
        <h1 class="login-title">Selamat Datang</h1>
        <p class="login-subtitle">Masuk ke Inventaris Warung Kelontong Bu Tutik</p>

        <?php if ($error != ''): ?>
            <div class="alert custom-alert alert-danger mb-3">
                <i class="bi bi-exclamation-circle-fill"></i><?= $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'logout_sukses'): ?>
            <div class="alert custom-alert alert-success mb-3">
                <i class="bi bi-check-circle-fill"></i>Anda berhasil logout!
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username"
                           value="<?= htmlspecialchars($_POST['username'] ?? ''); ?>" required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right"></i>Masuk
            </button>
        </form>


    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
