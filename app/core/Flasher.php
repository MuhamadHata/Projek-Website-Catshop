<?php

class Flasher {

    /**
     * Mengatur pesan flash yang akan ditampilkan.
     * @param string $pesan Isi pesan yang ingin ditampilkan.
     * @param string $tipe Tipe pesan (misalnya: 'success', 'error', 'warning', 'info').
     */
    public static function setFlash($pesan, $tipe) { // Hanya 2 parameter: pesan, lalu tipe
        // Pastikan session sudah dimulai sebelum mengakses $_SESSION
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'] = [
            'pesan' => $pesan,
            'tipe'  => strtolower($tipe) // Konsistensi tipe (lowercase)
        ];
    }

    /**
     * Menampilkan pesan flash jika ada, lalu menghapusnya dari session.
     */
    public static function flash() {
        // Pastikan session sudah dimulai
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['flash'])) {
            $pesanFlash = $_SESSION['flash']['pesan']; // Ambil pesan
            $tipeFlash = htmlspecialchars($_SESSION['flash']['tipe']); // Ambil tipe

            // Judul akan kita ambil dari tipe pesan yang di-capitalize
            $judulDisplay = ucfirst($tipeFlash);
            if ($tipeFlash === 'error') { // Judul khusus untuk error jika mau
                $judulDisplay = 'Error!';
            } elseif ($tipeFlash === 'success') {
                $judulDisplay = 'Berhasil!';
            }


            $icon = '';
            // Menentukan ikon berdasarkan tipe
            switch ($tipeFlash) {
                case 'success':
                    $icon = '<i class="bi bi-check-circle-fill me-2"></i>';
                    break;
                case 'error': 
                    $icon = '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
                    break;
                case 'warning':
                    $icon = '<i class="bi bi-exclamation-circle-fill me-2"></i>';
                    break;
                case 'info':
                    $icon = '<i class="bi bi-info-circle-fill me-2"></i>';
                    break;
            }
            
            // Mapping tipe ke kelas alert Bootstrap yang lebih sesuai
            $alertClass = 'alert-' . $tipeFlash; 
            if ($tipeFlash === 'error') {
                $alertClass = 'alert-danger'; 
            } elseif ($tipeFlash === 'warning') {
                $alertClass = 'alert-warning';
            } elseif ($tipeFlash === 'info') {
                $alertClass = 'alert-info';
            }

            // Pilih gaya tampilan yang Anda inginkan (Toast atau Terpusat)
            // Di sini saya contohkan yang Terpusat, karena Flasher login Anda tampak terpusat.
            echo '
            <div class="alert ' . $alertClass . ' alert-dismissible fade show m-3" role="alert" style="position: fixed; top: 60px; right: 20px; z-index: 1050;">
                <div class="d-flex align-items-center">
                    <div class="fs-4 me-3">' . $icon . '</div>
                    <div>
                        <h5 class="alert-heading mb-1">' . htmlspecialchars($judulDisplay) . '</h5>
                        <div>' . $pesanFlash . '</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
            unset($_SESSION['flash']);
        }
    }
}