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
    $msg = "Mantap, game masuk ke keranjang!";
}

if(isset($_POST['add_fav'])) {
    $fav = new Favorite();
    $fav->addToFavorite($_SESSION['user_id'], $_POST['game_id']);
    $msg = "Woke, game udah disimpen ke favorit!";
}
?>

<h2>Daftar Game Redeem Code</h2>

<?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>

<div class="grid-container">
    <?php foreach($games as $g): ?>
        <div class="card">
            <div class="card-img">
                <?php if(!empty($g['image'])): ?>
                    <img src="public/assets/<?php echo htmlspecialchars($g['image']); ?>" alt="Gambar Game" onerror="this.src='https://via.placeholder.com/200?text=No+Image'">
                <?php else: ?>
                    <div style="background:#ddd; height:150px; text-align:center; line-height:150px;">No Image</div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <h3><?php echo htmlspecialchars($g['title']); ?></h3>
                <p class="desc"><?php echo htmlspecialchars($g['description']); ?></p>
                <h4 class="price">Rp <?php echo number_format($g['price'], 0, ',', '.'); ?></h4>
                
                <form method="POST" class="btn-group">
                    <input type="hidden" name="game_id" value="<?php echo $g['id']; ?>">
                    <button type="submit" name="add_cart" class="btn">Tambah ke Keranjang</button>
                    <button type="submit" name="add_fav" class="btn btn-love">♥ Favorit</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
