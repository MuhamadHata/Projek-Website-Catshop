<?php

class Home_model
{   
    private $db; 
    private $tabel = 'Produk';
    public function __construct() {
        $this->db = new Koneksi(); 
    }
    
    public function getPopularProduk($limit = 3) { // Sesuaikan limit jika perlu
        $this->db->prepare(
            // Pastikan detail_produk juga diambil jika akan ditampilkan di modal dari produk populer
            'SELECT ID_Produk, nama_produk, detail_produk, gambar_produk, harga_produk FROM ' . $this->tabel . 
            ' ORDER BY ID_Produk DESC LIMIT ?' // Contoh urutan, bisa diubah sesuai kriteria populer
        );
        $this->db->bind('i', $limit); 
        return $this->db->resultset(); 
    }
}
