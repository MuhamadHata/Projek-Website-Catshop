<?php
// **PERBAIKAN:** Semua logika PHP untuk menangani status GET dihapus dari sini.
// Logika ini sekarang sepenuhnya ditangani oleh controller Keranjang.php.

// Kalkulasi subtotal dan total, sudah ada di file asli Anda.
$subtotal = 0;
$shippingCost = 0; // Biaya pengiriman bisa diatur dari sini atau via JavaScript/AJAX nanti
$total = 0;

if (!empty($data['keranjang_items'])) {
    foreach ($data['keranjang_items'] as $item) {
        $harga = is_numeric($item['harga_produk']) ? $item['harga_produk'] : 0;
        $jumlah = is_numeric($item['jumlah']) ? $item['jumlah'] : 0;
        $item_subtotal = $harga * $jumlah;
        $subtotal += $item_subtotal;
    }
    $total = $subtotal + $shippingCost;
}
?>

<main class="container py-5">
    <section class="cart-section">
        <h1 class="cart-title">Keranjang Belanja Anda</h1>
        <?php
        // Baris ini akan menampilkan notifikasi (flash message) yang sudah disiapkan oleh controller.
        Flasher::flash();
        ?>

        <div id="cartItemsContainer">
            <?php if (empty($data['keranjang_items'])): ?>
                <p class="empty-cart-message alert alert-info" id="emptyCartMessage">
                    Keranjang Anda kosong. <a href="<?= BASEURL ?>shop" class="alert-link">Yuk, jelajahi produk kami!</a>
                </p>
            <?php else: ?>
                <?php foreach ($data['keranjang_items'] as $item): ?>
                    <?php
                    $harga_item = is_numeric($item['harga_produk']) ? $item['harga_produk'] : 0;
                    $jumlah_item = is_numeric($item['jumlah']) ? $item['jumlah'] : 0;
                    $stok_saat_ini = isset($item['stok_produk']) ? (int)$item['stok_produk'] : 0;
                    $subtotal_item_display = $harga_item * $jumlah_item;
                    $melebihi_stok = $jumlah_item > $stok_saat_ini;

                    $gambar_filename = '';
                    if (!empty($item['gambar_produk'])) {
                        $gambar_filename = basename(trim($item['gambar_produk']));
                    }
                    $imageWebPath = BASEURL . 'assets/' . rawurlencode($gambar_filename);
                    $placeholderImageWebPath = BASEURL . 'assets/placeholder.png';

                    $publicDirectoryPath = dirname($_SERVER['SCRIPT_FILENAME']);
                    $imageServerPath = $publicDirectoryPath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $gambar_filename;

                    $imageFileExistsOnServer = false;
                    if (!empty($gambar_filename) && file_exists($imageServerPath)) {
                        $imageFileExistsOnServer = true;
                    }
                    ?>
                    <div class="cart-item d-flex flex-wrap justify-content-between align-items-center mb-3 p-3 <?php if ($melebihi_stok) echo 'border-danger'; ?>" style="border: 1px solid <?php echo $melebihi_stok ? '#dc3545' : '#e0e0e0'; ?>; border-radius: 8px;">

                        <div class="item-image-container" style="flex: 0 0 100px; margin-right: 15px; text-align:center;">
                            <?php if ($imageFileExistsOnServer): ?>
                                <img src="<?= htmlspecialchars($imageWebPath) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>" class="img-fluid rounded" style="max-height: 80px; max-width: 80px; object-fit: contain;">
                            <?php else: ?>
                                <img src="<?= htmlspecialchars($placeholderImageWebPath) ?>" alt="Gambar tidak tersedia" class="img-fluid rounded" style="max-height: 80px; max-width: 80px; object-fit: contain;">
                            <?php endif; ?>
                        </div>

                        <div class="item-info" style="flex: 1 1 250px; margin-right: 15px;">
                            <h5 class="item-name mb-1"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                            <p class="item-price text-muted mb-1">Harga: Rp <?= number_format($harga_item, 0, ',', '.') ?></p>
                            <p class="item-subtotal fw-bold">Subtotal Produk: Rp <?= number_format($subtotal_item_display, 0, ',', '.') ?></p>
                            <p class="item-stock <?php if ($melebihi_stok) echo 'text-danger fw-bold'; ?>">
                                Stok Tersedia: <?= $stok_saat_ini ?>
                                <?php if ($melebihi_stok): ?>
                                    <br><span class="fw-bold">(Jumlah Anda melebihi stok!)</span>
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="item-quantity" style="flex: 0 1 180px; margin-right: 15px; display: flex; align-items: center;">
                            <form action="<?= BASEURL ?>keranjang/update" method="post" class="d-inline-flex align-items-center">
                                <input type="hidden" name="produk_id" value="<?= $item['produk_id'] ?>">
                                <input type="hidden" name="nama_produk_hidden" value="<?= htmlspecialchars($item['nama_produk']) ?>">
                                <label for="jumlah_<?= $item['produk_id'] ?>" class="visually-hidden">Jumlah</label>
                                <input type="number" id="jumlah_<?= $item['produk_id'] ?>" name="jumlah" value="<?= htmlspecialchars($jumlah_item) ?>" min="1" max="<?= $stok_saat_ini > 0 ? $stok_saat_ini : 1 ?>" required class="form-control form-control-sm d-inline <?php if ($melebihi_stok) echo 'is-invalid'; ?>" style="width: 70px; text-align: center; margin-right: 10px;">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </form>
                        </div>

                        <div class="item-actions" style="flex: 0 0 auto;">
                            <form action="<?= BASEURL ?>keranjang/remove" method="post" class="d-inline">
                                <input type="hidden" name="produk_id" value="<?= $item['produk_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div id="cartSummaryContainer" class="row justify-content-end mt-4" <?php if (empty($data['keranjang_items'])) echo 'style="display: none;"'; ?>>
            <div class="col-lg-5 col-md-7">
                <div class="card">
                    <div class="card-body cart-summary" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">
                        <h5>Ringkasan Belanja</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span id="cartSubtotal">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Pengiriman</span>
                            <span id="shippingCost">Rp <?= number_format($shippingCost, 0, ',', '.') ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between total fw-bold h5">
                            <span>Total</span>
                            <span id="cartTotal">Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </div>

                        <?php if (!empty($data['keranjang_items'])): ?>
                            <div class="d-grid mt-3">
                                <button type="button" class="btn btn-success w-100" id="pay-button">
                                    <i class="bi bi-shield-check"></i> Bayar Sekarang
                                </button>
                            </div>
                        <?php endif; ?>
                        <a href="<?= BASEURL ?>shop" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-left">Lanjut Belanja</i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const payButton = document.getElementById('pay-button');
        if (!payButton) return;

        if (typeof window.isPaymentProcessing === 'undefined') {
            window.isPaymentProcessing = false;
        }

        payButton.addEventListener('click', async function(event) {
            event.preventDefault();
            if (window.isPaymentProcessing) return;

            window.isPaymentProcessing = true;
            payButton.disabled = true;
            payButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';

            try {
                const response = await fetch('<?= BASEURL ?>keranjang/requestMidtransToken', {
                    method: 'POST',
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal mendapat token pembayaran.');

                window.snap.pay(data.snapToken, {
                    onSuccess: (midtransResult) => {
                        // **PERBAIKAN KUNCI:** Hapus `orderData` karena tidak terdefinisi dan tidak diperlukan.
                        // Cukup kirim hasil dari Midtrans ke server.
                        processOrderOnServer(midtransResult);
                    },
                    onPending: (result) => {
                        // Untuk pembayaran yang pending (misal: transfer bank), arahkan ke riwayat.
                        window.location.href = '<?= BASEURL ?>profile/riwayatpesanan';
                    },
                    onError: (result) => {
                        // Jika Midtrans mengembalikan error.
                        window.location.href = '<?= BASEURL ?>keranjang?status=failed';
                    },
                    onClose: () => {
                        // Jika pengguna menutup jendela pembayaran.
                        // **PERBAIKAN:** Menggunakan status 'cancelled' yang lebih sesuai.
                        window.location.href = '<?= BASEURL ?>keranjang?status=cancelled';
                    }
                });
            } catch (error) {
                console.error('Gagal memulai sesi pembayaran:', error);
                window.location.href = '<?= BASEURL ?>keranjang?status=error';
            }
        });

        async function processOrderOnServer(midtransResult) {
            try {
                const response = await fetch('<?= BASEURL ?>keranjang/processOrderAfterPayment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(midtransResult)
                });

                const serverResponse = await response.json();

                if (serverResponse.success) {
                    window.location.href = serverResponse.redirect_url;
                } else {
                    // Jika pembayaran sukses, tapi simpan ke DB gagal.
                    window.location.href = '<?= BASEURL ?>keranjang?status=order_failed';
                }
            } catch (error) {
                console.error('CRITICAL: Gagal saat memproses pesanan di server.', error);
                window.location.href = '<?= BASEURL ?>keranjang?status=order_failed';
            }
        }
    });
</script>