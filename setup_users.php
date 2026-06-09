<?php
/**
 * SKRIP SETUP - Jalankan SEKALI untuk membuat tabel users dan data awal.
 * Akses via browser: http://localhost/UAS3/setup_users.php
 * Setelah berhasil, hapus file ini demi keamanan.
 */

$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'inventaris_warung';

$koneksi = mysqli_connect($host, $user, $pass, $dbname);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($koneksi, "utf8mb4");

// Buat tabel users jika belum ada
$sql_create = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if (mysqli_query($koneksi, $sql_create)) {
    echo "<p>✅ Tabel 'users' berhasil dibuat / sudah ada.</p>";
} else {
    echo "<p>❌ Gagal membuat tabel: " . mysqli_error($koneksi) . "</p>";
}

// Cek apakah data sudah ada
$cek = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users");
$row = mysqli_fetch_assoc($cek);

if ($row['total'] == 0) {
    // Hash password dengan benar menggunakan PHP
    $hash_admin = password_hash('admin123', PASSWORD_DEFAULT);
    $hash_kasir = password_hash('kasir123', PASSWORD_DEFAULT);

    $sql1 = "INSERT INTO users (username, password, role) VALUES ('admin', '$hash_admin', 'admin')";
    $sql2 = "INSERT INTO users (username, password, role) VALUES ('kasir', '$hash_kasir', 'kasir')";

    mysqli_query($koneksi, $sql1);
    mysqli_query($koneksi, $sql2);

    echo "<p>✅ Data user berhasil ditambahkan:</p>";
    echo "<ul>";
    echo "<li><strong>Admin</strong> — username: <code>admin</code>, password: <code>admin123</code></li>";
    echo "<li><strong>Kasir</strong> — username: <code>kasir</code>, password: <code>kasir123</code></li>";
    echo "</ul>";
} else {
    echo "<p>⚠️ Data user sudah ada ($row[total] user). Tidak ada perubahan.</p>";
}

echo "<br><p>🔗 <a href='login.php'>Ke Halaman Login</a></p>";
echo "<p style='color:red;'><strong>⚠️ HAPUS FILE INI (setup_users.php) SETELAH SELESAI!</strong></p>";

mysqli_close($koneksi);
?>
