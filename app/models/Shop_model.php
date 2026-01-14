<?php

class Shop_model
{
    private $db;
    private $tabel = 'Produk';

    public function __construct()
    {
        $this->db = new Koneksi();
    }

    /**
     * Mengambil produk berdasarkan keyword pencarian dan/atau filter kategori.
     * Jika tidak ada keyword atau kategori, maka akan mengambil semua produk.
     * @param string|null $keyword Kata kunci untuk mencari di nama_produk atau detail_produk.
     * @param string|null $kategori Kategori produk untuk difilter.
     * @return array Daftar produk yang cocok.
     */
    public function getProduk($keyword = null, $kategori = null)
    {
        // Kolom yang akan di-SELECT
        $sql = "SELECT ID_Produk, nama_produk, detail_produk, stok_produk, gambar_produk, kategori_produk, harga_produk FROM " . $this->tabel;

        $conditions = []; // Array untuk menampung kondisi WHERE
        $params = [];     // Array untuk menampung parameter yang akan di-bind
        $types = '';      // String untuk tipe parameter yang akan di-bind

        // Tambahkan kondisi untuk keyword jika ada
        if (!empty($keyword)) {
            // Pencarian case-insensitive hanya pada nama_produk
            $conditions[] = 'LOWER(nama_produk) LIKE LOWER(?)';
            $keywordParam = "%" . trim($keyword) . "%"; // Trim keyword dan tambahkan wildcard
            $params[] = $keywordParam;
            $types .= 's'; // Satu parameter string
        }

        // Tambahkan kondisi untuk kategori jika ada dan bukan string kosong
        if (!empty($kategori)) {
            $conditions[] = 'kategori_produk = ?'; // Gunakan nama kolom yang benar
            $params[] = trim($kategori); // Trim kategori
            $types .= 's'; // Satu parameter string
        }

        // Gabungkan kondisi jika ada
        if (count($conditions) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY nama_produk ASC'; // Urutkan berdasarkan nama produk

        $this->db->prepare($sql);

        // Lakukan bind hanya jika ada parameter
        if (!empty($params) && !empty($types)) {
            $this->db->bind($types, ...$params);
        }

        return $this->db->resultset();
    }

    /**
     * Mengambil detail produk berdasarkan ID.
     * @param int $productId ID produk.
     * @return array|false Detail produk atau false jika tidak ditemukan.
     */
    public function getProductById($productId)
    {
        $query = "SELECT ID_Produk, nama_produk, detail_produk, stok_produk, gambar_produk, kategori_produk, harga_produk 
                  FROM " . $this->tabel . " WHERE ID_Produk = ?";
        $this->db->prepare($query);
        $this->db->bind("i", $productId);
        return $this->db->single(); // Mengembalikan satu baris produk atau false
    }

    /**
     * Mengambil beberapa produk yang dianggap populer.
     * @param int $limit Jumlah produk populer yang ingin ditampilkan.
     */
    public function getPopularProduk($limit = 3)
    {
        // Query untuk mengambil produk populer berdasarkan jumlah total yang dipesan
        // Kita akan JOIN tabel produk dengan transaksi_produk
        // Kemudian GROUP BY produk untuk menjumlahkan total pesanan
        // Dan ORDER BY total pesanan secara descending
        $sql = 'SELECT 
                p.ID_Produk, 
                p.nama_produk, 
                p.detail_produk, 
                p.gambar_produk, 
                p.harga_produk, 
                p.stok_produk,
                SUM(tp.jumlah_produk) AS total_terjual
            FROM 
                ' . $this->tabel . ' p 
            JOIN 
                transaksi_produk tp ON p.ID_Produk = tp.ID_Produk
            GROUP BY 
                p.ID_Produk, 
                p.nama_produk, 
                p.detail_produk, 
                p.gambar_produk, 
                p.harga_produk, 
                p.stok_produk  -- Semua kolom non-agregat dari produk harus ada di GROUP BY
            ORDER BY 
                total_terjual DESC
            LIMIT ?';

        $this->db->prepare($sql);
        $this->db->bind('i', $limit);
        return $this->db->resultset();
    }

    /**
     * Mengambil semua kategori produk yang unik untuk filter.
     * @return array Daftar kategori unik.
     */
    public function getAllKategori()
    {
        $this->db->prepare('SELECT DISTINCT kategori_produk FROM ' . $this->tabel . ' WHERE kategori_produk IS NOT NULL AND kategori_produk != "" ORDER BY kategori_produk ASC');
        $results = $this->db->resultset();
        $kategori_list = [];
        if ($results) {
            foreach ($results as $result) {
                $kategori_list[] = $result['kategori_produk'];
            }
        }
        return $kategori_list;
    }

    public function getProductStock($productId)
    {
        $query = "SELECT stok_produk FROM " . $this->tabel . " WHERE ID_Produk = ?"; // Menggunakan $this->tabel
        $this->db->prepare($query);
        $this->db->bind("i", $productId);
        $row = $this->db->single();
        return $row ? (int)$row['stok_produk'] : 0;
    }

    /**
     * Mengurangi stok produk.
     * @param int $productId ID produk.
     * @param int $quantityToDecrease Jumlah yang akan dikurangkan.
     * @return bool True jika berhasil, false jika gagal atau stok tidak mencukupi.
     */
    public function decreaseStock($productId, $quantityToDecrease)
    {
        // Opsional: Periksa dulu apakah stok mencukupi sebelum mencoba mengurangi
        // $currentStock = $this->getProductStock($productId);
        // if ($currentStock < $quantityToDecrease) {
        //     error_log("Attempt to decrease stock for product {$productId} by {$quantityToDecrease}, but stock is {$currentStock}");
        //     return false; 
        // }

        $query = "UPDATE " . $this->tabel . " SET stok_produk = stok_produk - ? 
                  WHERE ID_Produk = ? AND stok_produk >= ?";
        $this->db->prepare($query);
        // Bind quantity, product ID, and quantity again for the WHERE condition to prevent negative stock
        $this->db->bind("iii", $quantityToDecrease, $productId, $quantityToDecrease);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}
