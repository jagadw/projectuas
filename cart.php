<?php
require_once 'config/config.php';
require_once 'templates/header.php';
require_once 'classes/Cart.php';

$cart = new Cart();

if(isset($_POST['remove'])) {
    $cart->remove($_POST['cart_id'], $_SESSION['user_id']);
}

$myCart = $cart->getUserCart($_SESSION['user_id']);
?>

<h2>Keranjang Belanja Kamu</h2>

<?php if(empty($myCart)): ?>
    <p>Keranjangnya masih kosong nih, ayo pilih game di halaman Home!</p>
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
                        <button type="submit" name="remove" class="btn btn-danger">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2">Total Pembayaran</th>
                <th colspan="2">Rp <?php echo number_format($total, 0, ',', '.'); ?></th>
            </tr>
        </tfoot>
    </table>
    <br>
    <button class="btn" onclick="alert('TODOOOOOOO')">Checkout Sekarang</button>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
