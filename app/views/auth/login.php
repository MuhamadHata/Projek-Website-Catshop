<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Shop - <?php echo htmlspecialchars($data['page_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASEURL; ?>css/style.css">
</head>
<body class="login-page" style="opacity: 1;">
    <?php 
        // Flasher::flash(); // Seharusnya sudah dipanggil di header, tapi karena ini halaman mandiri, bisa dipanggil di sini jika header tidak di-include
    ?>
    <div class="signup-container"> 
        <div class="signup-image-section">
            <div class="image-stack">
                <img src="<?php echo BASEURL; ?>assets/Logo_1.png" alt="Logo Cat Shop" class="top-logo-hitam">
                <img src="<?php echo BASEURL; ?>assets/Gambar Shop.png" alt="Ilustrasi Toko Kucing" class="shop-illustration-img">
            </div>
            <div class="image-text">
                <h2>Untuk Kucing & Pecintanya</h2>
                <p>Selamat datang kembali di Cat Shop! Masuk untuk melanjutkan petualangan meong-mu.</p>
            </div>
        </div>
        <div class="signup-form-container">
            <h3 class="mb-4 text-center">Selamat Datang Kembali!</h3>
            
            <!-- <a href="#" class="btn-google"> 
                <img src="<?php echo BASEURL; ?>assets/google_icon.png" alt="Google icon"> Masuk dengan Google
            </a>
            <div class="text-center my-3 or-separator">
                <span>ATAU</span>
            </div> -->

            <?php if (!empty($data['errors']['form'])): ?>
                <div class="alert alert-danger text-center p-2 mb-3" role="alert">
                    <?php echo htmlspecialchars($data['errors']['form']); ?>
                </div>
            <?php endif; ?>
            <?php Flasher::flash(); // Tempatkan Flasher di sini untuk menampilkan pesan sukses/error dari controller ?>

            <form id="loginForm" method="POST" action="<?php echo BASEURL; ?>auth/login" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?php echo isset($data['errors']['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($data['input']['email'] ?? ''); ?>" required>
                    <?php if (isset($data['errors']['email'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['email']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="pass" class="form-label">Password</label> 
                    <input type="password" class="form-control <?php echo isset($data['errors']['pass']) ? 'is-invalid' : ''; ?>" id="pass" name="pass" required> <?php if (isset($data['errors']['pass'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['pass']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="text">
                        <label class="form-check-label" for="rememberMeLogin">
                            <b>web selalu mengingatmu</b>    
                        </label>
                    </div>
                    <a href="#" class="forgot-password-link">Lupa Password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
                <p class="mt-3 text-center login-link">
                    Belum punya akun? <a href="<?php echo BASEURL; ?>auth/register">Daftar di sini</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // document.body.style.opacity = '0';
            // setTimeout(() => {
            //     document.body.style.transition = 'opacity 0.5s ease-in-out';
            //     document.body.style.opacity = '1';
            // }, 50);
        });
    </script> -->
</body>
</html>
