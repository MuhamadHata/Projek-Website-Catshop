<?php

class Keranjang_model
{
    private $db; // Instance dari kelas Koneksi
    private $tabel = 'keranjang';

    public function __construct()
    {
        $this->db = new Koneksi();
    }

    public function getKeranjangItems($userId)
    {
        $query = "SELECT k.produk_id, k.jumlah, k.user_id, p.nama_produk, p.harga_produk, p.gambar_produk 
                  FROM " . $this->tabel . " k
                  JOIN produk p ON k.produk_id = p.ID_Produk 
                  WHERE k.user_id = ?";
        $this->db->prepare($query);
        $this->db->bind("i", $userId);
        return $this->db->resultset();
    }

    public function addToKeranjang($data)
    {
        $queryCek = "SELECT jumlah FROM " . $this->tabel . " WHERE user_id = ? AND produk_id = ?";
        $this->db->prepare($queryCek);
        $this->db->bind("ii", $data['user_id'], $data['produk_id']);
        $existingItem = $this->db->single();

        if ($existingItem) {
            $newJumlah = $existingItem['jumlah'] + $data['jumlah'];
            return $this->updateKeranjang([
                'user_id' => $data['user_id'],
                'produk_id' => $data['produk_id'],
                'jumlah' => $newJumlah
            ]);
        } else {
            $query = "INSERT INTO " . $this->tabel . " (user_id, produk_id, jumlah) VALUES (?, ?, ?)";
            $this->db->prepare($query);
            $this->db->bind("iii", $data['user_id'], $data['produk_id'], $data['jumlah']);
            $this->db->execute();
            return $this->db->rowCount() > 0;
        }
    }

    public function updateKeranjang($data)
    {
        $query = "UPDATE " . $this->tabel . " SET jumlah = ? WHERE user_id = ? AND produk_id = ?";
        $this->db->prepare($query);
        $this->db->bind("iii", $data['jumlah'], $data['user_id'], $data['produk_id']);
        $this->db->execute();
        // Untuk UPDATE, rowCount() bisa 0 jika tidak ada baris yang cocok atau nilainya sama.
        // Mengembalikan true jika tidak ada error sudah cukup, atau >= 0 jika ingin memastikan query jalan.
        // Namun, > 0 lebih ketat untuk memastikan ada perubahan, atau periksa error jika model DB mendukung.
        // Jika diasumsikan bahwa jika tidak ada error maka berhasil, bisa return true jika execute tidak throw exception.
        // Namun, untuk konsistensi dengan add dan remove, kita bisa biarkan seperti ini, 
        // dengan catatan bahwa tidak adanya perubahan (misal update jumlah ke nilai yang sama) akan return false.
        return $this->db->rowCount() >= 0; // Atau > 0 jika hanya ingin true jika ada perubahan
    }

    public function removeFromKeranjang($userId, $produkId)
    {
        $query = "DELETE FROM " . $this->tabel . " WHERE user_id = ? AND produk_id = ?";
        $this->db->prepare($query);
        $this->db->bind("ii", $userId, $produkId);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function getTotalCartQuantity($userId)
    {
        $query = "SELECT SUM(jumlah) as total_quantity FROM " . $this->tabel . " WHERE user_id = ?";
        $this->db->prepare($query);
        $this->db->bind("i", $userId);
        $result = $this->db->single();
        return $result ? (int)$result['total_quantity'] : 0;
    }

    public function getCartItemsWithStock($userId)
    {
        $query = "SELECT k.produk_id, k.jumlah, k.user_id, 
                     p.nama_produk, p.harga_produk, p.gambar_produk, p.stok_produk 
              FROM " . $this->tabel . " k
              JOIN produk p ON k.produk_id = p.ID_Produk 
              WHERE k.user_id = ?";
        $this->db->prepare($query);
        $this->db->bind("i", $userId);
        return $this->db->resultset();
    }

    public function validateCartStock($userId)
    {
        $itemsWithStock = $this->getCartItemsWithStock($userId);
        $errors = [];
        if (empty($itemsWithStock)) {
            return $errors;
        }
        foreach ($itemsWithStock as $item) {
            if ($item['jumlah'] > $item['stok_produk']) {
                $errors[] = [
                    'nama_produk' => $item['nama_produk'],
                    'diminta' => $item['jumlah'],
                    'tersedia' => $item['stok_produk']
                ];
            }
        }
        return $errors;
    }

    /**
     * Menghapus semua item dari keranjang milik user tertentu.
     * Digunakan setelah checkout berhasil.
     */
    public function clearCartByUserId($userId)
    {
        $query = "DELETE FROM " . $this->tabel . " WHERE user_id = ?";
        $this->db->prepare($query);
        $this->db->bind("i", $userId);
        $this->db->execute();
        return $this->db->rowCount() > 0; 
    }
}
// Pastikan tidak ada kurung kurawal } penutup tambahan di bawah ini.
// File Keranjang_model.php Anda sebelumnya memiliki satu } ekstra di akhir.