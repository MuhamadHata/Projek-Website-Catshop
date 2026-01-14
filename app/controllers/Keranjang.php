<?php

class Keranjang extends Controller
{
    private $keranjangModel;
    private $shopModel;
    private $transaksiModel;
    private $profileModel;

    public function __construct()
    {
        $this->keranjangModel = $this->model('Keranjang_model');
        $this->shopModel = $this->model('Shop_model');
        $this->transaksiModel = $this->model('Transaksi_model');
        $this->profileModel = $this->model('Profile_model');

        // Middleware untuk cek login, kecuali untuk halaman otentikasi
        if (!isset($_SESSION['user_id']) && !$this->isAuthPage()) {
            Flasher::setFlash('Anda harus login terlebih dahulu untuk mengakses halaman ini.', 'error');
            header('Location: ' . BASEURL . 'auth/login');
            exit;
        }
    }

    private function isAuthPage()
    {
        $currentUrl = $_GET['url'] ?? '';
        return (strpos($currentUrl, 'auth/') === 0);
    }

    public function index()
    {
        $data['page_title'] = 'Keranjang Belanja';
        $data['current_controller_name'] = 'Keranjang';
        $userId = $_SESSION['user_id'];

        $data['cart_item_count'] = $this->keranjangModel->getTotalCartQuantity($userId);
        $data['keranjang_items'] = $this->keranjangModel->getCartItemsWithStock($userId);

        if (isset($_SESSION['user_username'])) {
            $data['user_username'] = $_SESSION['user_username'];
        }

        // **PERBAIKAN:** Logika Flasher dipusatkan di sini, mencakup semua status.
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            $message = '';
            $type = 'info';
            switch ($status) {
                case 'cancelled':
                    $message = 'Anda menutup jendela pembayaran sebelum transaksi selesai.';
                    $type = 'warning';
                    break;
                case 'failed':
                    $message = 'Pembayaran gagal dari Midtrans. Silakan coba lagi.';
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
            header('Location: ' . BASEURL . 'keranjang'); // Redirect ke URL bersih
            exit;
        }


        $data['checkout_validated_produk_session'] = $_SESSION['checkout_validated_produk'] ?? false; 

        $this->view('templates/header', $data);
        $this->view('keranjang/index', $data); 
        $this->view('templates/footer', $data);
    }
    
