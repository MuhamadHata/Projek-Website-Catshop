<?php // app/views/penitipan/index.php 
?>

<section class="penitipan-form-section py-5">
    <div class="container">

        <?php
        // **PENAMBAHAN KODE**
        // Baris ini akan memeriksa dan menampilkan notifikasi (sukses, error, warning) 
        // dari Flasher yang diatur oleh controller setelah redirect.
        Flasher::flash();
        ?>

        <h2 class="section-title text-center mb-5">Pesan Layanan Penitipan Kucing</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <form id="penitipanFormClient">
                            <div class="mb-3">
                                <label for="tanggalPenitipan" class="form-label">Tanggal Mulai Penitipan:</label>
                                <input type="date" class="form-control" id="tanggalPenitipan" name="tanggal_penitipan_display" required>
                            </div>
                            <div class="mb-3">
                                <label for="lamaPenitipan" class="form-label">Lama Penitipan (hari):</label>
                                <input type="number" class="form-control" id="lamaPenitipan" name="lama_penitipan_display" min="1" value="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="jumlahKucingTotal" class="form-label">Jumlah Kucing (total yang dititip):</label>
                                <input type="number" class="form-control" id="jumlahKucingTotal" name="jumlah_kucing_total_display" min="1" value="1" required>
                            </div>

                            <hr class="my-4">

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="toggleObatHarianUi">
                                <label class="form-check-label" for="toggleObatHarianUi">
                                    Perlu Obat Harian Rutin? (Ada biaya tambahan Rp <?php echo number_format($data['tarif_obat_harian'] ?? 0, 0, ',', '.'); ?>/hari/kucing)
                                </label>
                            </div>

                            <div id="detailObatHarianSection" class="p-3 border rounded bg-light mb-4 d-none">
                                <h5 class="mb-3">Detail Obat Harian</h5>
                                <div class="mb-3">
                                    <label for="jumlahKucingDiberiObat" class="form-label">Jumlah Kucing yang Diberi Obat:</label>
                                    <input type="number" class="form-control" id="jumlahKucingDiberiObat" name="jumlah_kucing_diberi_obat_display" min="0" value="0">
                                    <small class="form-text text-muted">Tidak boleh melebihi total kucing yang dititip.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="namaObatHarian" class="form-label">Nama Obat:</label>
                                    <input type="text" class="form-control" id="namaObatHarian" name="nama_obat_harian_display" placeholder="Contoh: Vitamin Bulu NutriGel">
                                    <small id="namaObatError" class="text-danger d-none">Nama obat wajib diisi jika layanan obat dipilih.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="keteranganPenggunaanObat" class="form-label">Keterangan Penggunaan:</label>
                                    <textarea class="form-control" id="keteranganPenggunaanObat" name="keterangan_penggunaan_obat_display" rows="3" placeholder="Contoh: 1x sehari setelah makan pagi, 1/2 sendok teh"></textarea>
                                </div>
                            </div>

                            <button type="button" id="cekBiayaPenitipanBtn" class="btn btn-submit w-100">Cek Detail Biaya Penitipan</button>
                        </form>

                        <div id="biayaPenitipanInfo" class="payment-info-card mt-4 d-none">
                            <h4>Detail Biaya Penitipan</h4>
                            <p><strong>Tanggal Mulai:</strong> <span id="displayTanggalPenitipan"></span></p>
                            <p><strong>Lama Penitipan:</strong> <span id="displayLamaPenitipan"></span> hari</p>
                            <p><strong>Jumlah Kucing Dititip:</strong> <span id="displayJumlahKucingTotal"></span> ekor</p>
                            <div id="displayObatInfoSection" class="d-none">
                                <p><strong>Biaya Obat per Hari per Kucing:</strong> <span id="displayBiayaObatPerHari"></span></p>
                                <p><strong>Subtotal Biaya Obat:</strong> <span id="displaySubtotalBiayaObat"></span></p>
                            </div>
                            <p><strong>Subtotal Biaya Penitipan Dasar:</strong> <span id="displaySubtotalDasar"></span></p>
                            <p class="h5"><strong>Total Estimasi Biaya:</strong> <span id="displayTotalBiayaPenitipan" class="text-danger"></span></p>
                            <hr>
                            <button class="btn btn-success w-100" id="bayar-penitipan-btn">
                                <i class="bi bi-shield-check"></i> Bayar Sekarang via Midtrans
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // ... Seluruh kode JavaScript Anda tidak berubah ...
    document.addEventListener('DOMContentLoaded', function() {
        const TARIF_PENITIPAN_HARIAN = <?php echo $data['tarif_penitipan_harian'] ?? 0; ?>;
        const TARIF_OBAT_HARIAN = <?php echo $data['tarif_obat_harian'] ?? 0; ?>;
        const penitipanFormClient = document.getElementById('penitipanFormClient');
        const toggleObatHarianUiCheckbox = document.getElementById('toggleObatHarianUi');
        const detailObatHarianSection = document.getElementById('detailObatHarianSection');
        const cekBiayaPenitipanBtn = document.getElementById('cekBiayaPenitipanBtn');
        const biayaPenitipanInfoDiv = document.getElementById('biayaPenitipanInfo');
        const tanggalPenitipanInput = document.getElementById('tanggalPenitipan');
        const lamaPenitipanInput = document.getElementById('lamaPenitipan');
        const jumlahKucingTotalInput = document.getElementById('jumlahKucingTotal');
        const jumlahKucingDiberiObatInput = document.getElementById('jumlahKucingDiberiObat');
        const namaObatInput = document.getElementById('namaObatHarian');
        const keteranganObatInput = document.getElementById('keteranganPenggunaanObat');
        const displayTanggalPenitipan = document.getElementById('displayTanggalPenitipan');
        const displayLamaPenitipan = document.getElementById('displayLamaPenitipan');
        const displayJumlahKucingTotal = document.getElementById('displayJumlahKucingTotal');
        const displayObatInfoSection = document.getElementById('displayObatInfoSection');
        const displayBiayaObatPerHari = document.getElementById('displayBiayaObatPerHari');
        const displaySubtotalBiayaObat = document.getElementById('displaySubtotalBiayaObat');
        const displaySubtotalDasar = document.getElementById('displaySubtotalDasar');
        const displayTotalBiayaPenitipan = document.getElementById('displayTotalBiayaPenitipan');
        const bayarPenitipanBtn = document.getElementById('bayar-penitipan-btn');

        if (typeof window.isPaymentProcessing === 'undefined') {
            window.isPaymentProcessing = false;
        }

        function formatCurrencyJS(amount) {
            return 'Rp ' + Number(amount).toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        function hideBiayaInfo() {
            if (biayaPenitipanInfoDiv && !biayaPenitipanInfoDiv.classList.contains('d-none')) {
                biayaPenitipanInfoDiv.classList.add('d-none');
            }
        }

        toggleObatHarianUiCheckbox.addEventListener('change', function() {
            detailObatHarianSection.classList.toggle('d-none', !this.checked);
            if (this.checked && parseInt(jumlahKucingDiberiObatInput.value) === 0 && parseInt(jumlahKucingTotalInput.value) > 0) {
                jumlahKucingDiberiObatInput.value = jumlahKucingTotalInput.value;
            } else if (!this.checked) {
                jumlahKucingDiberiObatInput.value = 0;
                namaObatInput.value = '';
                keteranganObatInput.value = '';
            }
            hideBiayaInfo();
        });

        [jumlahKucingTotalInput, jumlahKucingDiberiObatInput, tanggalPenitipanInput, lamaPenitipanInput, namaObatInput, keteranganObatInput].forEach(el => {
            if (el) el.addEventListener('input', hideBiayaInfo);
        });

        cekBiayaPenitipanBtn.addEventListener('click', function() {
            if (!penitipanFormClient.checkValidity()) {
                penitipanFormClient.reportValidity();
                return;
            }
            const lamaHari = parseInt(lamaPenitipanInput.value) || 0;
            const jumlahKucing = parseInt(jumlahKucingTotalInput.value) || 0;
            const layananObatAktifLogika = toggleObatHarianUiCheckbox.checked;

            if (lamaHari <= 0 || jumlahKucing <= 0) {
                alert("Lama penitipan dan jumlah kucing harus lebih dari 0.");
                return;
            }

            const biayaDasarPenitipan = TARIF_PENITIPAN_HARIAN * jumlahKucing * lamaHari;
            let biayaTambahanObat = 0;

            displayObatInfoSection.classList.add('d-none');
            if (layananObatAktifLogika) {
                let jumlahKucingObat = parseInt(jumlahKucingDiberiObatInput.value) || 0;
                if (jumlahKucingObat > 0) {
                    biayaTambahanObat = TARIF_OBAT_HARIAN * jumlahKucingObat * lamaHari;
                    displayBiayaObatPerHari.textContent = formatCurrencyJS(TARIF_OBAT_HARIAN);
                    displaySubtotalBiayaObat.textContent = formatCurrencyJS(biayaTambahanObat);
                    displayObatInfoSection.classList.remove('d-none');
                }
            }

            const totalEstimasiBiaya = biayaDasarPenitipan + biayaTambahanObat;
            displayTanggalPenitipan.textContent = new Date(tanggalPenitipanInput.value).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            displayLamaPenitipan.textContent = lamaHari;
            displayJumlahKucingTotal.textContent = jumlahKucing;
            displaySubtotalDasar.textContent = formatCurrencyJS(biayaDasarPenitipan);
            displayTotalBiayaPenitipan.textContent = formatCurrencyJS(totalEstimasiBiaya);
            biayaPenitipanInfoDiv.classList.remove('d-none');
            biayaPenitipanInfoDiv.scrollIntoView({
                behavior: 'smooth'
            });
        });

        bayarPenitipanBtn.addEventListener('click', async function() {
            event.preventDefault();
            if (biayaPenitipanInfoDiv.classList.contains('d-none')) {
                alert('Harap klik "Cek Detail Biaya Penitipan" terlebih dahulu.');
                return;
            }
            if (window.isPaymentProcessing) return;

            window.isPaymentProcessing = true;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

            const orderData = {
                tanggal_penitipan: tanggalPenitipanInput.value,
                lama_penitipan_hari: parseInt(lamaPenitipanInput.value),
                jumlah_kucing_total: parseInt(jumlahKucingTotalInput.value),
                nama_obat_harian: toggleObatHarianUiCheckbox.checked ? namaObatInput.value.trim() : '',
                keterangan_penggunaan_obat: toggleObatHarianUiCheckbox.checked ? keteranganObatInput.value.trim() : '',
                jumlah_kucing_diberi_obat: toggleObatHarianUiCheckbox.checked ? parseInt(jumlahKucingDiberiObatInput.value) : 0
            };

            try {
                const response = await fetch('<?= BASEURL ?>penitipan/requestMidtransToken', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Respons server tidak OK');

                window.snap.pay(data.snapToken, {
                    onSuccess: (midtransResult) => {
                        processPenitipanOrderOnServer(midtransResult, orderData);
                    },
                    onPending: (result) => {
                        window.location.href = '<?= BASEURL ?>profile/riwayatlayanan';
                    },
                    onError: (result) => {
                        window.location.href = '<?= BASEURL ?>penitipan?status=failed';
                    },
                    onClose: () => {
                        window.location.href = '<?= BASEURL ?>penitipan?status=cancelled';
                    }
                });

            } catch (error) {
                alert('Gagal memulai pembayaran: ' + error.message);
                window.isPaymentProcessing = false;
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-shield-check"></i> Bayar Sekarang via Midtrans';
            }
        });

        async function processPenitipanOrderOnServer(midtransResult, orderData) {
            try {
                const payload = {
                    midtrans_result: midtransResult,
                    order_data: orderData
                };
                const response = await fetch('<?= BASEURL ?>penitipan/processOrderAfterPayment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const serverResponse = await response.json();
                if (serverResponse.success && serverResponse.redirect_url) {
                    window.location.href = serverResponse.redirect_url;
                } else {
                    alert('Error setelah pembayaran: ' + (serverResponse.message || 'Tidak ada URL redirect.'));
                    window.location.href = '<?= BASEURL ?>penitipan?status=failed';
                }
            } catch (error) {
                alert('Terjadi kesalahan kritis saat menyimpan pesanan Anda. Hubungi admin.');
                window.location.href = '<?= BASEURL ?>penitipan?status=failed';
            }
        }
    });
</script>