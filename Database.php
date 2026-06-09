<?php
// ============================================================
// FILE: Database.php
// PENERAPAN KONSEP: CLASS & CONSTRUCTOR (__construct)
// ============================================================

// CLASS — Mendefinisikan class Database sebagai blueprint koneksi database
class Database
{
    // ENCAPSULATION — Property private hanya bisa diakses dari dalam class ini
    private $host   = 'localhost';     // ENCAPSULATION — Host database (private)
    private $user   = 'root';          // ENCAPSULATION — Username database (private)
    private $pass   = '';              // ENCAPSULATION — Password database (private)
    private $dbname = 'inventaris_warung'; // ENCAPSULATION — Nama database (private)
    private $koneksi;                  // ENCAPSULATION — Objek koneksi MySQLi (private)

    // CONSTRUCTOR — Magic method __construct() dipanggil otomatis saat OBJECT dibuat
    // Fungsi: Menginisialisasi koneksi database secara otomatis
    public function __construct()
    {
        // Membuat koneksi MySQLi di dalam CONSTRUCTOR
        $this->koneksi = mysqli_connect(
            $this->host,
            $this->user,
            $this->pass,
            $this->dbname
        );

        // Cek apakah koneksi berhasil
        if (!$this->koneksi) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }

        // Set charset untuk mendukung karakter Indonesia
        mysqli_set_charset($this->koneksi, "utf8mb4");
    }

    // ENCAPSULATION — Getter method untuk mengakses property private $koneksi
    // Method public ini menjadi satu-satunya cara mendapatkan objek koneksi dari luar class
    public function getKoneksi()
    {
        return $this->koneksi;
    }

    // DESTRUCTOR — Magic method __destruct() dipanggil otomatis saat OBJECT dihancurkan
    // Fungsi: Menutup koneksi database secara otomatis saat objek tidak dipakai lagi
    public function __destruct()
    {
        if ($this->koneksi) {
            mysqli_close($this->koneksi);
        }
    }
}
