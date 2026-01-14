<?php

class Grooming_model
{
    private $db;
    private $table = 'grooming';

    public function __construct()
    {
        $this->db = new Koneksi();
    }

    public function createGroomingOrder($data)
    {
        $query = "INSERT INTO {$this->table} 
              (tempat_grooming, jumlah_kucing, tipe_grooming, harga_grooming, tanggal_grooming) 
              VALUES (?, ?, ?, ?, ?)";

        $this->db->prepare($query);
        $this->db->bind(
            'sisds', // s: tempat, i: jumlah, s: tipe_grooming, d: harga, s: tanggal
            $data['tempat_grooming'],
            $data['jumlah_kucing'],
            $data['tipe_grooming'], // Kolom baru
            $data['harga_grooming'],
            $data['tanggal_grooming']
        );

        $this->db->execute();

        if ($this->db->rowCount() > 0) {
            return $this->db->getKoneksi()->insert_id;
        } else {
            return false;
        }
    }
}
