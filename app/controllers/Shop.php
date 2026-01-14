<?php

class Shop extends Controller
{

    public function index()
    {
        $data = [];
        if (isset($_SESSION['user_id'])) {
            $data['user_id'] = $_SESSION['user_id'];
            $data['cart_item_count'] = $this->model('Keranjang_model')->getTotalCartQuantity($_SESSION['user_id']);
            if (isset($_SESSION['user_username'])) {
                $data['user_username'] = $_SESSION['user_username'];
            }
        } else {
            $data['cart_item_count'] = 0;
            Flasher::setFlash('Anda harus login untuk mengakses shop.', 'error');
            header('Location: ' . BASEURL . 'auth/login');
            exit;
        }

        $data['page_title'] = 'Shop';
        $data['current_controller_name'] = 'Shop';
        $shopModel = $this->model('Shop_model');

        // Tentukan keyword dan kategori yang sedang aktif
        $active_keyword = null;
        if (isset($_POST['keyword'])) { // Jika dari submit form filter
            $active_keyword = trim($_POST['keyword']);
        } elseif (isset($_GET['keyword'])) { // Jika dari redirect (misal setelah add to cart)
            $active_keyword = trim($_GET['keyword']);
        }

        $active_kategori = null;
        if (isset($_POST['kategori'])) { // Jika dari submit form filter
            $active_kategori = trim($_POST['kategori']);
        } elseif (isset($_GET['kategori'])) { // Jika dari redirect
            $active_kategori = trim($_GET['kategori']);
        }
        
        // Variabel ini akan dikirim ke view untuk mengisi ulang form filter
        // dan digunakan untuk query produk
        $data['current_keyword'] = $active_keyword;
        $data['current_kategori'] = $active_kategori;

        $data['produk_all'] = $shopModel->getProduk($active_keyword, $active_kategori);
        $data['produk_populer'] = $shopModel->getPopularProduk(3);
        $data['kategori_list'] = $shopModel->getAllKategori();

        // Untuk logika scroll di JavaScript jika request berasal dari filter atau ada parameter di URL
        $data['is_filtered_request'] = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['keyword']) || isset($_POST['kategori'])))
            || isset($_GET['kategori'])
            || isset($_GET['keyword']);

        $this->view('templates/header', $data);
        $this->view('shop/index', $data);
        $this->view('templates/footer', $data);
    }
}