<?php // app/views/home/index.php

// Pastikan fungsi formatCurrency sudah tersedia
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        // Fungsi ini mengembalikan format mata uang Rupiah
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
?>
<section class="hero-section">
    <div class="hero-blob-background"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-content">
                <div class="sub-heading-container">
                    <p class="sub-heading-text">CAT SHOP</p>
                </div>
                <h1 class="hero-title">🐾 Segalanya untuk Si Manis Berbulu</h1>
                <p class="hero-lead">Tempat terbaik untuk memenuhi semua kebutuhan kucing kesayangan Anda. Dari makanan sehat, mainan lucu, furnitur nyaman, hingga layanan grooming. Semua tersedia di sini!</p>
                <div class="hero-button-container">
                    <a href="<?php echo BASEURL; ?>shop" class="btn hero-button">Belanja Sekarang</a>
                </div>
            </div>
            <div class="col-lg-6 hero-image-container text-center">
                <img src="<?php echo BASEURL; ?>assets/Gambar Kucing 1.png" alt="Cute Cats" class="img-fluid hero-image">
            </div>
        </div>
    </div>
</section>

<section class="categories-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Jelajahi berdasarkan Kategori</h2>
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <a href="<?php echo BASEURL; ?>shop" class="category-link">
                    <div class="category-card text-center">
                        <img src="<?php echo BASEURL; ?>assets/Aksesoris.png" alt="Aksesoris" class="img-fluid category-image">
                        <h5 class="category-name mt-3">Aksesoris</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="<?php echo BASEURL; ?>shop" class="category-link">
                    <div class="category-card text-center">
                        <img src="<?php echo BASEURL; ?>assets/Makanan.png" alt="Makanan" class="img-fluid category-image">
                        <h5 class="category-name mt-3">Makanan</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="<?php echo BASEURL; ?>shop" class="category-link">
                    <div class="category-card text-center">
                        <img src="<?php echo BASEURL; ?>assets/Furnitur.png" alt="Furniture" class="img-fluid category-image">
                        <h5 class="category-name mt-3">Furniture</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3 col-6">
                <a href="<?php echo BASEURL; ?>grooming" class="category-link">
                    <div class="category-card text-center">
                        <img src="<?php echo BASEURL; ?>assets/Grooming.png" alt="Grooming" class="img-fluid category-image">
                        <h5 class="category-name mt-3">Grooming</h5>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>


<section class="cta-section py-5">
    <div class="cta-blob-background"></div>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-5 text-center text-lg-start mb-4 mb-lg-0">
                <img src="<?php echo BASEURL; ?>assets/Gambar kucing 2.png" alt="Cute Cats CTA" class="img-fluid cta-image">
            </div>
            <div class="col-lg-7 cta-content text-center text-lg-start">
                <p class="cta-sub-heading">Cat shop</p>
                <h2 class="cta-title">Selalu penuhi kebutuhan kucing kesayanganmu</h2>
                <p class="cta-text">Temukan berbagai produk pilihan untuk kucing tercinta Anda. Dengan pilihan yang lengkap, kualitas terbaik, dan harga bersahabat, belanja jadi mudah, cepat, dan menyenangkan hanya di Cat Shop!</p>
            </div>
        </div>
    </div>
</section>

