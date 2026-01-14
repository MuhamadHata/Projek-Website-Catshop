<?php // app/models/Penitipan_model.php

class Penitipan_model {
    private $db; 
    private $table = 'penitipan'; 

    public function __construct() {
        $this->db = new Koneksi(); 
    }

    /**
     * Menyimpan data pesanan penitipan baru ke database.
     * @param array $data Data pesanan penitipan yang akan disimpan.
     * Expected keys: tanggal_penitipan, lama_penitipan_hari, jumlah_kucing,
     * nama_obat_harian, keterangan_penggunaan_obat, jumlah_kucing_diberi_obat, harga_penitipan
     * @return int|false ID pesanan yang baru dibuat jika berhasil, atau false jika gagal.
     */
    public function createPenitipanOrder($data) {
        // Query INSERT tanpa kolom `menggunakan_obat_harian`
        $query = "INSERT INTO {$this->table} 
                  (tanggal_penitipan, lama_penitipan_hari, jumlah_kucing, 
                   nama_obat_harian, keterangan_penggunaan_obat, jumlah_kucing_diberi_obat, 
                   harga_penitipan) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)"; // 7 placeholders
        
        $this->db->prepare($query);
        
        // Tipe data untuk bind_param:
        // tanggal_penitipan (s - string datetime YYYY-MM-DD HH:MM:SS)
        // lama_penitipan_hari (i - integer)
        // jumlah_kucing (i - integer)
        // nama_obat_harian (s - string, bisa null -> kirim null PHP)
        // keterangan_penggunaan_obat (s - string, bisa null -> kirim null PHP)
        // jumlah_kucing_diberi_obat (i - integer, 0 jika tidak ada obat)
        // harga_penitipan (d - double/decimal)
        $this->db->bind(
            'siisssd', // String tipe disesuaikan (7 parameter)
            $data['tanggal_penitipan'],
            $data['lama_penitipan_hari'],
            $data['jumlah_kucing'],
            $data['nama_obat_harian'],          // Kirim null jika memang null
            $data['keterangan_penggunaan_obat'], // Kirim null jika memang null
            $data['jumlah_kucing_diberi_obat'], // Kirim 0 jika tidak ada
            $data['harga_penitipan']
        );
        
        $this->db->execute(); 
        
        if ($this->db->rowCount() > 0) {
            return $this->db->getKoneksi()->insert_id; // Mengambil ID terakhir yang di-insert
        } else {
            // error_log("SQL Error Penitipan Model: " . $this->db->getKoneksi()->error); // Untuk debug
            // error_log("Data to Penitipan Model: " . print_r($data, true)); // Untuk debug
            return false;
        }
    }
}