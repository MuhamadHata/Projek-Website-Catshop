<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-gxewygO8hUuzGeAv"></script>
    <title>Cat Shop - <?php echo $data['page_title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/style.css">
</head>

<body>

    <?php
    // Pastikan session sudah dimulai untuk Flasher bekerja
    // Sebaiknya session_start() ada di init.php atau public/index.php paling atas
    if (session_status() == PHP_SESSION_NONE) {
        session_start(); // Tambahkan ini jika belum ada jaminan session sudah dimulai
    }
    $currentController = $data['current_controller_name'] ?? '';
    Flasher::flash(); // Menampilkan flash message global
    ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASEURL; ?>">
                    <img src="<?php echo BASEURL; ?>assets/Logo_1.png" alt="Cat Shop Logo" class="logo-image">
                </a>
                <div class="d-flex ms-auto">
                    <?php
                    // Ambil nama controller saat ini, ini sudah ada di bagian bawah, kita bisa definisikan di awal jika belum
                    // Jika $data['current_controller_name'] belum tentu ada di scope ini, pastikan itu dikirim ke view header ini
                    $currentControllerForCart = $data['current_controller_name'] ?? '';
                    ?>
                    <a href="<?php echo BASEURL; ?>keranjang" class="btn cart-btn position-relative me-2 d-flex align-items-center justify-content-center cart-btn-nav px-3 <?php echo ($currentControllerForCart === 'Keranjang') ? 'active' : ''; ?>">
                        <i class="bi bi-cart3"></i>
                        <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger cart-badge" id="cartItemCount">
                            <?php echo $data['cart_item_count'] ?? 0; ?>
                            <span class="visually-hidden">items in cart</span>
                        </span>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <?php
                    // Ambil nama controller saat ini
                    $currentController = $data['current_controller_name'] ?? '';
                    // Anda perlu cara untuk mengidentifikasi halaman CS Chat jika berbeda
                    // Misalnya, jika CsChat adalah controller sendiri:
                    // $isCsChatPage = ($currentController === 'CsChat'); 
                    // Atau jika dikelola via variabel lain:
                    $isCsChatPage = ($data['special_page_indicator'] ?? '') === 'cs_chat';
                    ?>
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item text-center text-lg-start">
                            <a class="nav-link <?php echo ($currentController === 'Shop') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>shop">Shop</a>
                        </li>
                        <li class="nav-item text-center text-lg-start">
                            <a class="nav-link <?php echo ($currentController === 'Grooming') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>grooming">Grooming</a>
                        </li>
                        <li class="nav-item text-center text-lg-start">
                            <a class="nav-link <?php echo ($currentController === 'Penitipan') ? 'active' : ''; ?>" href="<?php echo BASEURL; ?>penitipan">Penitipan</a>
                        </li>
                    </ul>
                    <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center justify-content-lg-end w-100 w-lg-auto mt-3 mt-lg-0">
                        <?php
                        // Asumsikan CS Chat memiliki controller sendiri bernama 'CsChatController' atau 'Chat'
                        $csChatControllerName = 'CsChat'; // Ganti dengan nama controller CS Chat Anda yang sebenarnya
                        $csChatLink = BASEURL . strtolower($csChatControllerName);
                        ?>
                        <a href="https://wa.me/6288801807389" class="btn btn-signup-nav mb-2 mb-lg-0 me-lg-2 <?php echo ($currentController === $csChatControllerName) ? 'active' : ''; ?> d-flex align-items-center justify-content-center" style="width: 150px;"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-headset me-2" viewBox="0 0 16 16">
                                <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5" />
                            </svg>
                            CS Chat
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-login-nav dropdown-toggle d-flex align-items-center justify-content-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="width: 150px;"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle me-2 profile-avatar-nav" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                </svg>
                                <span id="userNameDisplayNav"><?php echo htmlspecialchars($_SESSION['user_username']); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <?php
                                // Asumsikan Controller untuk Profil adalah 'ProfileController'
                                $profileControllerName = 'Profile'; // Ganti dengan nama controller profil Anda
                                $profileLink = BASEURL . strtolower($profileControllerName);
                                ?>
                                <li><a class="dropdown-item <?php echo ($currentController === $profileControllerName && empty($_GET['tab'])) ? 'active' : ''; ?>" href="<?php echo $profileLink; ?>"><i class="bi bi-person-circle me-2"></i> Profil Saya</a></li>
                                <li><a class="dropdown-item <?php echo ($currentController === $profileControllerName && ($_GET['tab'] ?? '') === 'purchase-history') ? 'active' : ''; ?>" href="<?php echo $profileLink; ?>/riwayatpesanan"><i class="bi bi-box-seam me-2"></i> Riwayat Pesanan</a></li>
                                <li><a class="dropdown-item <?php echo ($currentController === $profileControllerName && ($_GET['tab'] ?? '') === 'service-history') ? 'active' : ''; ?>" href="<?php echo $profileLink; ?>/riwayatlayanan"><i class="bi bi-clipboard-check me-2"></i> Riwayat Layanan</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASEURL; ?>auth/logout" id="logoutButton"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

    <?php else: ?>
        <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASEURL; ?>">
                    <img src="<?php echo BASEURL; ?>assets/Logo_1.png" alt="Cat Shop Logo" class="logo-image">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <?php
                    // Ambil nama controller saat ini
                    $currentController = $data['current_controller_name'] ?? '';
                    // Anda perlu cara untuk mengidentifikasi halaman CS Chat jika berbeda
                    // Misalnya, jika CsChat adalah controller sendiri:
                    // $isCsChatPage = ($currentController === 'CsChat'); 
                    // Atau jika dikelola via variabel lain:
                    $isCsChatPage = ($data['special_page_indicator'] ?? '') === 'cs_chat';
                    ?>
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item text-center text-lg-start">
                            <a class="nav-link <?php echo ($currentController === 'Home' || empty($currentController)) ? 'active' : ''; ?>" aria-current="page" href="<?php echo BASEURL; ?>">Home</a>
                        </li>
                        <li class="nav-item text-center text-lg-start">
                            <?php
                            // Asumsikan Contact Us adalah bagian dari PageController dengan method contact
                            $pageControllerName = 'Page'; // Ganti jika perlu
                            $contactMethodName = 'contact';
                            $contactLink = BASEURL . strtolower($pageControllerName) . '/' . $contactMethodName;
                            ?>
                            <a class="nav-link" href="<?php echo BASEURL; ?>#about-us-section">Contact Us</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center mt-3 mt-lg-0">
                        <a href="<?php echo BASEURL; ?>auth/register" class="btn btn-signup-nav">Sign Up</a>
                        <a href="<?php echo BASEURL; ?>auth/login" class="btn btn-login-nav ms-2">Login</a>
                    </div>
                </div>
            </div>
        </nav>

    <?php endif; ?>