<?php
require_once 'config/config.php';
require_once 'templates/header.php';
require_once 'classes/Game.php';
require_once 'classes/Cart.php';
require_once 'classes/Favorite.php';

$gameObj = new Game();
$games = $gameObj->getAllGames();

$msg = '';

if(isset($_POST['add_cart'])) {
    $cart = new Cart();
    $cart->addToCart($_SESSION['user_id'], $_POST['game_id']);
    $msg = "Game berhasil ditambahkan ke keranjang!";
}

if(isset($_POST['add_fav'])) {
    $fav = new Favorite();
    $fav->addToFavorite($_SESSION['user_id'], $_POST['game_id']);
    $msg = "Game berhasil disimpan ke favorit!";
}

$categories = ['Action', 'RPG', 'Strategy', 'Sports', 'Racing'];
$platforms = ['PC', 'XBOX', 'Playstation'];
?>

<div class="home-wrapper">
    
    <aside class="home-sidebar">
        <div class="sidebar-section">
            <h4>Kategori</h4>
            <div class="filter-tags">
                <button class="filter-btn active" data-filter="category" data-value="all">Semua</button>
                <?php foreach($categories as $cat): ?>
                    <button class="filter-btn" data-filter="category" data-value="<?php echo $cat; ?>"><?php echo $cat; ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="sidebar-section">
            <h4>Platform</h4>
            <div class="filter-tags">
                <button class="filter-btn active" data-filter="platform" data-value="all">Semua</button>
                <?php foreach($platforms as $plat): ?>
                    <button class="filter-btn" data-filter="platform" data-value="<?php echo $plat; ?>"><?php echo $plat; ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="sidebar-section">
            <h4>Harga</h4>
            <div class="filter-tags price-filter">
                <button class="filter-btn active" data-filter="price" data-value="all">Semua Harga</button>
                <button class="filter-btn" data-filter="price" data-value="under100k">Di bawah 100rb</button>
                <button class="filter-btn" data-filter="price" data-value="100k-250k">100rb - 250rb</button>
                <button class="filter-btn" data-filter="price" data-value="above250k">Di atas 250rb</button>
            </div>
        </div>
    </aside>

    <main class="home-main">
        <div class="page-header">
            <h2>Daftar Game</h2>
            <span class="result-count" id="resultCount"><?php echo count($games); ?> produk</span>
        </div>

        <?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>

        <div class="grid-container" id="productGrid">
            <?php foreach($games as $index => $g): 
                if ($g['price'] == 0) continue; 
                $randomCategory = $categories[$index % count($categories)];
                $imgSrc = !empty($g['image']) ? "public/uploads/" . htmlspecialchars($g['image']) : "public/uploads/default.jpg";
                $gamePlatform = !empty($g['platform']) ? $g['platform'] : 'PC';
            ?>
                <div class="card" data-category="<?php echo $randomCategory; ?>" data-platform="<?php echo $gamePlatform; ?>" data-price="<?php echo $g['price']; ?>">
                    <a href="detail.php?id=<?php echo $g['id']; ?>" class="card-link">
                        <div class="card-img">
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($g['title']); ?>" onerror="this.src='public/uploads/default.jpg'">
                        </div>
                        <div class="card-body">
                            <div>
                                <span class="badge"><?php echo $randomCategory; ?></span>
                                <span class="badge badge-platform"><?php echo htmlspecialchars($gamePlatform); ?></span>
                            </div>
                            <h3><?php echo htmlspecialchars($g['title']); ?></h3>
                            <p class="desc"><?php echo htmlspecialchars($g['description']); ?></p>
                            <p class="price">
                                Rp <?php echo number_format($g['price'], 0, ',', '.'); ?>
                            </p>
                        </div>
                    </a>
                    <div class="card-body" style="padding-top:0">
                        <div class="card-actions">
                            <form method="POST" style="flex:1;display:flex;gap:6px">
                                <input type="hidden" name="game_id" value="<?php echo $g['id']; ?>">
                                <button type="submit" name="add_cart" class="btn btn-outline-cyan">+ Keranjang</button>
                                <button type="submit" name="add_fav" class="btn btn-love">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

</div>

<script>
    const activeFilters = { category: 'all', platform: 'all', price: 'all' };
    const cards = document.querySelectorAll('.card');

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const filterType = btn.getAttribute('data-filter');
            const filterValue = btn.getAttribute('data-value');
            
            activeFilters[filterType] = filterValue;

            btn.closest('.filter-tags').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            let visibleCount = 0;
            cards.forEach(card => {
                const catMatch = activeFilters.category === 'all' || card.getAttribute('data-category') === activeFilters.category;
                const platMatch = activeFilters.platform === 'all' || card.getAttribute('data-platform') === activeFilters.platform;
                
                const price = parseInt(card.getAttribute('data-price'));
                let priceMatch = true;
                if(activeFilters.price === 'under100k') priceMatch = price > 0 && price < 100000;
                else if(activeFilters.price === '100k-250k') priceMatch = price >= 100000 && price <= 250000;
                else if(activeFilters.price === 'above250k') priceMatch = price > 250000;

                if(catMatch && platMatch && priceMatch) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('resultCount').textContent = visibleCount + ' produk';
        });
    });
</script>

<?php require_once 'templates/footer.php'; ?>
