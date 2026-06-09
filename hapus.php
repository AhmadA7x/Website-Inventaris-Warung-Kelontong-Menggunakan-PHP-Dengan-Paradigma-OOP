<?php
// ============================================================
// FILE: hapus.php (VERSI OOP)
// PENERAPAN KONSEP: OBJECT, CLASS, CONSTRUCTOR, INHERITANCE, ENCAPSULATION
// Skrip untuk menghapus barang (hanya Admin yang bisa akses)
// ============================================================

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Memuat file class yang dibutuhkan
require_once 'Database.php';
require_once 'BarangManager.php';
require_once 'Pengguna.php';

// OBJECT & CONSTRUCTOR — Membuat object dari class Database
$db = new Database();                    // OBJECT & CONSTRUCTOR — Instansiasi Database
$koneksi = $db->getKoneksi();            // ENCAPSULATION — Mengakses koneksi via getter

// OBJECT & CONSTRUCTOR — Membuat object dari class BarangManager
$manager = new BarangManager($koneksi);  // OBJECT & CONSTRUCTOR — Instansiasi BarangManager

// ============================================================
// INHERITANCE — Membuat object sesuai role untuk cek hak akses
// Admin → bisaHapus() return true
// Kasir → bisaHapus() return false
// ============================================================
if ($_SESSION['role'] == 'admin') {
    $user = new Admin($koneksi);         // OBJECT & INHERITANCE — Instansiasi child class Admin
} else {
    $user = new Kasir($koneksi);         // OBJECT & INHERITANCE — Instansiasi child class Kasir
}

// INHERITANCE — Cek apakah user bisa hapus menggunakan method dari child class
if (!$user->bisaHapus()) {
    // Kasir tidak bisa menghapus, redirect dengan pesan error
    header('Location: index.php?pesan=akses_ditolak');
    exit;
}

// Proses hapus barang (hanya bisa dijalankan oleh Admin)
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    // Memanggil method dari OBJECT $manager
    $barang = $manager->ambilBarangById($id); // Memanggil method dari OBJECT

    if ($barang != null) {
        // Memanggil method hapusBarang() dari OBJECT $manager
        $hasil = $manager->hapusBarang($id); // Memanggil method dari OBJECT
        if ($hasil) {
            header('Location: index.php?pesan=hapus_sukses');
            exit;
        } else {
            header('Location: index.php?pesan=gagal');
            exit;
        }
    } else {
        header('Location: index.php?pesan=tidak_ditemukan');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
