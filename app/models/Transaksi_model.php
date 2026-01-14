<?php // app/models/Transaksi_model.php

class Transaksi_model {
    private $db;
    private $table_transaksi = 'transaksi';
    private $table_transaksi_produk = 'transaksi_produk';

    public function __construct() {
        $this->db = new Koneksi(); // Asumsi Koneksi.php sudah ada dan berfungsi
    }

    /**
     * Membuat entri transaksi baru.
     * @param array $data Data untuk tabel transaksi.
     * Expected keys: ID_User, ID_Grooming (nullable), ID_Pentipan (nullable),
     * total_harga, detail_transaksi_catatan
     * @return int|false ID transaksi yang baru dibuat jika berhasil, atau false jika gagal.
     */
    public function createTransaksi($data) {
        // Perhatikan nama kolom ID_Pentipan di tabel transaksi sesuai schema SQL Anda
        $query = "INSERT INTO {$this->table_transaksi} 
                  (ID_User, ID_Grooming, ID_Pentipan, total_harga, detail_transaksi_catatan, tanggal_transaksi) 
                  VALUES (?, ?, ?, ?, ?, NOW())";
        
        $this->db->prepare($query);
        $this->db->bind(
            'iiids', // i: integer, d: double, s: string
            $data['ID_User'],
            $data['ID_Grooming'],    // Akan NULL jika bukan transaksi grooming
            $data['ID_Pentipan'],    // Akan NULL jika bukan transaksi penitipan (Perhatikan typo Pentipan vs Penitipan)
            $data['total_harga'],
            $data['detail_transaksi_catatan']
        );
        
        $this->db->execute();
        
        if ($this->db->rowCount() > 0) {
            return $this->db->getKoneksi()->insert_id; // Mengambil ID terakhir yang di-insert
        } else {
            // error_log("SQL Error Transaksi Model (createTransaksi): " . $this->db->getKoneksi()->error);
            // error_log("Data to Transaksi Model (createTransaksi): " . print_r($data, true));
            return false;
        }
    }

    /**
     * Menambahkan item produk ke transaksi.
     * @param array $data Data untuk tabel transaksi_produk.
     * Expected keys: ID_Transaksi, ID_Produk, jumlah_produk, harga_saat_transaksi
     * @return bool true jika berhasil, false jika gagal.
     */
    public function addProdukToTransaksi($data) {
        $query = "INSERT INTO {$this->table_transaksi_produk} 
                  (ID_Transaksi, ID_Produk, jumlah_produk, harga_saat_transaksi) 
                  VALUES (?, ?, ?, ?)";
        
        $this->db->prepare($query);
        $this->db->bind(
            'iiid', // i: integer, d: double
            $data['ID_Transaksi'],
            $data['ID_Produk'],
            $data['jumlah_produk'],
            $data['harga_saat_transaksi']
        );
        
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }

    // Anda bisa menambahkan method untuk mengambil riwayat transaksi di sini nanti
    // public function getTransaksiByUserId($userId) { ... }
    // public function getDetailTransaksi($transaksiId) { ... }
}