<footer class="footer-section pt-5 pb-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <img src="<?php echo BASEURL; ?>assets/Logo.png" alt="Cat Shop Logo" class="footer-logo mb-3">
                <p class="footer-description">Tempat terbaik untuk memenuhi semua kebutuhan kucing kesayangan Anda. Dari makanan sehat, mainan lucu, furnitur nyaman, hingga layanan grooming. Semua tersedia di sini!</p>
            </div>
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Team 8</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo BASEURL; ?>about">About Us</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                <h5 class="footer-title">Customer Service</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo BASEURL; ?>contact">Contact Us</a></li>
                    <li><a href="<?php echo BASEURL; ?>chatbot">CS Bot</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-12">
                <h5 class="footer-title">Store</h5>
                <ul class="list-unstyled footer-contact">
                    <li><i class="bi bi-geo-alt-fill"></i> Jl. Pendidikan No.15, Cibiru Wetan, Kec. Cileunyi, Kabupaten Bandung, Jawa Barat 40625</li>
                    <li><i class="bi bi-telephone-fill"></i> 0858-3456-2375</li>
                    <li><i class="bi bi-envelope-fill"></i> support@catshop.com</li>
                </ul>
            </div>
        </div>
        <hr class="footer-hr">
        <div class="row align-items-center">
            <div class="col-12 text-center">
                <p class="copyright-text mb-2 mb-md-0">©Copyright Cat Shop. 2025</p>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.style.opacity = '0';
        setTimeout(() => {
            document.body.style.transition = 'opacity 0.5s ease-in-out';
            document.body.style.opacity = '1';
        }, 50);

        const heroContent = document.querySelector('.hero-content');
        const heroImageContainer = document.querySelector('.hero-image-container');

        setTimeout(() => {
            if (heroContent) heroContent.classList.add('visible');
            if (heroImageContainer) heroImageContainer.classList.add('visible');
        }, 100);
    });
</script>

</body>

</html>