<section class="featured-products-section py-5 bg-light">
    <div class="container">
        <h2 class="display-5 fw-semibold text-dark text-center mb-5">Produk Unggulan</h2>
        <div class="row g-4 justify-content-center">
            <?php if (!empty($data['produk_populer'])) : ?>
                <?php foreach ($data['produk_populer'] as $pop_produk) : ?>
                    <div class="col-lg-4 col-md-6 col-sm-6 product-item"
                        data-product-id="<?php echo htmlspecialchars($pop_produk['ID_Produk']); ?>"
                        data-name="<?php echo htmlspecialchars($pop_produk['nama_produk']); ?>"
                        data-price="<?php echo htmlspecialchars($pop_produk['harga_produk']); ?>"
                        data-image="<?php echo BASEURL . "assets/" . htmlspecialchars($pop_produk['gambar_produk']); ?>"
                        data-description="<?php echo htmlspecialchars($pop_produk['detail_produk'] ?? 'Deskripsi tidak tersedia.'); ?>">
                        <div class="product-card-shop" data-bs-toggle="modal" data-bs-target="#productDetailModal" style="cursor:pointer;">
                            <div class="product-image-placeholder">
                                <img src="<?php echo BASEURL . "assets/" . htmlspecialchars($pop_produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($pop_produk['nama_produk']); ?>" class="img-fluid">
                            </div>
                            <div class="product-info-shop">
                                <h5 class="product-name-shop"><?php echo htmlspecialchars($pop_produk['nama_produk']); ?></h5>
                                <p class="product-price-shop"><?php echo formatCurrency($pop_produk['harga_produk']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12">
                    <p class="text-center text-muted">Tidak ada produk unggulan yang dapat ditampilkan saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="services-menu-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-5">Layanan Kami</h2>
        <p class="text-center text-muted mb-5">Kami menyediakan berbagai layanan profesional untuk kucing kesayangan Anda. Pilih yang terbaik!</p>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="service-category-card">
                    <h3 class="service-category-title text-center mb-4">Grooming</h3>
                    <ul class="list-unstyled service-list">
                        <li class="service-item">
                            <span class="service-name">Mandi & Kering (Standar)</span>
                            <span class="service-price">Rp 50.000</span>
                        </li>
                        <li class="service-item">
                            <span class="service-name">Full Groom (Mandi, Kering, Cukur, Potong Kuku)</span>
                            <span class="service-price">Rp 120.000</span>
                        </li>
                        <li class="service-item">
                            <span class="service-name">Potong Kuku</span>
                            <span class="service-price">Rp 20.000</span>
                        </li>
                        <li class="service-item">
                            <span class="service-name">Pembersihan Telinga</span>
                            <span class="service-price">Rp 15.000</span>
                        </li>
                        <li class="service-item">
                            <span class="service-name">Anti Kutu & Jamur (dengan Mandi)</span>
                            <span class="service-price">Rp 80.000</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="service-category-card">
                    <h3 class="service-category-title text-center mb-4">Penitipan (Boarding)</h3>
                    <ul class="list-unstyled service-list">
                        <li class="service-item">
                            <span class="service-name">Standard Stay (per hari)</span>
                            <span class="service-price">Rp 30.000</span>
                        </li>
                        <li class="service-item">
                            <span class="service-name">Pemberian Obat Harian (tambahan per hari per kucing)</span>
                            <span class="service-price">Rp 5.000</span>
                        </li>
                        <li class="service-item">
                            <span class="service-name">Home Service (biaya tambahan)</span>
                            <span class="service-price">Rp 40.000</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="<?php echo BASEURL; ?>grooming" class="btn hero-button me-3">Pesan Grooming Sekarang</a>
            <a href="<?php echo BASEURL; ?>penitipan" class="btn hero-button">Pesan Penitipan Sekarang</a>
        </div>
    </div>
</section>

<section id="about-us-section" class="about-store-section py-5">
    <div class="container">
        <h2 class="section-title-contact text-start mb-4">About our store</h2>
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <p class="about-text">
                    Selamat datang di Cat Shop! Kami adalah surga bagi para pecinta kucing dan sahabat berbulu mereka. Berawal dari kecintaan kami yang mendalam terhadap kucing, kami berkomitmen untuk menyediakan semua kebutuhan kucing Anda, mulai dari makanan berkualitas tinggi, mainan yang menyenangkan, hingga aksesoris yang stylish dan nyaman. Kami percaya bahwa setiap kucing berhak mendapatkan yang terbaik.
                </p>
            </div>
            <div class="col-lg-6">
                <p class="about-text">
                    Di Cat Shop, kami tidak hanya menjual produk, tetapi juga membangun komunitas. Tim kami terdiri dari pecinta kucing yang selalu siap membantu Anda menemukan produk yang paling tepat dan memberikan saran perawatan terbaik. Kami selalu berusaha untuk menghadirkan produk-produk inovatif dan layanan terpercaya agar pengalaman berbelanja Anda menyenangkan dan kucing kesayangan Anda selalu sehat serta bahagia.
                </p>
            </div>
        </div>
        <div class="row stats-row mt-5">
            <div class="col-6 col-md-3 stat-item">
                <span class="stat-number">100+</span>
                <span class="stat-label">Happy Clients</span>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <span class="stat-number">3</span>
                <span class="stat-label">Brand</span>
            </div>
            <div class="col-6 col-md-3 stat-item mt-4 mt-md-0">
                <span class="stat-number">12</span>
                <span class="stat-label">Produk</span>
            </div>
            <div class="col-6 col-md-3 stat-item mt-4 mt-md-0">
                <span class="stat-number">1</span>
                <span class="stat-label">Years in business</span>
            </div>
        </div>
    </div>
</section>

<section class="our-team-section py-5 bg-light-contact">
    <div class="container">
        <h2 class="section-title-contact text-center mb-5">Our Team</h2>
        <div class="row justify-content-center g-4">
            <div class="col-md-6 col-lg-4">
                <div class="team-member-card">
                    <div class="team-member-img-placeholder yellow-bg">
                        <img src="<?php echo BASEURL; ?>assets/foto Rhyno.png" alt="Foto Rhyno Fairuz Melin" class="team-photo-small">
                    </div>
                    <div class="team-member-info">
                        <h5 class="team-member-name">Rhyno Fairuz Melin</h5>
                        <p class="team-member-role">Leader: Backend</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="team-member-card">
                    <div class="team-member-img-placeholder blue-bg">
                        <img src="<?php echo BASEURL; ?>assets/foto Hata.jpg" alt="Foto Muhamad Hata" class="team-photo-small">
                    </div>
                    <div class="team-member-info">
                        <h5 class="team-member-name">Muhamad Hata</h5>
                        <p class="team-member-role">UI/UX, Frontend</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="team-member-card">
                    <div class="team-member-img-placeholder red-bg">
                        <img src="<?php echo BASEURL; ?>assets/foto Raihan.jpg" alt="Foto Raihan Fajar Ramdani" class="team-photo-small">
                    </div>
                    <div class="team-member-info">
                        <h5 class="team-member-name">Raihan Fajar Ramdani</h5>
                        <p class="team-member-role">Frontend</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="instagram-section py-5">
    <div class="container">
        <h2 class="section-title-contact text-center mb-4">Follow Our Team on Instagram</h2>
        <div class="d-flex justify-content-center align-items-center mt-4">
            <a href="https://www.instagram.com/rhy.n_n.o?igsh=YXNmaWE0ZHgzbDA3" target="_blank" class="instagram-profile-link mx-3" aria-label="Instagram Rhyno Fairuz Melin">
                <i class="bi bi-instagram"></i>
                <span class="team-member-instagram-name">Rhyno F.M.</span>
            </a>
            <a href="https://www.instagram.com/ini_hata/" target="_blank" class="instagram-profile-link mx-3" aria-label="Instagram Muhamad Hata">
                <i class="bi bi-instagram"></i>
                <span class="team-member-instagram-name">Muhamad Hata</span>
            </a>
            <a href="https://www.instagram.com/raihanfajar_r/?hl=en" target="_blank" class="instagram-profile-link mx-3" aria-label="Instagram Raihan Fajar Ramdani">
                <i class="bi bi-instagram"></i>
                <span class="team-member-instagram-name">Raihan F.R.</span>
            </a>
        </div>
    </div>
</section>