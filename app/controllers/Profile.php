<?php

class Profile extends Controller
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            Flasher::setFlash('Anda harus login untuk mengakses halaman ini.', 'warning');
            header('Location: ' . BASEURL . 'auth/login');
            exit;
        }
    }

    public function index($activeTab = 'profile')
    {
        $data['page_title'] = 'Profil Saya';
        $data['current_controller_name'] = 'Profile';

        $profileModel = $this->model('Profile_model');
        $userId = $_SESSION['user_id'];
        $data['user'] = $profileModel->getUserById($userId);

        if (!$data['user']) {
            Flasher::setFlash('User tidak ditemukan.', 'error');
            header('Location: ' . BASEURL . 'auth/logout');
            exit;
        }
        
        $data['cart_item_count'] = $profileModel->getCartItemCount($userId); 
        $_SESSION['cart_item_count'] = $data['cart_item_count']; //

        $data['product_history'] = $profileModel->getProductHistory($userId);
        $data['grooming_history'] = $profileModel->getGroomingHistory($userId);
        $data['penitipan_history'] = $profileModel->getPenitipanHistory($userId);
        
        $validTabs = ['profile', 'purchase-history', 'service-history'];
        $data['active_tab'] = in_array($activeTab, $validTabs) ? $activeTab : 'profile';
        
        if (isset($_SESSION['form_data'])) {
            // Jika ada data form dari validasi gagal, Anda bisa memilih untuk
            // menimpanya ke $data['user'] agar form terisi kembali.
            // Namun, ini bisa rumit jika struktur tidak sama persis.
            // Untuk sekarang, kita hanya unset. Flasher akan menampilkan error.
            unset($_SESSION['form_data']); 
        }

        $this->view('templates/header', $data);
        $this->view('profile/index', $data);
        $this->view('templates/footer', $data); // Asumsi ada footer.php
    }

    public function riwayatpesanan()
    {
        $this->index('purchase-history');
    }

    public function riwayatlayanan()
    {
        $this->index('service-history');
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = [];
            $userId = $_SESSION['user_id'];
            $profileModel = $this->model('Profile_model');


            // 1. Ambil dan bersihkan input
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['fullName'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $phoneNumberInput = trim($_POST['phoneNumber'] ?? '');
            
            // 2. Validasi
            // Username
            if (empty($username)) {
                $errors[] = 'Username tidak boleh kosong.';
            } elseif (strlen($username) > 10) { // Sesuai DB: varchar(10)
                $errors[] = 'Username terlalu panjang (maks 10 karakter).';
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $errors[] = 'Username hanya boleh mengandung huruf, angka, dan underscore (_).';
            } elseif ($profileModel->isUsernameTakenByOtherUser($username, $userId)) {
                $errors[] = 'Username ini sudah digunakan.';
            }

            // Nama Lengkap
            if (empty($fullName)) {
                $errors[] = 'Nama lengkap tidak boleh kosong.';
            } elseif (strlen($fullName) > 25) { // Sesuai DB: varchar(25)
                $errors[] = 'Nama lengkap terlalu panjang (maks 25 karakter).';
            } elseif (!preg_match('/^[a-zA-Z\s.\'-]+$/', $fullName)) {
                 $errors[] = 'Nama lengkap hanya boleh mengandung huruf, spasi, titik, apostrof, dan strip.';
            }


            // Email
            if (empty($email)) {
                $errors[] = 'Email tidak boleh kosong.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Format email tidak valid.';
            } elseif (strlen($email) > 255) {
                $errors[] = 'Email terlalu panjang (maks 255 karakter).';
            } elseif ($profileModel->isEmailTakenByOtherUser($email, $userId)) {
                 $errors[] = 'Email ini sudah digunakan oleh akun lain.';
            }

            // Alamat (NOT NULL di DB)
            if (empty($address)) {
                $errors[] = 'Alamat lengkap tidak boleh kosong.';
            } elseif (strlen($address) > 65535) { // Batas TEXT di MySQL
                $errors[] = 'Alamat terlalu panjang.';
            }
            // Anda bisa menambahkan validasi lebih spesifik untuk alamat jika perlu

            // Nomor Telepon (NOT NULL dan INT di DB)
            $finalPhoneNumber = null;
            if (empty($phoneNumberInput)) {
                $errors[] = 'Nomor telepon tidak boleh kosong.';
            } elseif (!is_numeric($phoneNumberInput)) {
                $errors[] = 'Nomor telepon harus berupa angka.';
            } elseif (strlen($phoneNumberInput) < 7 || strlen($phoneNumberInput) > 15) { // Batas umum panjang no telp
                 $errors[] = 'Panjang nomor telepon tidak valid (antara 7-15 digit).';
            } else {
                $finalPhoneNumber = (int)$phoneNumberInput; // Konversi ke integer untuk disimpan
            }


            // 3. Jika ada error, tampilkan dan kembali
            if (!empty($errors)) {
                // $_SESSION['form_data'] = $_POST; // Untuk mengisi kembali form jika diinginkan
                Flasher::setFlash(implode('<br>', $errors), 'error');
                header('Location: ' . BASEURL . 'profile');
                exit;
            }

            // 4. Jika tidak ada error, proses update
            $dataToUpdate = [
                'ID_User' => $userId,
                'username' => $username,
                'full_name' => $fullName,
                'email' => $email,
                'alamat' => $address, // $address sudah divalidasi not empty
                'no_telepon' => $finalPhoneNumber // $finalPhoneNumber sudah divalidasi not empty dan jadi integer
            ];
            
            if ($profileModel->updateUser($dataToUpdate)) {
                // Perbarui juga session jika username atau full_name di header digunakan
                $_SESSION['user_username'] = $dataToUpdate['username']; 
                // Jika Anda menampilkan full_name di header, update juga:
                // $_SESSION['user_full_name'] = $dataToUpdate['full_name']; 
                Flasher::setFlash('Profil berhasil diperbarui.', 'success');
            } else {
                Flasher::setFlash('Gagal memperbarui profil atau tidak ada perubahan data.', 'warning');
            }
            header('Location: ' . BASEURL . 'profile');
            exit;

        } else {
            // Jika bukan POST, redirect atau tampilkan error
            header('Location: ' . BASEURL . 'profile');
            exit;
        }
    }
}