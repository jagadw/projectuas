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
} elseif (isset($_POST['remove_cart'])) {
    $cart = new Cart();
    $cart->removeByGameId($_POST['game_id'], $_SESSION['user_id']);
    $msg = "Game berhasil dihapus dari keranjang!";
}

if(isset($_POST['add_fav'])) {
    $fav = new Favorite();
    $fav->addToFavorite($_SESSION['user_id'], $_POST['game_id']);
    $msg = "Game berhasil disimpan ke favorit!";
} elseif (isset($_POST['remove_fav'])) {
    $fav = new Favorite();
    $fav->removeByGameId($_POST['game_id'], $_SESSION['user_id']);
    $msg = "Game berhasil dihapus dari favorit!";
}

$categories = $gameObj->getAllGenres();
$platforms = ['PC', 'XBOX', 'Playstation', 'Multiplatform'];

$userCartIds = [];
$userFavIds = [];
if(isset($_SESSION['user_id'])) {
    $cartTmp = new Cart();
    foreach($cartTmp->getUserCart($_SESSION['user_id']) as $c) $userCartIds[] = $c['id'];
    
    $favTmp = new Favorite();
    foreach($favTmp->getUserFavorites($_SESSION['user_id']) as $f) $userFavIds[] = $f['id'];
}
?>

<style>
.card.out-of-stock {
    opacity: 0.6;
    filter: grayscale(70%);
    transition: all 0.3s ease;
}
.card.out-of-stock:hover {
    opacity: 0.85;
    filter: grayscale(20%);
}
</style>

<div class="home-wrapper">
    
    <aside class="home-sidebar">
        <div class="sidebar-section">
            <h4>Cari Game</h4>
            <input type="text" id="searchInput" placeholder="Ketik judul game..." style="width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; box-sizing: border-box; outline: none; margin-bottom: 5px; font-family: inherit;">
        </div>

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
                $gameCategory = !empty($g['genre_name']) ? $g['genre_name'] : 'Lainnya';
                $imgSrc = !empty($g['image']) ? "public/uploads/" . htmlspecialchars($g['image']) : "public/uploads/default.jpg";
                $gamePlatform = !empty($g['platform']) ? $g['platform'] : 'PC';
                $outOfStock = isset($g['stock']) && $g['stock'] == 0;
            ?>
                <div class="card <?php echo $outOfStock ? 'out-of-stock' : ''; ?>" data-category="<?php echo $gameCategory; ?>" data-platform="<?php echo $gamePlatform; ?>" data-price="<?php echo $g['price']; ?>">
                    <a href="detail.php?id=<?php echo $g['id']; ?>" class="card-link">
                        <div class="card-img">
                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($g['title']); ?>" onerror="this.src='public/uploads/default.jpg'">
                        </div>
                        <div class="card-body">
                            <div>
                                <?php 
                                $genreArray = !empty($g['genre_name']) ? explode(', ', $g['genre_name']) : ['Lainnya'];
                                foreach($genreArray as $gn): 
                                ?>
                                    <span class="badge" style="margin-bottom: 4px; display: inline-block;"><?php echo htmlspecialchars($gn); ?></span>
                                <?php endforeach; ?>
                                <span class="badge badge-platform" style="margin-bottom: 4px; display: inline-block;"><?php echo htmlspecialchars($gamePlatform); ?></span>
                                
                                <?php if (isset($g['stock']) && $g['stock'] > 0): ?>
                                    <span class="badge" style="margin-bottom: 4px; display: inline-block; background-color: rgba(40, 167, 69, 0.15); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.3);">Stok: <?php echo $g['stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge" style="margin-bottom: 4px; display: inline-block; background-color: rgba(220, 53, 69, 0.15); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.3);">Stok Habis</span>
                                <?php endif; ?>
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
                                <?php
                                $inCart = in_array($g['id'], $userCartIds);
                                $inFav = in_array($g['id'], $userFavIds);
                                ?>
                                <input type="hidden" name="game_id" value="<?php echo $g['id']; ?>">
                                <?php if($inCart): ?>
                                    <button type="submit" name="remove_cart" class="btn btn-outline-cyan" style="background:var(--accent);color:white;opacity:0.8;" title="Hapus dari Keranjang">Di Keranjang</button>
                                <?php elseif($outOfStock): ?>
                                    <button type="button" class="btn btn-outline-cyan" style="opacity:0.5;cursor:not-allowed;" title="Stok Habis" disabled>+ Keranjang</button>
                                <?php else: ?>
                                    <button type="submit" name="add_cart" class="btn btn-outline-cyan">+ Keranjang</button>
                                <?php endif; ?>

                                <?php if($inFav): ?>
                                    <button type="submit" name="remove_fav" class="btn btn-love" style="color:var(--pink);border-color:var(--pink);background:rgba(233,61,130,0.08);" title="Hapus dari Favorit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    </button>
                                <?php else: ?>
                                    <button type="submit" name="add_fav" class="btn btn-love">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

</div>

<script>
    const activeFilters = { category: 'all', platform: 'all', price: 'all', search: '' };
    const cards = document.querySelectorAll('.card');

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            activeFilters.search = e.target.value.toLowerCase();
            applyFilters();
        });
    }

    function applyFilters() {
        let visibleCount = 0;
        cards.forEach(card => {
            const cardCategories = card.getAttribute('data-category').split(', ');
            const catMatch = activeFilters.category === 'all' || cardCategories.includes(activeFilters.category);
            const platMatch = activeFilters.platform === 'all' || card.getAttribute('data-platform') === activeFilters.platform;
            
            const price = parseInt(card.getAttribute('data-price'));
            let priceMatch = true;
            if(activeFilters.price === 'under100k') priceMatch = price > 0 && price < 100000;
            else if(activeFilters.price === '100k-250k') priceMatch = price >= 100000 && price <= 250000;
            else if(activeFilters.price === 'above250k') priceMatch = price > 250000;

            const titleElement = card.querySelector('h3');
            const title = titleElement ? titleElement.textContent.toLowerCase() : '';
            const searchMatch = title.includes(activeFilters.search);

            if(catMatch && platMatch && priceMatch && searchMatch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('resultCount').textContent = visibleCount + ' produk';
    }

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const filterType = btn.getAttribute('data-filter');
            const filterValue = btn.getAttribute('data-value');
            
            activeFilters[filterType] = filterValue;

            btn.closest('.filter-tags').querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            applyFilters();
        });
    });
</script>

<?php require_once 'templates/footer.php'; ?>
