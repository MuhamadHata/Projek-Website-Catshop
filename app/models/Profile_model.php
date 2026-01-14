<?php

class Profile_model
{
    private $db; // Instance dari Koneksi
    private $tableUser = 'user';
    private $tableTransaksi = 'transaksi';
    private $tableTransaksiProduk = 'transaksi_produk';
    private $tableProduk = 'produk';
    private $tableGrooming = 'grooming';
    private $tablePenitipan = 'penitipan';
    private $tableKeranjang = 'keranjang';

    public function __construct()
    {
        $this->db = new Koneksi();
    }

    public function getUserById($id)
    {
        // Tambahkan full_name ke SELECT
        $this->db->prepare('SELECT ID_User, username, full_name, email, alamat, no_telepon FROM ' . $this->tableUser . ' WHERE ID_User = ?');
        $this->db->bind('i', $id);
        return $this->db->single();
    }

    public function updateUser($data)
    {
        // Tambahkan full_name ke UPDATE
        // Sesuaikan tipe data binding untuk no_telepon menjadi 'i' (integer)
        $query = "UPDATE " . $this->tableUser . " SET 
                    username = ?, 
                    full_name = ?,
                    email = ?, 
                    alamat = ?, 
                    no_telepon = ?
                  WHERE ID_User = ?";
        
        $this->db->prepare($query);
        // urutan bind: username, full_name, email, alamat, no_telepon, ID_User
        // tipe bind: s, s, s, s, i, i (string, string, string, string, integer, integer)
        $this->db->bind('ssssii', 
            $data['username'], 
            $data['full_name'],
            $data['email'], 
            $data['alamat'], 
            $data['no_telepon'], 
            $data['ID_User']
        );
        
        $this->db->execute();
        return $this->db->rowCount();
    }

    // Fungsi untuk memeriksa apakah email sudah digunakan oleh user lain (untuk validasi)
    public function isEmailTakenByOtherUser($email, $currentUserId)
    {
        $this->db->prepare('SELECT ID_User FROM ' . $this->tableUser . ' WHERE email = ? AND ID_User != ?');
        $this->db->bind('si', $email, $currentUserId);
        $result = $this->db->single();
        return $result !== null;
    }

    // Fungsi untuk memeriksa apakah username sudah digunakan oleh user lain (untuk validasi)
    public function isUsernameTakenByOtherUser($username, $currentUserId)
    {
        $this->db->prepare('SELECT ID_User FROM ' . $this->tableUser . ' WHERE username = ? AND ID_User != ?');
        $this->db->bind('si', $username, $currentUserId);
        $result = $this->db->single();
        return $result !== null;
    }


    public function getProductHistory($userId)
    {
        $query = "SELECT t.ID_Transaksi, t.total_harga, t.detail_transaksi_catatan, t.tanggal_transaksi
                  FROM " . $this->tableTransaksi . " t
                  WHERE t.ID_User = ? 
                  AND t.ID_Grooming IS NULL 
                  AND t.ID_Pentipan IS NULL
                  ORDER BY t.tanggal_transaksi DESC";
        $this->db->prepare($query);
        $this->db->bind('i', $userId);
        return $this->db->resultset();
    }

    public function getGroomingHistory($userId)
    {
        $query = "SELECT t.ID_Transaksi, t.total_harga, t.detail_transaksi_catatan, t.tanggal_transaksi,
                         g.ID_Grooming, g.tempat_grooming, g.jumlah_kucing, g.tipe_grooming, g.harga_grooming, g.tanggal_grooming
                  FROM " . $this->tableTransaksi . " t
                  JOIN " . $this->tableGrooming . " g ON t.ID_Grooming = g.ID_Grooming
                  WHERE t.ID_User = ?
                  ORDER BY g.tanggal_grooming DESC";
        $this->db->prepare($query);
        $this->db->bind('i', $userId);
        return $this->db->resultset();
    }

    public function getPenitipanHistory($userId)
    {
        $query = "SELECT t.ID_Transaksi, t.total_harga, t.detail_transaksi_catatan, t.tanggal_transaksi,
                         p.ID_Penitipan, p.lama_penitipan_hari, p.jumlah_kucing, p.nama_obat_harian, p.keterangan_penggunaan_obat, p.harga_penitipan, p.tanggal_penitipan
                  FROM " . $this->tableTransaksi . " t
                  JOIN " . $this->tablePenitipan . " p ON t.ID_Pentipan = p.ID_Penitipan
                  WHERE t.ID_User = ?
                  ORDER BY p.tanggal_penitipan DESC";
        $this->db->prepare($query);
        $this->db->bind('i', $userId);
        return $this->db->resultset();
    }
    
    public function getCartItemCount($userId)
    {
        $this->db->prepare('SELECT SUM(jumlah) as total_items FROM ' . $this->tableKeranjang . ' WHERE user_id = ?');
        $this->db->bind('i', $userId);
        $result = $this->db->single();
        return $result['total_items'] ?? 0;
    }

    public function __destruct()
    {
        $this->db->closeKoneksi();
    }
}