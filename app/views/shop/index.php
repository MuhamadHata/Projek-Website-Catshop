<?php // app/views/shop/index.php 
// Pastikan fungsi formatCurrency sudah tersedia
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
// Variabel $data['current_kategori'] dan $data['current_keyword'] sudah di-set oleh ShopController
// dan akan digunakan langsung untuk mengisi value form filter utama.
// Tidak perlu $active_kategori_from_url lagi di sini.
?>
<section class="hero-shop-section">
    <div class="blob-1"></div>
    <div class="blob-2"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 hero-shop-content">
                <p class="shop-subheading">Cat Shop</p>
                <h1 class="shop-title">Toko kucing yang ramah dan penuh perhatian</h1>
            </div>
            <div class="col-lg-6 hero-shop-image-container">
                <img src="<?php echo BASEURL; ?>assets/gambar kucing 3.png" alt="Happy Cat" class="img-fluid hero-cat-image">
            </div>
        </div>
    </div>
</section>

<main class="shop-main-content py-5">
    <div class="container">
        <?php Flasher::flash(); ?>
        <div class="row">
            <aside class="col-lg-3 popular-products-sidebar-custom mb-4 mb-lg-0">
                <div class="sidebar-sticky-content">
                    <h4 class="sidebar-title">✨ Produk Populer</h4>
                    <?php if (!empty($data['produk_populer'])): ?>
                        <ul class="list-unstyled popular-products-list">
                            <?php foreach ($data['produk_populer'] as $pop_produk): ?>
                                <li class="popular-product-item-custom product-item"
                                    data-product-id="<?php echo htmlspecialchars($pop_produk['ID_Produk']); ?>"
                                    data-name="<?php echo htmlspecialchars($pop_produk['nama_produk']); ?>"
                                    data-price="<?php echo htmlspecialchars($pop_produk['harga_produk']); ?>"
                                    data-image="<?php echo BASEURL . "assets/" . htmlspecialchars($pop_produk['gambar_produk']); ?>"
                                    data-description="<?php echo htmlspecialchars($pop_produk['detail_produk'] ?? 'Deskripsi tidak tersedia.'); ?>"
                                    data-stock="<?php echo htmlspecialchars($pop_produk['stok_produk'] ?? 0); ?>">
                                    <div class="popular-product-clickable-area" data-bs-toggle="modal" data-bs-target="#productDetailModal" style="cursor: pointer; display: flex; align-items: center; width: 100%;">
                                        <img src="<?php echo BASEURL . "assets/" . htmlspecialchars($pop_produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($pop_produk['nama_produk']); ?>" class="popular-product-img">
                                        <div class="popular-product-info">
                                            <span class="name"><?php echo htmlspecialchars($pop_produk['nama_produk']); ?></span>
                                            <span class="price"><?php echo formatCurrency($pop_produk['harga_produk']); ?></span>
                                            <span class="stock" style="font-size: 0.8em; color: #555;">Stok: <?php echo htmlspecialchars($pop_produk['stok_produk'] ?? 0); ?></span>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">Tidak ada produk populer saat ini.</p>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="col-lg-9 all-products-area" id="allProductsTitleAnchor">
                <h3 class="all-products-title mb-3">Semua Produk Kami</h3>

                <form action="<?php echo BASEURL; ?>shop" method="POST" id="filterForm" class="mb-4 p-3 border rounded bg-light">
                    <div class="d-flex flex-nowrap align-items-end gap-3">
                        <div class="flex-grow-1">
                            <label for="keyword" class="form-label">Cari Produk </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="keyword" name="keyword" placeholder="(Nama Produk)" value="<?php echo htmlspecialchars($data['current_keyword'] ?? ''); ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="ms-auto">
                            <label for="kategori" class="form-label">Filter</label>
                            <select class="form-select" id="kategori" name="kategori" style="min-width: 20vw;">
                                <option value="">Kategori</option>
                                <?php if (!empty($data['kategori_list'])): ?>
                                    <?php foreach ($data['kategori_list'] as $kategori_item): ?>
                                        <option value="<?php echo htmlspecialchars($kategori_item); ?>"
                                            <?php echo (isset($data['current_kategori']) && $data['current_kategori'] == $kategori_item) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars(ucfirst($kategori_item)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="product-grid-container" id="productGridContainer" style="height: 80vh; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 8px;">
                    <div class="row g-4" id="productGrid">
                        <?php if (!empty($data['produk_all'])): ?>
                            <?php foreach ($data['produk_all'] as $produk): ?>
                                <div class="col-6 col-md-4 product-item"
                                    data-product-id="<?php echo htmlspecialchars($produk['ID_Produk']); ?>"
                                    data-name="<?php echo htmlspecialchars($produk['nama_produk']); ?>"
                                    data-price="<?php echo htmlspecialchars($produk['harga_produk']); ?>"
                                    data-image="<?php echo BASEURL . "assets/" . htmlspecialchars($produk['gambar_produk']); ?>"
                                    data-description="<?php echo htmlspecialchars($produk['detail_produk'] ?? 'Deskripsi tidak tersedia.'); ?>"
                                    data-stock="<?php echo htmlspecialchars($produk['stok_produk'] ?? 0); ?>">
                                    <div class="product-card-shop" data-bs-toggle="modal" data-bs-target="#productDetailModal">
                                        <div class="product-image-placeholder">
                                            <img src="<?php echo BASEURL . "assets/" . htmlspecialchars($produk['gambar_produk']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="img-fluid">
                                        </div>
                                        <div class="product-info-shop">
                                            <h5 class="product-name-shop"><?php echo htmlspecialchars($produk['nama_produk']); ?></h5>
                                            <p class="product-price-shop"><?php echo formatCurrency($produk['harga_produk']); ?></p>
                                            <p class="product-stock-shop" style="font-size: 0.9em; color: #333;">Stok: <?php echo htmlspecialchars($produk['stok_produk'] ?? 0); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center mt-4">
                                    <?php if (!empty($data['current_keyword']) || !empty($data['current_kategori'])): ?>
                                        Produk yang cocok dengan pencarian/filter tidak ditemukan.
                                    <?php else: ?>
                                        Tidak ada produk yang tersedia saat ini.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<div class="modal fade" id="productDetailModal" tabindex="-1" aria-labelledby="productDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailModalLabel">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 d-flex justify-content-center align-items-center">
                        <img src="" class="img-fluid" alt="Product Image" id="modalProductImage" style="max-height: 300px; object-fit: contain;">
                    </div>
                    <div class="col-md-7">
                        <h2 id="modalProductName">Nama Produk</h2>
                        <p id="modalProductPrice" class="fs-4 fw-bold text-primary">Harga</p>
                        <p id="modalProductStock" class="fs-5">Stok: <span id="stockValue"></span></p>
                        <p id="modalProductDescription">Deskripsi produk akan muncul di sini.</p>

                        <form action="<?php echo BASEURL; ?>keranjang/add" method="POST" id="addToCartFormInModal">
                            <input type="hidden" name="produk_id" id="modalProdukId" value="">
                            <input type="hidden" name="jumlah" value="1">
                            <input type="hidden" name="source_page" value="shop">
                            
                            <input type="hidden" name="active_kategori" id="modalActiveKategori" value="">
                            <input type="hidden" name="active_keyword" id="modalActiveKeyword" value="">

                            <button type="submit" class="btn add-to-cart-btn" id="addToCartBtnInModal">
                                <i class="bi bi-cart-plus-fill"></i> Tambahkan ke Keranjang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var productDetailModalEl = document.getElementById('productDetailModal');
    var productGridContainer = document.getElementById('productGridContainer');
    var kategoriSelect = document.getElementById('kategori'); // Select kategori utama
    var keywordInput = document.getElementById('keyword'); // Input keyword utama
    var filterForm = document.getElementById('filterForm'); // Form filter utama

    function saveState() {
        sessionStorage.setItem('scrollPositionShopPage', window.scrollY);
        if (productGridContainer) {
            sessionStorage.setItem('scrollPositionProductGrid', productGridContainer.scrollTop);
        }
        // Tidak perlu menyimpan keyword/kategori ke session storage untuk tujuan reload ini,
        // karena akan ditangani oleh parameter URL.
        // sessionStorage.setItem('selectedKategoriShop', kategoriSelect.value); // Baris ini bisa dipertimbangkan jika ingin persistensi saat F5 biasa
    }

    function restoreState() {
        const scrollPage = sessionStorage.getItem('scrollPositionShopPage');
        if (scrollPage) {
            window.scrollTo(0, parseInt(scrollPage));
        }

        const scrollGrid = sessionStorage.getItem('scrollPositionProductGrid');
        if (productGridContainer && scrollGrid) {
            productGridContainer.scrollTop = parseInt(scrollGrid);
        }
        
        // Kategori dan Keyword sudah di-set oleh PHP dari $data['current_kategori'] dan $data['current_keyword']
        // yang sumbernya dari URL (setelah redirect) atau POST (setelah filter).
        // const selectedKategori = sessionStorage.getItem('selectedKategoriShop');
        // if (kategoriSelect && selectedKategori) {
        //     kategoriSelect.value = selectedKategori;
        // }
    }

    restoreState();

    if (productDetailModalEl) {
        productDetailModalEl.addEventListener('show.bs.modal', function(event) {
            var cardClicked = event.relatedTarget;
            var productItem = cardClicked.closest('.product-item');
            
            if (productItem) {
                var productId = productItem.dataset.productId;
                var productName = productItem.dataset.name;
                var productPrice = productItem.dataset.price;
                var productImage = productItem.dataset.image;
                var productDescription = productItem.dataset.description;
                var productStock = productItem.dataset.stock;

                document.getElementById('modalProductImage').src = productImage;
                document.getElementById('modalProductImage').alt = productName;
                document.getElementById('modalProductName').textContent = productName;
                document.getElementById('modalProductDescription').textContent = productDescription;
                document.getElementById('stockValue').textContent = productStock;
                document.getElementById('modalProdukId').value = productId;

                // Mengisi hidden input di form modal dengan nilai terkini dari filter utama
                if (kategoriSelect) {
                    document.getElementById('modalActiveKategori').value = kategoriSelect.value;
                }
                if (keywordInput) {
                    document.getElementById('modalActiveKeyword').value = keywordInput.value;
                }

                let priceNumber = parseFloat(productPrice);
                if (!isNaN(priceNumber)) {
                    document.getElementById('modalProductPrice').textContent = 'Rp ' + priceNumber.toLocaleString('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    });
                } else {
                    document.getElementById('modalProductPrice').textContent = 'Harga tidak tersedia';
                }

                var addToCartBtnForm = document.getElementById('addToCartBtnInModal');
                if (parseInt(productStock) === 0) {
                    addToCartBtnForm.disabled = true;
                    addToCartBtnForm.innerHTML = '<i class="bi bi-x-circle-fill"></i> Stok Habis';
                } else {
                    addToCartBtnForm.disabled = false;
                    addToCartBtnForm.innerHTML = '<i class="bi bi-cart-plus-fill"></i> Tambahkan ke Keranjang';
                }
            }
        });
    }

    var addToCartFormInModal = document.getElementById('addToCartFormInModal');
    if (addToCartFormInModal) {
        addToCartFormInModal.addEventListener('submit', function() {
            saveState(); // Simpan posisi scroll sebelum submit
        });
    }

    if (filterForm) {
        filterForm.addEventListener('submit', function() {
            saveState(); // Simpan posisi scroll sebelum submit filter
            // Tidak perlu hapus sessionStorage di sini karena halaman akan reload dengan filter baru
        });
    }

    if (kategoriSelect) {
        kategoriSelect.addEventListener('change', function() {
            // saveState() akan dipanggil oleh event submit filterForm jika form di-submit otomatis
            if (filterForm) filterForm.submit();
        });
    }

    <?php if (isset($data['is_filtered_request']) && $data['is_filtered_request'] === true): ?>
        // Hanya scroll jika ini adalah hasil filter DAN tidak ada posisi scroll yang dipulihkan dari add to cart
        // (Karena add to cart punya mekanisme scroll sendiri via restoreState)
        const scrollPageRestored = sessionStorage.getItem('scrollPositionShopPage');
        const scrollGridRestored = sessionStorage.getItem('scrollPositionProductGrid');

        if (!scrollPageRestored && !scrollGridRestored) {
             // Jika BUKAN karena kembali dari keranjang (dimana scroll sudah di-handle)
             // DAN ini adalah request hasil filter/pencarian.
            var allProductsTitleElement = document.getElementById('allProductsTitleAnchor');
            if (allProductsTitleElement) {
                // Beri sedikit delay agar rendering selesai sebelum scroll
                setTimeout(function() {
                    allProductsTitleElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start' 
                    });
                }, 100); // delay 100ms
            }
        }
    <?php endif; ?>

    var pageLinks = document.querySelectorAll('a:not([data-bs-toggle="modal"]):not([href^="#"]):not(.popular-product-clickable-area)');
    pageLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            // Hanya hapus jika link BUKAN bagian dari form filter atau aksi keranjang
            // Untuk link navigasi biasa
            const formElement = event.target.closest('form');
            if (!formElement || (formElement.id !== 'filterForm' && formElement.id !== 'addToCartFormInModal')) {
                sessionStorage.removeItem('scrollPositionShopPage');
                sessionStorage.removeItem('scrollPositionProductGrid');
                // sessionStorage.removeItem('selectedKategoriShop'); // Jika menggunakan ini
            }
        });
    });
    
    // Hapus sessionStorage scroll SETELAH berhasil di-restore dan digunakan.
    // Ini memastikan F5 biasa tidak akan scroll, tapi kembali dari keranjang/filter akan scroll.
    const scrollPageRestoredAfterLoad = sessionStorage.getItem('scrollPositionShopPage');
    const scrollGridRestoredAfterLoad = sessionStorage.getItem('scrollPositionProductGrid');

    if (scrollPageRestoredAfterLoad) {
      sessionStorage.removeItem('scrollPositionShopPage');
    }
    if (scrollGridRestoredAfterLoad) {
      sessionStorage.removeItem('scrollPositionProductGrid');
    }
    // Jika Anda menggunakan selectedKategoriShop untuk F5, jangan hapus di sini.
    // const kategoriRestored = sessionStorage.getItem('selectedKategoriShop');
    // if (kategoriRestored) sessionStorage.removeItem('selectedKategoriShop');


});
</script>