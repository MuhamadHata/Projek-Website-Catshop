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
<body class="signup-page" style="opacity: 1;"> 
    <div class="signup-container">
        <div class="signup-image-section">
            <div class="image-stack">
                <img src="<?php echo BASEURL; ?>assets/Logo_1.png" alt="Logo Cat Shop" class="top-logo-hitam">
                <img src="<?php echo BASEURL; ?>assets/Gambar Shop.png" alt="Ilustrasi Toko Kucing" class="shop-illustration-img">
            </div>
            <div class="image-text">
                <h2>Untuk Kucing & Pecintanya</h2>
                <p>Bergabunglah dengan Cat Shop! Buat akun untuk memulai petualangan meong-mu.</p>
            </div>
        </div>
        <div class="signup-form-container">
            <h3 class="mb-4 text-center">Buat Akun Baru</h3>
            
            <?php if (!empty($data['errors']['form'])): ?>
                <div class="alert alert-danger text-center p-2 mb-3" role="alert">
                    <?php echo htmlspecialchars($data['errors']['form']); ?>
                </div>
            <?php endif; ?>
            <?php Flasher::flash(); ?>

            <form id="registerForm" method="POST" action="<?php echo BASEURL; ?>auth/register" novalidate>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?php echo isset($data['errors']['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo htmlspecialchars($data['input']['email'] ?? ''); ?>" required autocomplete="off">
                    <?php if (isset($data['errors']['email'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['email']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control <?php echo isset($data['errors']['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo htmlspecialchars($data['input']['username'] ?? ''); ?>" required autocomplete="off">
                    <small class="form-text text-muted">Maks. 10 karakter, unik.</small>
                    <?php if (isset($data['errors']['username'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['username']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control <?php echo isset($data['errors']['full_name']) ? 'is-invalid' : ''; ?>" id="full_name" name="full_name" value="<?php echo htmlspecialchars($data['input']['full_name'] ?? ''); ?>" required autocomplete="off">
                    <small class="form-text text-muted">Maks. 50 karakter.</small> <?php if (isset($data['errors']['full_name'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['full_name']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea class="form-control <?php echo isset($data['errors']['alamat']) ? 'is-invalid' : ''; ?>" id="alamat" name="alamat" rows="3" required autocomplete="off"><?php echo htmlspecialchars($data['input']['alamat'] ?? ''); ?></textarea>
                    <?php if (isset($data['errors']['alamat'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['alamat']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="no_telepon" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control <?php echo isset($data['errors']['no_telepon']) ? 'is-invalid' : ''; ?>" id="no_telepon" name="no_telepon" value="<?php echo htmlspecialchars($data['input']['no_telepon'] ?? ''); ?>" required autocomplete="off">
                    <small class="form-text text-muted">Hanya angka, 7-15 digit.</small>
                    <?php if (isset($data['errors']['no_telepon'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['no_telepon']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="pass" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo isset($data['errors']['pass']) ? 'is-invalid' : ''; ?>" id="pass" name="pass" required autocomplete="off"> 
                    <small class="form-text text-muted">Min. 6 karakter.</small>
                    <?php if (isset($data['errors']['pass'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['pass']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="confirmPass" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control <?php echo isset($data['errors']['confirmPass']) ? 'is-invalid' : ''; ?>" id="confirmPass" name="confirmPass" required autocomplete="off">
                    <?php if (isset($data['errors']['confirmPass'])): ?>
                        <div class="invalid-feedback"><?php echo htmlspecialchars($data['errors']['confirmPass']); ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100">Daftar</button>
                <p class="mt-3 text-center login-link">
                    Sudah punya akun? <a href="<?php echo BASEURL; ?>auth/login">Masuk di sini</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>