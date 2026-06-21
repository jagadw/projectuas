<?php
require_once 'config/config.php';
require_once 'templates/header.php';
require_once 'classes/Favorite.php';
require_once 'classes/Cart.php';

$fav = new Favorite();
$cart = new Cart();
$msg = '';

if(isset($_POST['remove'])) {
    $fav->remove($_POST['fav_id'], $_SESSION['user_id']);
}

if(isset($_POST['to_cart'])) {
    $cart->addToCart($_SESSION['user_id'], $_POST['game_id']);
    $fav->remove($_POST['fav_id'], $_SESSION['user_id']);
    $msg = "Sip, game udah dipindahin ke keranjang!";
}

$myFav = $fav->getUserFavorites($_SESSION['user_id']);
?>

<h2>Game Favorit Kamu</h2>
<?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>

<?php if(empty($myFav)): ?>
    <p>Belum ada game yang kamu sukai nih.</p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Game</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach($myFav as $item): ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                <td>
                    <form method="POST" style="display:flex; gap:10px;">
                        <input type="hidden" name="fav_id" value="<?php echo $item['fav_id']; ?>">
                        <input type="hidden" name="game_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" name="to_cart" class="btn">Masukin Keranjang</button>
                        <button type="submit" name="remove" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
