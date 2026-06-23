<?php
require_once 'config/config.php';
require_once 'templates/header.php';
require_once 'classes/Cart.php';

$cart = new Cart();

if(isset($_POST['remove'])) {
    $cart->remove($_POST['cart_id'], $_SESSION['user_id']);
    header('Location: cart.php');
    exit;
}

$myCart = $cart->getUserCart($_SESSION['user_id']);
?>

<h2>Keranjang</h2>

<?php if(empty($myCart)): ?>
    <div class="empty-state">
        <p>Keranjangmu masih kosong.</p>
        <a href="index.php" class="btn btn-secondary">Jelajahi Game</a>
    </div>
<?php else: ?>
    <div class="cart-wrapper">
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
                <?php
                $no = 1;
                $total = 0;
                foreach($myCart as $item):
                    $total += $item['price'];
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit" name="remove" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total</span>
                Rp <?php echo number_format($total, 0, ',', '.'); ?>
            </div>
            <a href="checkout.php" class="btn">Checkout</a>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>