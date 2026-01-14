<?php // app/controllers/Penitipan.php

class Penitipan extends Controller
{
    // Definisikan tarif dasar di controller
    private $tarif_penitipan_per_hari_per_kucing = 30000;
    private $tarif_obat_per_hari_per_kucing = 5000;

    public function index()
    {
        if (isset($_SESSION['user_id'])) {
            $data['cart_item_count'] = $this->model('Keranjang_model')->getTotalCartQuantity($_SESSION['user_id']);
        } else {
            $data['cart_item_count'] = 0; // Atur ke 0 jika tidak ada user_id
            Flasher::setFlash('Anda harus login untuk mengakses penitipan.', 'error');
            header('Location: ' . BASEURL . 'auth/login');
            exit;
        }

        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            $message = '';
            $type = 'info';
            switch ($status) {
                case 'cancelled':
                    $message = 'Pembayaran dibatalkan oleh pengguna.';
                    $type = 'warning';
                    break;
                case 'failed':
                    $message = 'Pembayaran gagal dari Midtrans. Silakan coba lagi.';
                    $type = 'error';
                    break;
            }
            if (!empty($message)) {
                Flasher::setFlash($message, $type);
            }
            header('Location: ' . BASEURL . 'penitipan'); // Redirect ke URL bersih
            exit;
        }

        $data['page_title'] = 'Pesan Layanan Penitipan';
        // Kirim tarif ke view agar bisa digunakan oleh JavaScript untuk kalkulasi awal
        $data['tarif_penitipan_harian'] = $this->tarif_penitipan_per_hari_per_kucing;
        $data['tarif_obat_harian'] = $this->tarif_obat_per_hari_per_kucing;

