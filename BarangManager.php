<?php
// ============================================================
// FILE: BarangManager.php
// PENERAPAN KONSEP: CLASS, CONSTRUCTOR, ENCAPSULATION
// Class ini mengelola seluruh logika bisnis untuk data barang (CRUD)
// ============================================================

class BarangManager
{
    // ENCAPSULATION — Property private hanya dapat diakses dari dalam class BarangManager
    private $koneksi;

    // CONSTRUCTOR — Dipanggil otomatis saat OBJECT BarangManager dibuat
    // Berfungsi menginisialisasi koneksi database
    public function __construct($koneksi)
    {
        $this->koneksi = $koneksi;
    }

    // METHOD PRIVATE — Membantu membersihkan input dari celah keamanan / SQL Injection
    private function bersihkanInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        return mysqli_real_escape_string($this->koneksi, $data);
    }

    // METHOD READ — Mengambil semua data barang dari database (mendukung fitur pencarian)
    public function ambilSemuaBarang($keyword = '')
    {
        if ($keyword != '') {
            $keyword = $this->bersihkanInput($keyword);
            $query = "SELECT * FROM barang 
                      WHERE nama_barang LIKE '%$keyword%' 
                         OR kategori LIKE '%$keyword%' 
                         OR keterangan LIKE '%$keyword%'
                      ORDER BY id DESC";
        } else {
            $query = "SELECT * FROM barang ORDER BY id DESC";
        }

        $result = mysqli_query($this->koneksi, $query);

        $data = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // METHOD READ BY ID — Mengambil satu data barang berdasarkan ID
    public function ambilBarangById($id)
    {
        $id = (int) $id;
        $query = "SELECT * FROM barang WHERE id = $id";
        $result = mysqli_query($this->koneksi, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }

    // METHOD CREATE — Menambahkan barang baru ke database
    public function tambahBarang($data)
    {
        $nama_barang   = $this->bersihkanInput($data['nama_barang']);
        $kategori      = $this->bersihkanInput($data['kategori']);
        $jumlah        = (int) $data['jumlah'];
        $stok_minimum  = (int) $data['stok_minimum'];
        $tanggal_masuk = $this->bersihkanInput($data['tanggal_masuk']);
        $keterangan    = $this->bersihkanInput($data['keterangan']);

        $query = "INSERT INTO barang (nama_barang, kategori, jumlah, stok_minimum, tanggal_masuk, keterangan)
                  VALUES ('$nama_barang', '$kategori', $jumlah, $stok_minimum, '$tanggal_masuk', '$keterangan')";

        return mysqli_query($this->koneksi, $query);
    }

    // METHOD UPDATE — Mengubah data barang yang sudah ada
    public function updateBarang($data)
    {
        $id            = (int) $data['id'];
        $nama_barang   = $this->bersihkanInput($data['nama_barang']);
        $kategori      = $this->bersihkanInput($data['kategori']);
        $jumlah        = (int) $data['jumlah'];
        $stok_minimum  = (int) $data['stok_minimum'];
        $tanggal_masuk = $this->bersihkanInput($data['tanggal_masuk']);
        $keterangan    = $this->bersihkanInput($data['keterangan']);

        $query = "UPDATE barang SET 
                    nama_barang = '$nama_barang',
                    kategori = '$kategori',
                    jumlah = $jumlah,
                    stok_minimum = $stok_minimum,
                    tanggal_masuk = '$tanggal_masuk',
                    keterangan = '$keterangan'
                  WHERE id = $id";

        return mysqli_query($this->koneksi, $query);
    }

    // METHOD DELETE — Menghapus data barang berdasarkan ID
    public function hapusBarang($id)
    {
        $id = (int) $id;
        $query = "DELETE FROM barang WHERE id = $id";
        return mysqli_query($this->koneksi, $query);
    }

    // METHOD STATISTIK — Menghitung total jenis barang (baris data)
    public function hitungBarang()
    {
        $query = "SELECT COUNT(*) as total FROM barang";
        $result = mysqli_query($this->koneksi, $query);
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total'];
    }

    // METHOD STATISTIK — Menghitung total unit keseluruhan stok barang
    public function hitungTotalUnit()
    {
        $query = "SELECT COALESCE(SUM(jumlah), 0) as total_unit FROM barang";
        $result = mysqli_query($this->koneksi, $query);
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total_unit'];
    }

    // METHOD STATISTIK — Menghitung jumlah barang yang stoknya aman (jumlah >= stok_minimum)
    public function hitungStokAman()
    {
        $query = "SELECT COUNT(*) as total FROM barang WHERE jumlah >= stok_minimum";
        $result = mysqli_query($this->koneksi, $query);
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total'];
    }

    // METHOD STATISTIK — Menghitung jumlah barang yang stoknya menipis (jumlah < stok_minimum)
    public function hitungStokMenipis()
    {
        $query = "SELECT COUNT(*) as total FROM barang WHERE jumlah < stok_minimum";
        $result = mysqli_query($this->koneksi, $query);
        $row = mysqli_fetch_assoc($result);
        return (int) $row['total'];
    }

    // METHOD FORMATTING — Memformat tanggal menjadi format Indonesia (d M Y)
    public function formatTanggal($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April',
            'Mei', 'Juni', 'Juli', 'Agustus',
            'September', 'Oktober', 'November', 'Desember'
        ];

        $pecah = explode('-', $tanggal);

        if (count($pecah) == 3) {
            $tgl = (int) $pecah[2];
            $bln = (int) $pecah[1];
            $thn = $pecah[0];
            return $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
        }

        return $tanggal;
    }

    // METHOD UI HELPER — Mengembalikan class badge Bootstrap berdasarkan jumlah stok
    public function badgeStok($jumlah, $stok_minimum)
    {
        if ($jumlah == 0) {
            return 'bg-danger';
        } elseif ($jumlah < $stok_minimum) {
            return 'bg-warning text-dark';
        } else {
            return 'bg-success';
        }
    }

    // METHOD UI HELPER — Mengembalikan label teks status stok
    public function labelStok($jumlah, $stok_minimum)
    {
        if ($jumlah == 0) {
            return 'Habis';
        } elseif ($jumlah < $stok_minimum) {
            return 'Menipis';
        } else {
            return 'Aman';
        }
    }

    // METHOD UI HELPER — Mengembalikan class badge Bootstrap berdasarkan kategori
    public function badgeKategori($kategori)
    {
        switch ($kategori) {
            case 'Minuman':
                return 'bg-primary';
            case 'Makanan Ringan':
                return 'bg-info text-dark';
            case 'Sembako':
                return 'bg-warning text-dark';
            case 'Rokok & Tembakau':
                return 'bg-secondary';
            case 'Kebersihan & Perawatan':
                return 'bg-success';
            case 'Lainnya':
                return 'bg-dark';
            default:
                return 'bg-secondary';
        }
    }
}
?>
