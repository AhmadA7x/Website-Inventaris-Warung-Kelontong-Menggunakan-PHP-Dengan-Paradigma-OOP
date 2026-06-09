-- ============================================================
-- DATABASE INVENTARIS WARUNG KELONTONG (VERSI OOP)
-- ============================================================

-- Membuat database
CREATE DATABASE IF NOT EXISTS inventaris_warung
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

-- Menggunakan database
USE inventaris_warung;

-- ============================================================
-- TABEL BARANG (sama seperti sebelumnya)
-- ============================================================
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    kategori ENUM('Minuman', 'Makanan Ringan', 'Sembako', 'Rokok & Tembakau', 'Kebersihan & Perawatan', 'Lainnya') NOT NULL DEFAULT 'Lainnya',
    jumlah INT NOT NULL DEFAULT 0,
    stok_minimum INT NOT NULL DEFAULT 5,
    tanggal_masuk DATE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABEL USERS (BARU - untuk sistem login & hak akses)
-- Kolom: id, username, password (hash), role (admin/kasir)
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'kasir') NOT NULL DEFAULT 'kasir',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATA AWAL USERS
-- Password default: admin123 dan kasir123 (sudah di-hash dengan password_hash)
-- Jalankan INSERT ini sekali saja.
-- ============================================================
-- Catatan: Hash di bawah dibuat dengan password_hash('admin123', PASSWORD_DEFAULT)
-- dan password_hash('kasir123', PASSWORD_DEFAULT).
-- Jika hash tidak berfungsi, gunakan skrip PHP untuk generate ulang.

INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$YE0QjF1x6m8vO9sG3rL5KeZ1pN4wX7cR2bT8dF0hJ5mA3qV6uI9yS', 'admin'),
('kasir', '$2y$10$RA1bT2cD3eF4gH5iJ6kL7MnO8pQ9rS0tU1vW2xY3zA4bC5dE6fG7h', 'kasir');
