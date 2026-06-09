<?php
// ============================================================
// FILE: Pengguna.php
// PENERAPAN KONSEP: ENCAPSULATION, INHERITANCE, CLASS, CONSTRUCTOR
// File ini berisi 3 class: Pengguna (parent), Admin (child), Kasir (child)
// ============================================================

require_once 'Database.php';

// ============================================================
// PARENT CLASS — Class Pengguna sebagai class induk (base class)
// ============================================================
class Pengguna
{
    // ENCAPSULATION — Property private hanya bisa diakses dari dalam class Pengguna
    private $username;  // ENCAPSULATION — Username disimpan secara private
    private $password;  // ENCAPSULATION — Password disimpan secara private

    // ENCAPSULATION — Property protected bisa diakses dari class ini DAN class turunannya
    protected $role;    // ENCAPSULATION — Role bisa diakses oleh child class (Admin & Kasir)

    // ENCAPSULATION — Property protected untuk menyimpan koneksi database
    protected $koneksi; // ENCAPSULATION — Koneksi DB bisa diakses oleh child class

    // CONSTRUCTOR — Dipanggil otomatis saat OBJECT Pengguna (atau turunannya) dibuat
    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi; // Simpan koneksi database ke property
    }

    // ============================================================
    // ENCAPSULATION — GETTER & SETTER METHODS
    // Metode publik untuk mengakses dan mengubah property private dari luar class
    // ============================================================

    // GETTER — Mengambil nilai property private $username
    public function getUsername()
    {
        return $this->username;
    }

    // SETTER — Mengubah nilai property private $username
    public function setUsername($username)
    {
        $this->username = $username;
    }

    // GETTER — Mengambil nilai property private $password
    public function getPassword()
    {
        return $this->password;
    }

    // SETTER — Mengubah nilai property private $password
    public function setPassword($password)
    {
        $this->password = $password;
    }

    // GETTER — Mengambil nilai property protected $role
    public function getRole()
    {
        return $this->role;
    }

    // SETTER — Mengubah nilai property protected $role
    public function setRole($role)
    {
        $this->role = $role;
    }

    // ============================================================
    // METHOD LOGIN — Memverifikasi username dan password dari database
    // ============================================================
    public function login($username, $password)
    {
        // Bersihkan input untuk keamanan
        $username = mysqli_real_escape_string($this->koneksi, $username);

        // Query untuk mencari user berdasarkan username
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($this->koneksi, $query);

        // Jika user ditemukan
        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verifikasi password yang di-hash dengan password_verify()
            if (password_verify($password, $user['password'])) {
                // Set property dengan data dari database
                $this->setUsername($user['username']); // ENCAPSULATION — Menggunakan setter
                $this->setRole($user['role']);          // ENCAPSULATION — Menggunakan setter

                // Simpan data user ke SESSION
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['role']      = $user['role'];

                return true; // Login berhasil
            }
        }

        return false; // Login gagal
    }

    // METHOD — Cek apakah user sudah login
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // METHOD — Cek apakah user memiliki akses tertentu
    // Method ini bisa di-override oleh child class (INHERITANCE)
    public function bisaHapus()
    {
        return false; // Default: tidak bisa hapus
    }

    // METHOD — Cek apakah user bisa mengedit
    public function bisaEdit()
    {
        return false; // Default: tidak bisa edit
    }
}

// ============================================================
// CHILD CLASS — Class Admin mewarisi (extends) class Pengguna
// INHERITANCE — Admin mendapatkan semua property & method dari Pengguna
// ============================================================
class Admin extends Pengguna // INHERITANCE — Kata kunci 'extends' menandakan pewarisan
{
    // CONSTRUCTOR — Memanggil CONSTRUCTOR parent class (Pengguna)
    public function __construct($koneksi)
    {
        parent::__construct($koneksi); // INHERITANCE — Memanggil constructor parent
        $this->role = 'admin';         // ENCAPSULATION — Mengakses property protected dari parent
    }

    // INHERITANCE — Override method dari parent class
    // Admin memiliki akses penuh, termasuk menghapus barang
    public function bisaHapus()
    {
        return true; // Admin BISA menghapus barang
    }

    // INHERITANCE — Override method dari parent class
    // Admin bisa mengedit barang
    public function bisaEdit()
    {
        return true; // Admin BISA mengedit barang
    }
}

// ============================================================
// CHILD CLASS — Class Kasir mewarisi (extends) class Pengguna
// INHERITANCE — Kasir mendapatkan semua property & method dari Pengguna
// ============================================================
class Kasir extends Pengguna // INHERITANCE — Kata kunci 'extends' menandakan pewarisan
{
    // CONSTRUCTOR — Memanggil CONSTRUCTOR parent class (Pengguna)
    public function __construct($koneksi)
    {
        parent::__construct($koneksi); // INHERITANCE — Memanggil constructor parent
        $this->role = 'kasir';         // ENCAPSULATION — Mengakses property protected dari parent
    }

    // INHERITANCE — Override method dari parent class
    // Kasir TIDAK memiliki akses hapus barang
    public function bisaHapus()
    {
        return false; // Kasir TIDAK BISA menghapus barang
    }

    // INHERITANCE — Override method dari parent class
    // Kasir TIDAK bisa mengedit barang
    public function bisaEdit()
    {
        return false; // Kasir TIDAK BISA mengedit barang
    }
}