    // ... Sisa fungsi (add, update, remove) tidak ada perubahan ...
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user_id'])) {
                Flasher::setFlash('Sesi berakhir, silakan login kembali.', 'error');
                header('Location: ' . BASEURL . 'auth/login');
                exit;
            }
            if (
                !isset($_POST['produk_id']) || !isset($_POST['jumlah']) ||
                !filter_var($_POST['produk_id'], FILTER_VALIDATE_INT) ||
                !filter_var($_POST['jumlah'], FILTER_VALIDATE_INT) ||
                (int)$_POST['jumlah'] <= 0
            ) {
                Flasher::setFlash('Data produk atau jumlah tidak valid.', 'error');
                $redirect_url = (isset($_POST['source_page']) && $_POST['source_page'] === 'shop') ? BASEURL . 'shop' : BASEURL . 'keranjang';
                header('Location: ' . $redirect_url);
                exit;
            }

            $data_post = [
                'user_id' => $_SESSION['user_id'],
                'produk_id' => (int)$_POST['produk_id'],
                'jumlah' => (int)$_POST['jumlah']
            ];
            $stok_produk = $this->shopModel->getProductStock($data_post['produk_id']);

            if ($stok_produk === null) { 
                Flasher::setFlash('Produk tidak ditemukan.', 'error');
            } elseif ($stok_produk < $data_post['jumlah']) {
                Flasher::setFlash('Stok produk tidak mencukupi (tersisa: ' . $stok_produk . ').', 'error');
            } else {
                if ($this->keranjangModel->addToKeranjang($data_post)) { 
                    Flasher::setFlash('Produk berhasil ditambahkan ke keranjang.', 'success');
                    unset($_SESSION['checkout_validated_produk']);
                } else {
                    Flasher::setFlash('Gagal menambahkan produk ke keranjang.', 'error');
                }
            }
            $redirect_url = BASEURL . 'keranjang';
            if (isset($_POST['source_page']) && $_POST['source_page'] === 'shop') {
                $redirect_url = BASEURL . 'shop';
                $queryParams = [];
                $active_kategori = $_POST['active_kategori'] ?? '';
                $active_keyword = $_POST['active_keyword'] ?? '';
                if (!empty($active_kategori)) {
                    $queryParams['kategori'] = $active_kategori;
                }
                if (!empty($active_keyword)) {
                    $queryParams['keyword'] = $active_keyword;
                }
                if (!empty($queryParams)) {
                    $redirect_url .= '?' . http_build_query($queryParams);
                }
            }
            header('Location: ' . $redirect_url);
            exit;
        }
        Flasher::setFlash('Metode request tidak diizinkan.', 'error');
        header('Location: ' . BASEURL . 'keranjang');
        exit;
    }


    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user_id'])) {
                Flasher::setFlash('Sesi berakhir, silakan login kembali.', 'error');
                header('Location: ' . BASEURL . 'auth/login');
                exit;
            }
            $userId = $_SESSION['user_id'];
            $produkId = $_POST['produk_id'];
            $requestedJumlah = (int)$_POST['jumlah'];
            $namaProdukHidden = $_POST['nama_produk_hidden'] ?? 'item ini';


            if (!filter_var($produkId, FILTER_VALIDATE_INT) || $requestedJumlah <= 0) {
                Flasher::setFlash('Data produk atau jumlah tidak valid.', 'error');
                header('Location: ' . BASEURL . 'keranjang');
                exit;
            }

            $currentStock = $this->shopModel->getProductStock($produkId);

            if ($currentStock === null) {
                Flasher::setFlash('Produk ' . htmlspecialchars($namaProdukHidden) . ' tidak ditemukan.', 'error');
            } elseif ($requestedJumlah > $currentStock) {
                $pesanError = 'Jumlah produk (' . htmlspecialchars($namaProdukHidden) . ') melebihi stok yang tersedia (Stok: ' . $currentStock . '). Keranjang tidak diperbarui.';
                Flasher::setFlash($pesanError, 'error');
            } else {
                $data = [
                    'user_id' => $userId,
                    'produk_id' => $produkId,
                    'jumlah' => $requestedJumlah
                ];
                if ($this->keranjangModel->updateKeranjang($data)) { 
                    Flasher::setFlash('Keranjang berhasil diperbarui.', 'success');
                    unset($_SESSION['checkout_validated_produk']); 
                } else {
                    $itemKeranjang = $this->keranjangModel->getCartItemsWithStock($userId); 
                    $jumlahSaatIniDiKeranjang = 0;
                    foreach ($itemKeranjang as $item) {
                        if ($item['produk_id'] == $produkId) {
                            $jumlahSaatIniDiKeranjang = $item['jumlah'];
                            break;
                        }
                    }
                    if ($jumlahSaatIniDiKeranjang != $requestedJumlah) {
                        Flasher::setFlash('Gagal memperbarui keranjang atau tidak ada perubahan.', 'warning');
                    }
                }
            }
            header('Location: ' . BASEURL . 'keranjang');
            exit;
        }
        Flasher::setFlash('Metode request tidak diizinkan.', 'error');
        header('Location: ' . BASEURL . 'keranjang');
        exit;
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user_id'])) {
                Flasher::setFlash('Sesi tidak valid atau berakhir.', 'error');
                header('Location: ' . BASEURL . 'auth/login');
                exit;
            }
            $userId = $_SESSION['user_id'];
            if (!isset($_POST['produk_id']) || !filter_var($_POST['produk_id'], FILTER_VALIDATE_INT)) {
                Flasher::setFlash('ID Produk tidak valid untuk dihapus.', 'error');
                header('Location: ' . BASEURL . 'keranjang');
                exit;
            }
            $produkId = (int)$_POST['produk_id'];

            if ($this->keranjangModel->removeFromKeranjang($userId, $produkId)) { 
                Flasher::setFlash('Produk berhasil dihapus dari keranjang.', 'success');
                unset($_SESSION['checkout_validated_produk']);
                if ($this->keranjangModel->getTotalCartQuantity($userId) == 0) { 
                    $_SESSION['checkout_validated_produk'] = false;
                }
            } else {
                Flasher::setFlash('Gagal menghapus produk dari keranjang.', 'error');
            }
            header('Location: ' . BASEURL . 'keranjang');
            exit;
        }
        Flasher::setFlash('Metode request tidak diizinkan.', 'error');
        header('Location: ' . BASEURL . 'keranjang');
        exit;
    }

    public function requestMidtransToken()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Anda harus login untuk melanjutkan.']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        header('Content-Type: application/json');

        $validationErrors = $this->keranjangModel->validateCartStock($userId);
        if (!empty($validationErrors)) {
            $errorMessages = [];
            foreach ($validationErrors as $err) {
                $errorMessages[] = htmlspecialchars($err['nama_produk']) . ' (diminta: ' . $err['diminta'] . ', tersedia: ' . $err['tersedia'] . ')';
            }
            $fullErrorMessage = 'Stok tidak mencukupi untuk: ' . implode(', ', $errorMessages);

            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => $fullErrorMessage]);
            exit;
        }

        $itemKeranjang = $this->keranjangModel->getCartItemsWithStock($userId);
        if (empty($itemKeranjang)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Keranjang Anda kosong.']);
            exit;
        }

        \Midtrans\Config::$serverKey = 'SB-Mid-server-ZNFJQAs0K9hF-G6xNaBcBPiQ';
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $transaction_details = [];
        $item_details = [];
        $gross_amount = 0;

        foreach ($itemKeranjang as $item) {
            $item_details[] = [
                'id'       => $item['produk_id'],
                'price'    => (int)$item['harga_produk'],
                'quantity' => (int)$item['jumlah'],
                'name'     => $item['nama_produk']
            ];
            $gross_amount += (int)$item['harga_produk'] * (int)$item['jumlah'];
        }

        $order_id = 'TSK-' . $userId . '-' . time();

        $transaction_details['order_id'] = $order_id;
        $transaction_details['gross_amount'] = $gross_amount;

        $user_info = $this->profileModel->getUserById($userId);

        $customer_details = [
            'first_name' => $user_info ? $user_info['full_name'] : ($_SESSION['user_username'] ?? 'Guest'),
            'last_name'  => '',
            'email'      => $user_info ? $user_info['email'] : 'guest@example.com',
            'phone'      => $user_info ? $user_info['no_telepon'] : '08123456789'
        ];

        $params = [
            'transaction_details' => $transaction_details,
            'item_details'        => $item_details,
            'customer_details'    => $customer_details,
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            echo json_encode(['success' => true, 'snapToken' => $snapToken, 'order_id' => $order_id]);
        } catch (Exception $e) {
            http_response_code(500);
            error_log("Midtrans Snap Token Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Gagal membuat token pembayaran: ' . $e->getMessage()]);
        }
        exit;
    }

    public function processOrderAfterPayment()
    {
        header('Content-Type: application/json');
        $midtransResult = json_decode(file_get_contents('php://input'), true);

        if (!$midtransResult || !isset($midtransResult['transaction_status'])) {
            echo json_encode(['success' => false, 'message' => 'Data pembayaran tidak valid.']);
            exit;
        }

        if ($midtransResult['transaction_status'] != 'capture' && $midtransResult['transaction_status'] != 'settlement') {
            echo json_encode(['success' => false, 'message' => 'Status pembayaran tidak berhasil.']);
            exit;
        }

        $id_user = $_SESSION['user_id'];
        $itemKeranjang = $this->keranjangModel->getCartItemsWithStock($id_user);

        if (empty($itemKeranjang)) {
            echo json_encode(['success' => false, 'message' => 'Keranjang Anda kosong.']);
            exit;
        }

        $finalServerTotalPrice = 0;
        $itemsForTransaction = [];
        foreach ($itemKeranjang as $cartItem) {
            $produkInfo = $this->shopModel->getProductById($cartItem['produk_id']);
            if (!$produkInfo || $cartItem['jumlah'] > $produkInfo['stok_produk']) {
                echo json_encode(['success' => false, 'message' => 'Stok untuk ' . htmlspecialchars($cartItem['nama_produk']) . ' baru saja habis. Pesanan dibatalkan.']);
                exit;
            }
            $finalServerTotalPrice += $produkInfo['harga_produk'] * $cartItem['jumlah'];

            $itemsForTransaction[] = [
                'produk_id' => $cartItem['produk_id'],
                'jumlah' => $cartItem['jumlah'],
                'harga_saat_transaksi' => $produkInfo['harga_produk'],
                'nama_produk' => $produkInfo['nama_produk']
            ];
        }

        $payment_method = $midtransResult['payment_type'] ?? 'Unknown';
        $note_lines = [];

        $note_lines[] = "Rincian Pesanan Produk:";
        $note_lines[] = "--------------------------------------";

        foreach ($itemsForTransaction as $item) {
            $subtotal_item = $item['jumlah'] * $item['harga_saat_transaksi'];
            $note_lines[] = "- " . htmlspecialchars($item['nama_produk'])
                . " (" . $item['jumlah'] . " x " . "Rp " . number_format($item['harga_saat_transaksi'], 0, ',', '.') . ")"
                . " = Rp " . number_format($subtotal_item, 0, ',', '.');
        }

        $note_lines[] = "--------------------------------------";
        $note_lines[] = "Total Harga: Rp " . number_format($finalServerTotalPrice, 0, ',', '.');
        $note_lines[] = "Metode Pembayaran: " . ucfirst(str_replace('_', ' ', $payment_method));
        $note_lines[] = "Order ID Midtrans: " . $midtransResult['order_id'];

        $catatan_transaksi_produk = implode("\n", $note_lines);

        $data_transaksi_utama = [
            'ID_User' => $id_user,
            'ID_Grooming' => null,
            'ID_Pentipan' => null,
            'total_harga' => $finalServerTotalPrice,
            'detail_transaksi_catatan' => $catatan_transaksi_produk
        ];

        $transaksiId = $this->transaksiModel->createTransaksi($data_transaksi_utama);
        if (!$transaksiId) {
            echo json_encode(['success' => false, 'message' => 'Gagal membuat data transaksi utama.']);
            exit;
        }

        foreach ($itemsForTransaction as $item_tx_detail) {
            $data_produk_transaksi = [
                'ID_Transaksi' => $transaksiId,
                'ID_Produk' => $item_tx_detail['produk_id'],
                'jumlah_produk' => $item_tx_detail['jumlah'],
                'harga_saat_transaksi' => $item_tx_detail['harga_saat_transaksi']
            ];

            if (!$this->transaksiModel->addProdukToTransaksi($data_produk_transaksi) || !$this->shopModel->decreaseStock($item_tx_detail['produk_id'], $item_tx_detail['jumlah'])) {
                error_log("CRITICAL: Failed processing item for Transaksi ID " . $transaksiId);
                echo json_encode(['success' => false, 'message' => 'Gagal memproses detail pesanan. Transaksi dibatalkan.']);
                exit;
            }
        }

        $this->keranjangModel->clearCartByUserId($id_user);

        $successMessage = 'Pesanan Anda (ID: TSK00' . $transaksiId . ') telah berhasil dibuat! Terima kasih telah berbelanja.';
        Flasher::setFlash($successMessage, 'success');

        echo json_encode([
            'success' => true,
            'message' => 'Pesanan Anda (ID: TSK00' . $transaksiId . ') telah berhasil dibuat! Terima kasih telah berbelanja.',
            'redirect_url' => BASEURL . 'profile/riwayatpesanan'
        ]);
        exit;
    }
}