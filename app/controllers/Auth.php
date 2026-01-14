<?php // app/controllers/Auth.php

class Auth extends Controller
{
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL);
            exit;
        }

        $data['page_title'] = 'Register';
        $data['errors'] = [];
        $data['input'] = []; 

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitasi umum, bisa disesuaikan per field jika perlu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['input'] = $_POST; // Simpan semua input untuk repopulate form

            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $alamat = trim($_POST['alamat'] ?? '');
            $noTeleponInput = trim($_POST['no_telepon'] ?? '');
            $pass = $_POST['pass'] ?? ''; // Jangan trim password
            $confirmPass = $_POST['confirmPass'] ?? '';

            $userModel = $this->model('Auth_model');

            // Validasi Input
            // Username
            if (empty($email)) {
                $data['errors']['email'] = "Email tidak boleh kosong.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = "Format email tidak valid.";
            } elseif ($userModel->getUserByEmail($email)) { // Cek keunikan email
                $data['errors']['email'] = 'Email sudah terdaftar. Silakan gunakan email lain.';
            }

            // Email
            if (empty($username)) {
                $data['errors']['username'] = "Username tidak boleh kosong.";
            } elseif (strlen($username) > 10) {
                $data['errors']['username'] = "Username maksimal 10 karakter.";
            } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
                $data['errors']['username'] = "Username hanya boleh berisi huruf, angka, dan underscore (_).";
            } elseif ($userModel->getUserByUsername($username)) { // Cek keunikan username
                $data['errors']['username'] = "Username sudah terdaftar. Silakan gunakan username lain.";
            }

            // Full Name
            if (empty($fullName)) {
                $data['errors']['full_name'] = "Nama lengkap tidak boleh kosong.";
            } elseif (strlen($fullName) > 50) {
                $data['errors']['full_name'] = "Nama lengkap maksimal 50 karakter.";
            } elseif (!preg_match("/^[a-zA-Z\s.'-]+$/", $fullName)) {
                 $data['errors']['full_name'] = "Nama lengkap hanya boleh mengandung huruf, spasi, titik, apostrof, dan strip.";
            }

            // Alamat
            if (empty($alamat)) {
                $data['errors']['alamat'] = "Alamat tidak boleh kosong.";
            } // Bisa tambahkan validasi panjang maks jika perlu (TEXT bisa sangat panjang)

            // Nomor Telepon
            if (empty($noTeleponInput)) {
                $data['errors']['no_telepon'] = "Nomor telepon tidak boleh kosong.";
            } elseif (!preg_match('/^[0-9]+$/', $noTeleponInput)) { // Hanya izinkan angka untuk string telepon
                $data['errors']['no_telepon'] = "Nomor telepon harus berupa angka.";
            } elseif (strlen($noTeleponInput) < 7 || strlen($noTeleponInput) > 15) {
                $data['errors']['no_telepon'] = "Panjang nomor telepon tidak valid (7-15 digit).";
            }

            // Password
            if (empty($pass)) {
                $data['errors']['pass'] = "Password tidak boleh kosong.";
            } elseif (strlen($pass) < 6) {
                $data['errors']['pass'] = "Password minimal 6 karakter.";
            }

            // Konfirmasi Password
            if (empty($confirmPass)) {
                $data['errors']['confirmPass'] = "Konfirmasi password tidak boleh kosong.";
            } elseif ($pass !== $confirmPass) {
                $data['errors']['confirmPass'] = "Konfirmasi password tidak cocok.";
            }

            // Jika tidak ada error validasi
            if (empty($data['errors'])) {
                $userData = [
                    'username' => $username,
                    'full_name' => $fullName,
                    'email' => $email,
                    'alamat' => $alamat,
                    'no_telepon' => $noTeleponInput, // Kirim sebagai string
                    'pass' => $pass 
                ];

                if ($userModel->createUser($userData)) {
                    Flasher::setFlash('Registrasi berhasil! Silakan login.', 'success');
                    header('Location: ' . BASEURL . 'auth/login');
                    exit;
                } else {
                    $data['errors']['form'] = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
                }
            
            }
        }
        $this->view('auth/register', $data);
    }

    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL);
            exit;
        }

        $data['page_title'] = 'Login';
        $data['errors'] = [];
        $data['input'] = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
            $data['input'] = $_POST;

            $email = trim($_POST['email']);
            $pass = $_POST['pass'];

            if (empty($email)) {
                $data['errors']['email'] = "Email tidak boleh kosong.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['errors']['email'] = "Format email tidak valid.";
            }
            if (empty($pass)) {
                $data['errors']['pass'] = "Password tidak boleh kosong.";
            }

            if (empty($data['errors'])) {
                $userModel = $this->model('Auth_model');
                $user = $userModel->getUserByEmail($email); // Mengambil data user

                if ($user && password_verify($pass, $user['pass'])) {
                    $_SESSION['user_id'] = $user['ID_User'];
                    $_SESSION['user_username'] = $user['username'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_full_name'] = $user['full_name']; // Simpan juga full_name

                    Flasher::setFlash('Login berhasil! Selamat datang, ' . htmlspecialchars(!empty($user['full_name']) ? $user['full_name'] : $user['username']) . '.', 'success');
                    header('Location: ' . BASEURL . 'shop'); 
                    exit;
                } else {
                    $data['errors']['form'] = 'Email atau password salah.';
                }
            }
        }
        $this->view('auth/login', $data);
    }

    public function logout()
    {
        $_SESSION = array();
        if (session_destroy()) {
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            Flasher::setFlash('Anda telah berhasil logout.', 'success');
        } else {
            Flasher::setFlash('Gagal melakukan logout.', 'danger');
        }
        
        header('Location: ' . BASEURL . 'home');
        exit;
    }
}