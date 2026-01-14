<?php
$user = $data['user'];
$activeTab = $data['active_tab'];

// Helper getFieldValue tidak lagi diperlukan untuk alamat dan no_telepon karena NOT NULL
// Tapi bisa tetap digunakan untuk field lain jika ada yang opsional di masa depan
function getFieldValue($value, $defaultText = '') { //
    if ($value === null || trim($value) === '') { //
        return $defaultText; //
    }
    return $value; //
}
?>

<header class="profile-header align-items-center mb-5">
    <div class="container align-items-center justify-content-center d-flex">
        <div> 
            <h1 class="profile-name mb-0 text-center" id="profileDisplayName"><?php echo htmlspecialchars(!empty($user['full_name']) ? $user['full_name'] : ($user['username'] ?? 'Nama Pengguna')); ?></h1>
            <p class="profile-email mb-0 text-center" id="profileDisplayEmail"><?php echo htmlspecialchars($user['email'] ?? 'email@example.com'); ?></p>
        </div>
    </div>
</header>

<main class="container mb-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="profile-card">
                <ul class="nav nav-pills flex-column" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($activeTab === 'profile') ? 'active' : ''; ?>" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="<?php echo ($activeTab === 'profile') ? 'true' : 'false'; ?>">
                            <i class="bi bi-person-fill me-2"></i>Profil Saya
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($activeTab === 'purchase-history') ? 'active' : ''; ?>" id="purchase-history-tab" data-bs-toggle="pill" data-bs-target="#purchase-history" type="button" role="tab" aria-controls="purchase-history" aria-selected="<?php echo ($activeTab === 'purchase-history') ? 'true' : 'false'; ?>">
                            <i class="bi bi-box-seam me-2"></i>Riwayat Pesanan Produk
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo ($activeTab === 'service-history') ? 'active' : ''; ?>" id="service-history-tab" data-bs-toggle="pill" data-bs-target="#service-history" type="button" role="tab" aria-controls="service-history" aria-selected="<?php echo ($activeTab === 'service-history') ? 'true' : 'false'; ?>">
                            <i class="bi bi-clipboard-check me-2"></i>Riwayat Pemesanan Layanan
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="tab-content" id="profileTabsContent">
                <div class="tab-pane fade <?php echo ($activeTab === 'profile') ? 'show active' : ''; ?>" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="profile-card">
                        <h4 class="mb-4">Informasi Profil</h4>
                        <form id="profileForm" action="<?php echo BASEURL; ?>profile/update" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                                <small class="form-text text-muted">Maksimal 10 karakter, unik.</small>
                            </div>
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" required>
                                <small class="form-text text-muted">Maksimal 25 karakter.</small>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="phoneNumber" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars((string)($user['no_telepon'] ?? '')); ?>" required>
                                <small class="form-text text-muted">Hanya angka.</small>
                            </div>
                            <button type="submit" class="btn btn-save-profile">Simpan Perubahan</button>
                        </form>
                    </div>
                </div>

                <div class="tab-pane fade <?php echo ($activeTab === 'purchase-history') ? 'show active' : ''; ?>" id="purchase-history" role="tabpanel" aria-labelledby="purchase-history-tab">
                     <div class="profile-card">
                        <h4 class="mb-4">Riwayat Pesanan Produk</h4>
                        <div id="productOrderHistory">
                            <?php if (empty($data['product_history'])) : ?>
                                <p class="empty-history-message">Anda belum melakukan pesanan produk apapun.</p>
                            <?php else : ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Transaksi</th>
                                                <th>Tanggal</th>
                                                <th>Detail Pesanan</th>
                                                <th>Total Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['product_history'] as $order) : ?>
                                                <tr>
                                                    <td>#<?php echo htmlspecialchars($order['ID_Transaksi']); ?></td>
                                                    <td><?php echo date('d M Y, H:i', strtotime($order['tanggal_transaksi'])); ?></td>
                                                    <td><?php echo nl2br(htmlspecialchars($order['detail_transaksi_catatan'])); ?></td>
                                                    <td>Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade <?php echo ($activeTab === 'service-history') ? 'show active' : ''; ?>" id="service-history" role="tabpanel" aria-labelledby="service-history-tab">
                   <div class="profile-card">
                        <h4 class="mb-4">Riwayat Layanan Grooming</h4>
                        <div id="groomingServiceHistory">
                            <?php if (empty($data['grooming_history'])) : ?>
                                <p class="empty-history-message">Anda belum memesan layanan grooming apapun.</p>
                            <?php else : ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Transaksi</th>
                                                <th>Tanggal Grooming</th>
                                                <th>Tempat</th>
                                                <th>Jml Kucing</th>
                                                <th>Tipe</th>
                                                <th>Harga</th>
                                                <th>Catatan Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['grooming_history'] as $service) : ?>
                                                <tr>
                                                    <td>#<?php echo htmlspecialchars($service['ID_Transaksi']); ?></td>
                                                    <td><?php echo date('d M Y', strtotime($service['tanggal_grooming'])); ?></td>
                                                    <td><?php echo htmlspecialchars(ucfirst($service['tempat_grooming'])); ?></td>
                                                    <td><?php echo htmlspecialchars($service['jumlah_kucing']); ?></td>
                                                    <td><?php echo htmlspecialchars(ucfirst($service['tipe_grooming'] ?? '-')); ?></td>
                                                    <td>Rp <?php echo number_format($service['harga_grooming'], 0, ',', '.'); ?></td>
                                                    <td><?php echo nl2br(htmlspecialchars($service['detail_transaksi_catatan'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>

                        <h4 class="mt-5 mb-4">Riwayat Layanan Penitipan</h4>
                        <div id="boardingServiceHistory">
                            <?php if (empty($data['penitipan_history'])) : ?>
                                <p class="empty-history-message">Anda belum memesan layanan penitipan apapun.</p>
                            <?php else : ?>
                                <div class="table-responsive">
                                     <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID Transaksi</th>
                                                <th>Tanggal Penitipan</th>
                                                <th>Lama (Hari)</th>
                                                <th>Jml Kucing</th>
                                                <th>Obat Harian</th>
                                                <th>Harga</th>
                                                <th>Catatan Transaksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['penitipan_history'] as $service) : ?>
                                                <tr>
                                                    <td>#<?php echo htmlspecialchars($service['ID_Transaksi']); ?></td>
                                                    <td><?php echo date('d M Y', strtotime($service['tanggal_penitipan'])); ?></td>
                                                    <td><?php echo htmlspecialchars($service['lama_penitipan_hari']); ?></td>
                                                    <td><?php echo htmlspecialchars($service['jumlah_kucing']); ?></td>
                                                    <td><?php echo htmlspecialchars($service['nama_obat_harian'] ?? 'Tidak ada'); ?></td>
                                                    <td>Rp <?php echo number_format($service['harga_penitipan'], 0, ',', '.'); ?></td>
                                                     <td><?php echo nl2br(htmlspecialchars($service['detail_transaksi_catatan'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// ... (Salin script JS dari respons sebelumnya) ...
document.addEventListener('DOMContentLoaded', function () {
    // Anda mungkin tidak lagi memiliki avatarUploadInput atau profilePageAvatar jika dihapus dari HTML
    // const avatarUploadInput = document.getElementById('avatarUploadInput'); 
    // const profilePageAvatar = document.getElementById('profilePageAvatar');
    // const avatarUploadLabel = document.querySelector('label[for="avatarUpload"]'); 

    // if(avatarUploadLabel && avatarUploadInput) {
    //     avatarUploadLabel.addEventListener('click', function() {
    //         avatarUploadInput.click(); 
    //     });
    // }
    
    // if(avatarUploadInput && profilePageAvatar) {
    //     avatarUploadInput.addEventListener('change', function(event) {
    //         const file = event.target.files[0];
    //         if (file) {
    //             const reader = new FileReader();
    //             reader.onload = function(e) {
    //                 profilePageAvatar.src = e.target.result;
    //             }
    //             reader.readAsDataURL(file);
    //         }
    //     });
    // }

    let activeTabId = '<?php echo $activeTab; ?>';
    if (window.location.hash) {
        const hashTab = window.location.hash.substring(1); 
        if (document.getElementById(hashTab + '-tab')) { 
            activeTabId = hashTab;
        }
    }

    if (activeTabId) {
        const tabToActivate = document.getElementById(activeTabId + '-tab');
        if (tabToActivate) {
            // Bootstrap 5 tab activation (jika diperlukan secara eksplisit)
            // var tab = new bootstrap.Tab(tabToActivate);
            // tab.show();
        }
    }

    const profileTabLinks = document.querySelectorAll('#profileTabs .nav-link');
    profileTabLinks.forEach(tabLink => {
        tabLink.addEventListener('shown.bs.tab', function (event) {
            // const newActiveTabId = event.target.getAttribute('data-bs-target').substring(1); 
            // window.location.hash = newActiveTabId; // Optional: update URL hash
        });
    });
});
</script>