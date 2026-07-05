</div>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-col">
            <h5>Seedem</h5>
            <p>Marketplace redeem code game terpercaya. Beli game original dengan harga terjangkau dan dapatkan kode langsung ke library kamu.</p>
        </div>
        <div class="footer-col">
            <h5>Navigasi</h5>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="library.php">Library Key</a></li>
                <li><a href="history.php">History</a></li>
                <li><a href="#">Bantuan</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h5>Akun</h5>
            <ul>
                <li><a href="cart.php">Keranjang</a></li>
                <li><a href="favorite.php">Favorit</a></li>
                <li><a href="history.php">Feedback</a></li>
                <li><a href="logout.php">Keluar</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> Seedem</span>
        <span>Project UAS</span>
    </div>
</footer>

<script>
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const closeMenuBtn = document.getElementById('closeMenuBtn');
    const mobileNav = document.getElementById('mobileNav');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if(hamburgerBtn) {
        hamburgerBtn.addEventListener('click', () => {
            mobileNav.classList.add('show');
            mobileOverlay.classList.add('show');
        });
    }

    if(closeMenuBtn) {
        closeMenuBtn.addEventListener('click', () => {
            mobileNav.classList.remove('show');
            mobileOverlay.classList.remove('show');
        });
    }

    if(mobileOverlay) {
        mobileOverlay.addEventListener('click', () => {
            mobileNav.classList.remove('show');
            mobileOverlay.classList.remove('show');
        });
    }
</script>

</body>
</html>