        $this->view('templates/header', $data);
        $this->view('penitipan/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * METHOD BARU 1: Menerima detail pesanan, validasi, dan meminta token Midtrans
     */
    public function requestMidtransToken()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Anda harus login untuk memesan.']);
            exit;
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        // --- VALIDASI & KALKULASI ULANG HARGA DI SERVER (SANGAT PENTING!) ---
        $lama_hari = filter_var($input['lama_penitipan_hari'] ?? 0, FILTER_VALIDATE_INT);
        $jumlah_kucing = filter_var($input['jumlah_kucing_total'] ?? 0, FILTER_VALIDATE_INT);
        $nama_obat = trim($input['nama_obat_harian'] ?? '');
        $layanan_obat_aktif = !empty($nama_obat);
        $jumlah_kucing_obat = $layanan_obat_aktif ? filter_var($input['jumlah_kucing_diberi_obat'] ?? 0, FILTER_VALIDATE_INT) : 0;

        if ($lama_hari <= 0 || $jumlah_kucing <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data tidak valid.']);
            exit;
        }

        $harga_dasar = $this->tarif_penitipan_per_hari_per_kucing * $lama_hari * $jumlah_kucing;
        $harga_obat = $layanan_obat_aktif ? ($this->tarif_obat_per_hari_per_kucing * $jumlah_kucing_obat * $lama_hari) : 0;
        $total_harga = $harga_dasar + $harga_obat;

        // --- SIAPKAN DATA UNTUK MIDTRANS ---
        \Midtrans\Config::$serverKey = 'SB-Mid-server-ZNFJQAs0K9hF-G6xNaBcBPiQ';
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $order_id = 'PNP-' . $_SESSION['user_id'] . '-' . time();
        $user_info = $this->model('Profile_model')->getUserById($_SESSION['user_id']);

        // Buat item_details yang deskriptif
        $item_details = [];
        $item_details[] = [
            'id' => 'PENITIPAN_DASAR',
            'price' => $harga_dasar,
            'quantity' => 1,
            'name' => 'Layanan Penitipan (' . $jumlah_kucing . ' ekor x ' . $lama_hari . ' hari)'
        ];
        if ($layanan_obat_aktif) {
            $item_details[] = [
                'id' => 'OBAT_HARIAN',
                'price' => $harga_obat,
                'quantity' => 1,
                'name' => 'Obat Harian (' . $jumlah_kucing_obat . ' ekor x ' . $lama_hari . ' hari)'
            ];
        }

        $params = [
            'transaction_details' => ['order_id' => $order_id, 'gross_amount' => $total_harga],
            'item_details' => $item_details,
            'customer_details' => [
                'first_name' => $user_info ? $user_info['full_name'] : 'Guest',
                'email' => $user_info ? $user_info['email'] : 'guest@example.com',
                'phone' => $user_info ? $user_info['no_telepon'] : '08123456789'
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            echo json_encode(['success' => true, 'snapToken' => $snapToken]);
        } catch (Exception $e) {
            http_response_code(500);
            error_log("Midtrans Snap Token Error (Penitipan): " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * METHOD BARU 2: Menyimpan pesanan penitipan ke DB setelah pembayaran sukses
     */
    public function processOrderAfterPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit;
        }

        header('Content-Type: application/json');
        $payload = json_decode(file_get_contents('php://input'), true);

        $midtransResult = $payload['midtrans_result'] ?? null;
        $orderData = $payload['order_data'] ?? null;

        if (!$midtransResult || !$orderData || ($midtransResult['transaction_status'] != 'capture' && $midtransResult['transaction_status'] != 'settlement')) {
            echo json_encode(['success' => false, 'message' => 'Data pembayaran atau pesanan tidak valid.']);
            exit;
        }

        $id_user = $_SESSION['user_id'];

        $data_penitipan_to_db = [
            'tanggal_penitipan' => $orderData['tanggal_penitipan'] . ' 09:00:00',
            'lama_penitipan_hari' => $orderData['lama_penitipan_hari'],
            'jumlah_kucing' => $orderData['jumlah_kucing_total'],
            'nama_obat_harian' => !empty($orderData['nama_obat_harian']) ? $orderData['nama_obat_harian'] : null,
            'keterangan_penggunaan_obat' => !empty($orderData['keterangan_penggunaan_obat']) ? $orderData['keterangan_penggunaan_obat'] : null,
            'jumlah_kucing_diberi_obat' => !empty($orderData['nama_obat_harian']) ? $orderData['jumlah_kucing_diberi_obat'] : 0,
            'harga_penitipan' => $midtransResult['gross_amount'],
        ];

        $penitipanModel = $this->model('Penitipan_model');
        $penitipanId = $penitipanModel->createPenitipanOrder($data_penitipan_to_db);

        if ($penitipanId) {
            $transaksiModel = $this->model('Transaksi_model');

            $note_lines = [];
            $note_lines[] = "Rincian Pesanan Penitipan";
            $note_lines[] = "--------------------------------------";
            $note_lines[] = "Tanggal Mulai: " . date('d F Y', strtotime($orderData['tanggal_penitipan']));
            $note_lines[] = "Lama Penitipan: " . $orderData['lama_penitipan_hari'] . " hari";
            $note_lines[] = "Jumlah Kucing: " . $orderData['jumlah_kucing_total'] . " ekor";

            if (!empty($orderData['nama_obat_harian'])) {
                $note_lines[] = "";
                $note_lines[] = "Layanan Obat Harian: Ya";
                $note_lines[] = "Nama Obat: " . htmlspecialchars($orderData['nama_obat_harian']);
                $note_lines[] = "Keterangan: " . htmlspecialchars($orderData['keterangan_penggunaan_obat']);
                $note_lines[] = "Jumlah Kucing (Diberi Obat): " . $orderData['jumlah_kucing_diberi_obat'] . " ekor";
            } else {
                $note_lines[] = "Layanan Obat Harian: Tidak";
            }

            $note_lines[] = "--------------------------------------";
            $note_lines[] = "Total Harga: Rp " . number_format($midtransResult['gross_amount'], 0, ',', '.');
            $note_lines[] = "Metode Pembayaran: " . ucfirst(str_replace('_', ' ', $midtransResult['payment_type']));
            $note_lines[] = "Order ID Midtrans: " . $midtransResult['order_id'];
            $catatan_transaksi = implode("\n", $note_lines);

            $data_transaksi = [
                'ID_User' => $id_user,
                'ID_Grooming' => null,
                'ID_Pentipan' => $penitipanId,
                'total_harga' => $midtransResult['gross_amount'],
                'detail_transaksi_catatan' => $catatan_transaksi
            ];

            $transaksiId = $transaksiModel->createTransaksi($data_transaksi);

            if ($transaksiId) {
                Flasher::setFlash('Pesanan penitipan berhasil dibuat dan dibayar (ID: TSK00' . $transaksiId . '). Terima kasih!', 'success');
                echo json_encode(['success' => true, 'redirect_url' => BASEURL . 'profile/riwayatlayanan']);
                exit;
            }
        }

        Flasher::setFlash('Pembayaran berhasil, tetapi gagal menyimpan detail pesanan. Hubungi admin.', 'error');
        echo json_encode(['success' => false, 'redirect_url' => BASEURL . 'penitipan']);
        exit;
    }
}
