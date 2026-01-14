<?php // app/views/grooming/index.php 
?>

<section class="grooming-form-section py-5">
    <div class="container">

        <?php
        // // Baris ini akan menampilkan notifikasi (sukses, error, warning) dari Flasher
        // setelah redirect dari controller.
        Flasher::flash();
        ?>

        <h2 class="section-title text-center mb-5">Pesan Layanan Grooming</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form id="groomingFormClient">
                            <div class="mb-3">
                                <label for="groomingDate" class="form-label">Tanggal Grooming:</label>
                                <input type="date" class="form-control" id="groomingDate" name="tanggal_grooming_display" required>
                            </div>
                            <div class="mb-3">
                                <label for="groomingLocation" class="form-label">Lokasi Grooming:</label>
                                <select class="form-select" id="groomingLocation" name="tempat_grooming_display" required>
                                    <option value="">Pilih Lokasi</option>
                                    <option value="toko">Di Toko (Cat Shop)</option>
                                    <option value="rumah">Di Rumah (Home Service)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jumlahKucing" class="form-label">Jumlah Kucing:</label>
                                <input type="number" class="form-control" id="jumlahKucing" name="jumlah_kucing_display" min="1" value="1" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Pilih Paket Grooming:</label>
                                <div id="paketGroomingOptions">
                                    <?php
                                    // Ambil daftar paket dari data yang dikirim controller
                                    // $data['paket_grooming_list'] berisi array $this->definisi_paket_grooming_server dari controller
                                    if (!empty($data['paket_grooming_list'])) :
                                        foreach ($data['paket_grooming_list'] as $kode_paket => $detail_paket) : ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input grooming-paket-item" type="radio"
                                                    name="tipe_paket_grooming_pilihan" value="<?php echo htmlspecialchars($kode_paket); ?>"
                                                    data-price="<?php echo $detail_paket['harga_satuan']; ?>"
                                                    data-details="<?php echo htmlspecialchars($detail_paket['detail_layanan']); ?>"
                                                    data-paket-nama="<?php echo htmlspecialchars($detail_paket['nama_paket']); ?>"
                                                    id="paket_<?php echo htmlspecialchars($kode_paket); ?>"
                                                    required>
                                                <label class="form-check-label" for="paket_<?php echo htmlspecialchars($kode_paket); ?>">
                                                    <strong><?php echo htmlspecialchars($detail_paket['nama_paket']); ?></strong> - Rp <?php echo number_format($detail_paket['harga_satuan'], 0, ',', '.'); ?>
                                                    <small class="d-block text-muted">(<?php echo htmlspecialchars($detail_paket['detail_layanan']); ?>)</small>
                                                </label>
                                            </div>
                                        <?php endforeach;
                                    else : ?>
                                        <p class="text-muted">Tidak ada paket grooming yang tersedia saat ini.</p>
                                    <?php endif; ?>
                                </div>
                                <small id="paketError" class="text-danger d-none">Pilih salah satu paket.</small>
                            </div>
                            <button type="button" id="cekPembayaranBtn" class="btn btn-submit w-100">Cek Detail Pesanan</button>
                        </form>

                        <div id="paymentInfo" class="payment-info-card mt-4 d-none">
                            <h4>Detail Pesanan Grooming</h4>
                            <p><strong>Tanggal Grooming:</strong> <span id="displayGroomingDate"></span></p>
                            <p><strong>Lokasi Grooming:</strong> <span id="displayGroomingLocationText"></span></p>
                            <p><strong>Jumlah Kucing:</strong> <span id="displayJumlahKucing"></span> ekor</p>
                            <p><strong>Paket Dipilih:</strong> <span id="displayPaketNama"></span></p>
                            <p><strong>Detail Layanan:</strong> <span id="displayPaketDetail"></span></p>
                            <p id="displayHomeServiceFeeText" class="d-none"><strong>Biaya Home Service:</strong> <span id="displayHomeServiceFee"></span></p>
                            <p class="h5"><strong>Total Biaya:</strong> <span id="displayTotalPrice" class="text-danger"></span></p>
                            <hr>
                            <p class="mt-3 text-muted small">Pembayaran dapat dilakukan di lokasi (Cash) atau konfirmasi via Admin untuk metode lain.</p>
                            <button class="btn btn-success w-100" id="bayar-grooming-btn">
                                <i class="bi bi-shield-check"></i> Bayar Sekarang via Midtrans
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<form id="actualGroomingOrderForm" action="<?php echo BASEURL; ?>grooming/pesan" method="POST" class="d-none">
    <input type="hidden" name="tanggal_grooming" id="hiddenGroomingDate">
    <input type="hidden" name="tempat_grooming" id="hiddenGroomingLocation"> <input type="hidden" name="jumlah_kucing" id="hiddenJumlahKucing">
    <input type="hidden" name="tipe_grooming" id="hiddenTipeGrooming"> <input type="hidden" name="harga_total_final" id="hiddenHargaTotalGrooming"> <input type="hidden" name="payment_method" id="hiddenPaymentMethod">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- DEKLARASI ELEMEN ---
        const groomingFormClient = document.getElementById('groomingFormClient');
        const cekPembayaranBtn = document.getElementById('cekPembayaranBtn');
        const paymentInfoDiv = document.getElementById('paymentInfo');
        const groomingDateInput = document.getElementById('groomingDate');
        const groomingLocationSelect = document.getElementById('groomingLocation');
        const jumlahKucingInput = document.getElementById('jumlahKucing');
        const bayarGroomingBtn = document.getElementById('bayar-grooming-btn');
        const BIAYA_HOME_SERVICE = <?php echo $data['biaya_home_service_js'] ?? 0; ?>;

        if (typeof window.isPaymentProcessing === 'undefined') {
            window.isPaymentProcessing = false;
        }

        // --- FUNGSI BANTUAN ---
        function formatCurrencyJS(amount) {
            return 'Rp ' + Number(amount).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // --- EVENT LISTENER UNTUK KALKULASI BIAYA (Tidak Berubah) ---
        cekPembayaranBtn.addEventListener('click', function() {
            // Kode JavaScript Anda sebelumnya untuk validasi dan menampilkan detail biaya
            // tetap dipertahankan di sini karena itu adalah UX yang baik.
            if (!groomingFormClient.checkValidity()) {
                groomingFormClient.reportValidity();
                return;
            }
            const selectedPaketRadio = document.querySelector('.grooming-paket-item:checked');
            if (!selectedPaketRadio) {
                alert('Pilih salah satu paket grooming.');
                return;
            }

            // Kalkulasi
            const jumlahKucing = parseInt(jumlahKucingInput.value) || 1;
            const paketHargaSatuan = parseFloat(selectedPaketRadio.dataset.price) || 0;
            let totalBiayaPesanan = paketHargaSatuan * jumlahKucing;

            document.getElementById('displayGroomingDate').textContent = new Date(groomingDateInput.value).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('displayGroomingLocationText').textContent = groomingLocationSelect.selectedOptions[0].text;
            document.getElementById('displayJumlahKucing').textContent = jumlahKucing;
            document.getElementById('displayPaketNama').textContent = selectedPaketRadio.dataset.paketNama;
            document.getElementById('displayPaketDetail').textContent = selectedPaketRadio.dataset.details;

            if (groomingLocationSelect.value === 'rumah') {
                totalBiayaPesanan += BIAYA_HOME_SERVICE;
                document.getElementById('displayHomeServiceFee').textContent = formatCurrencyJS(BIAYA_HOME_SERVICE);
                document.getElementById('displayHomeServiceFeeText').classList.remove('d-none');
            } else {
                document.getElementById('displayHomeServiceFeeText').classList.add('d-none');
            }
            document.getElementById('displayTotalPrice').textContent = formatCurrencyJS(totalBiayaPesanan);
            paymentInfoDiv.classList.remove('d-none');
            paymentInfoDiv.scrollIntoView({
                behavior: 'smooth'
            });
        });

        // --- LOGIKA BARU UNTUK PEMBAYARAN MIDTRANS ---
        bayarGroomingBtn.addEventListener('click', async function() {
            if (paymentInfoDiv.classList.contains('d-none')) {
                alert('Harap klik "Cek Detail Pesanan" terlebih dahulu.');
                return;
            }
            if (window.isPaymentProcessing) return;

            window.isPaymentProcessing = true;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

            const selectedPaketRadio = document.querySelector('.grooming-paket-item:checked');
            const orderData = {
                tanggal_grooming: groomingDateInput.value,
                tempat_grooming: groomingLocationSelect.value,
                jumlah_kucing: parseInt(jumlahKucingInput.value),
                tipe_grooming: selectedPaketRadio ? selectedPaketRadio.value : null,
            };

            try {
                const response = await fetch('<?= BASEURL ?>grooming/requestMidtransToken', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Gagal memulai pembayaran.');

                window.snap.pay(data.snapToken, {
                    onSuccess: (midtransResult) => {
                        processGroomingOrderOnServer(midtransResult, orderData);
                    },
                    onPending: (result) => {
                        window.location.href = '<?= BASEURL ?>profile/riwayatlayanan';
                    },
                    onError: (result) => {
                        window.location.href = '<?= BASEURL ?>grooming?status=order_failed';
                    },
                    onClose: () => {
                        window.location.href = '<?= BASEURL ?>grooming?status=cancelled';
                    }
                });
            } catch (error) {
                window.location.href = '<?= BASEURL ?>grooming?status=error';
            }
        });

        async function processGroomingOrderOnServer(midtransResult, orderData) {
            try {
                const payload = {
                    midtrans_result: midtransResult,
                    order_data: orderData
                };
                const response = await fetch('<?= BASEURL ?>grooming/processOrderAfterPayment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const serverResponse = await response.json();
                if (serverResponse.success) {
                    window.location.href = serverResponse.redirect_url;
                } else {
                    window.location.href = '<?= BASEURL ?>grooming?status=cancelled';
                }
            } catch (error) {
                window.location.href = '<?= BASEURL ?>grooming?status=failed';
            }
        }
    });
</script>