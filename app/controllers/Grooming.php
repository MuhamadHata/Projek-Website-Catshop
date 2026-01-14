<?php // app/controllers/Grooming.php

class Grooming extends Controller
{
    private $definisi_paket_grooming_server = [
        'hemat' => ['nama_paket' => 'Paket Hemat', 'harga_satuan' => 50000, 'detail_layanan' => 'Mandi Standar'],
        'normal' => ['nama_paket' => 'Paket Normal', 'harga_satuan' => 80000, 'detail_layanan' => 'Mandi Standar, Potong Kuku, Pembersihan Telinga'],
        'sultan' => ['nama_paket' => 'Paket Sultan', 'harga_satuan' => 110000, 'detail_layanan' => 'Mandi Standar, Potong Kuku, Pembersihan Telinga, Serum Anti Kutu dan Jamur']
    ];
    private $biaya_home_service = 40000;

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            Flasher::setFlash('Anda harus login untuk mengakses halaman ini.', 'error');
            header('Location: ' . BASEURL . 'auth/login');
            exit;
        }

        // Logika untuk menangani notifikasi Flasher dari redirect
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
                    $message = 'Pembayaran gagal atau dibatalkan oleh pihak Midtrans.';
                    $type = 'error';
                    break;
                case 'error':
                    $message = 'Terjadi kesalahan teknis saat mencoba memulai sesi pembayaran.';
                    $type = 'error';
                    break;
                case 'order_failed':
                    $message = 'Pembayaran berhasil, namun terjadi kegagalan saat menyimpan pesanan Anda. Silakan hubungi admin.';
                    $type = 'error';
                    break;
            }
            if (!empty($message)) {
                Flasher::setFlash($message, $type);
            }
            header('Location: ' . BASEURL . 'grooming'); // Redirect ke URL bersih
            exit;
        }
        
        $data['cart_item_count'] = $this->model('Keranjang_model')->getTotalCartQuantity($_SESSION['user_id']);
        $data['page_title'] = 'Pesan Layanan Grooming';
        $data['paket_grooming_list'] = $this->definisi_paket_grooming_server;
        $data['biaya_home_service_js'] = $this->biaya_home_service;

        $this->view('templates/header', $data);
        $this->view('grooming/index', $data);
        $this->view('templates/footer', $data);
    }

    /**
     * METHOD BARU 1: Meminta Token Midtrans
     */
    public function requestMidtransToken()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            http_response_code(403); exit;
        }

        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        // Validasi & Kalkulasi Ulang Harga di Server
        $jumlah_kucing = filter_var($input['jumlah_kucing'] ?? 0, FILTER_VALIDATE_INT);
        $tipe_paket = $input['tipe_grooming'] ?? '';
        $tempat_grooming = $input['tempat_grooming'] ?? '';

        if ($jumlah_kucing <= 0 || empty($tipe_paket) || !isset($this->definisi_paket_grooming_server[$tipe_paket]) || !in_array($tempat_grooming, ['toko', 'rumah'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Data pesanan tidak valid.']);
            exit;
        }
        
        $paket_terpilih = $this->definisi_paket_grooming_server[$tipe_paket];
        $total_harga = $paket_terpilih['harga_satuan'] * $jumlah_kucing;
        if ($tempat_grooming === 'rumah') {
            $total_harga += $this->biaya_home_service;
        }

        // Siapkan Data untuk Midtrans
        \Midtrans\Config::$serverKey = 'SB-Mid-server-ZNFJQAs0K9hF-G6xNaBcBPiQ';
        \Midtrans\Config::$isProduction = false;
        
        $order_id = 'GRM-' . $_SESSION['user_id'] . '-' . time();
        $user_info = $this->model('Profile_model')->getUserById($_SESSION['user_id']);

        $item_details = [];
        $item_details[] = ['id' => 'GRM_' . strtoupper($tipe_paket), 'price' => $paket_terpilih['harga_satuan'] * $jumlah_kucing, 'quantity' => 1, 'name' => 'Grooming ' . $paket_terpilih['nama_paket'] . ' (' . $jumlah_kucing . ' ekor)'];
        if ($tempat_grooming === 'rumah') {
            $item_details[] = ['id' => 'HOME_SERVICE', 'price' => $this->biaya_home_service, 'quantity' => 1, 'name' => 'Biaya Home Service'];
        }

        $params = [
            'transaction_details' => ['order_id' => $order_id, 'gross_amount' => $total_harga],
            'item_details' => $item_details,
            'customer_details' => ['first_name' => $user_info['full_name'], 'email' => $user_info['email'], 'phone' => $user_info['no_telepon']],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            echo json_encode(['success' => true, 'snapToken' => $snapToken]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal membuat token pembayaran.']);
        }
        exit;
    }

    /**
     * METHOD BARU 2: Menyimpan pesanan ke DB setelah pembayaran
     */
    public function processOrderAfterPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            http_response_code(403); exit;
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

        $data_grooming_to_db = [
            'tempat_grooming' => $orderData['tempat_grooming'],
            'jumlah_kucing' => $orderData['jumlah_kucing'],
            'tipe_grooming' => $orderData['tipe_grooming'],
            'harga_grooming' => $midtransResult['gross_amount'],
            'tanggal_grooming' => $orderData['tanggal_grooming'],
        ];

        $groomingModel = $this->model('Grooming_model');
        $groomingId = $groomingModel->createGroomingOrder($data_grooming_to_db);

        if ($groomingId) {
            $transaksiModel = $this->model('Transaksi_model');
            $paket_terpilih = $this->definisi_paket_grooming_server[$orderData['tipe_grooming']];
            
            // --- Membuat Catatan Transaksi yang Rinci ---
            $note_lines = [];
            $note_lines[] = "Rincian Pesanan Grooming";
            $note_lines[] = "--------------------------------------";
            $note_lines[] = "Tanggal: " . date('d F Y', strtotime($orderData['tanggal_grooming']));
            $note_lines[] = "Lokasi: " . ucfirst($orderData['tempat_grooming']);
            $note_lines[] = "Jumlah Kucing: " . $orderData['jumlah_kucing'] . " ekor";
            $note_lines[] = "Paket: " . $paket_terpilih['nama_paket'];
            $note_lines[] = "Layanan: " . $paket_terpilih['detail_layanan'];
            if ($orderData['tempat_grooming'] === 'rumah') {
                $note_lines[] = "Biaya Home Service: Rp " . number_format($this->biaya_home_service, 0, ',', '.');
            }
            $note_lines[] = "--------------------------------------";
            $note_lines[] = "Total Harga: Rp " . number_format($midtransResult['gross_amount'], 0, ',', '.');
            $note_lines[] = "Metode Pembayaran: " . ucfirst(str_replace('_', ' ', $midtransResult['payment_type']));
            $note_lines[] = "Order ID Midtrans: " . $midtransResult['order_id'];
            $catatan_transaksi = implode("\n", $note_lines);
            // --- Akhir Catatan Transaksi ---

            $data_transaksi = [
                'ID_User' => $id_user, 'ID_Grooming' => $groomingId, 'ID_Pentipan' => null,
                'total_harga' => $midtransResult['gross_amount'], 'detail_transaksi_catatan' => $catatan_transaksi
            ];
            
            $transaksiId = $transaksiModel->createTransaksi($data_transaksi);

            if ($transaksiId) {
                Flasher::setFlash('Pesanan grooming berhasil dibuat dan dibayar (ID: TSK00'. $transaksiId .'). Terima kasih!', 'success');
                echo json_encode(['success' => true, 'redirect_url' => BASEURL . 'profile/riwayatlayanan']);
                exit;
            }
        }
        
        // Gagal menyimpan pesanan
        Flasher::setFlash('Pembayaran berhasil, tetapi gagal menyimpan pesanan. Hubungi admin.', 'error');
        echo json_encode(['success' => true, 'redirect_url' => BASEURL . 'grooming?status=order_failed']);
        exit;
    }
}