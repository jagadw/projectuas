<?php
require_once 'config/config.php';
require_once 'templates/header.php';
require_once 'classes/Game.php';
require_once 'classes/Cart.php';
require_once 'classes/Favorite.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$gameObj = new Game();
$games = $gameObj->getAllGames();
$game = null;

foreach($games as $g) {
    if($g['id'] == $_GET['id']) {
        $game = $g;
        break;
    }
}

if(!$game) {
    header("Location: index.php");
    exit;
}

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

$imgSrc = !empty($game['image']) ? "public/assets/" . htmlspecialchars($game['image']) : "https://picsum.photos/seed/" . ($game['id'] + 50) . "/600/400";
$categories = ['Action', 'RPG', 'Strategy', 'Sports', 'Racing'];
$gameCategory = $categories[($game['id'] - 1) % count($categories)];
?>

<a href="index.php" class="back-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
    Kembali ke Daftar Game
</a>

<?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>

<div class="detail-wrapper">
    <div class="detail-image">
        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" onerror="this.src='https://picsum.photos/seed/<?php echo ($game['id'] + 50); ?>/600/400'">
    </div>

    <div class="detail-info">
        <div class="detail-badges">
            <span class="badge"><?php echo $gameCategory; ?></span>
            <?php if(!empty($game['platform'])): ?>
                <span class="badge badge-platform"><?php echo htmlspecialchars($game['platform']); ?></span>
            <?php endif; ?>
        </div>

        <h1><?php echo htmlspecialchars($game['title']); ?></h1>

        <p class="detail-desc"><?php echo htmlspecialchars($game['description']); ?></p>

        <p class="detail-price">
            Rp <?php echo number_format($game['price'], 0, ',', '.'); ?>
        </p>

        <form method="POST" class="detail-actions">
            <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
            <button type="submit" name="add_cart" class="btn">+ Tambah ke Keranjang</button>
            <button type="submit" name="add_fav" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Simpan Favorit
            </button>
        </form>

        <div class="detail-meta">
            <?php if(!empty($game['platform'])): ?>
            <div class="detail-meta-row">
                <span>Platform</span>
                <span><?php echo htmlspecialchars($game['platform']); ?></span>
            </div>
            <?php endif; ?>
            <div class="detail-meta-row">
                <span>Kategori</span>
                <span><?php echo $gameCategory; ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
