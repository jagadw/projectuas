<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/config.php';
require_once 'classes/Cart.php';
require_once 'classes/Promo.php';
require_once 'classes/Transaction.php';

$cart        = new Cart();
$promo       = new Promo();
$transaction = new Transaction();

$userId    = $_SESSION['user_id'];
$cartItems = $cart->getUserCart($userId);

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$error      = '';
$promoData  = null;
$promoCode  = '';
$discount   = 0;

$subtotal = array_sum(array_column($cartItems, 'price'));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_promo'])) {
    header('Content-Type: application/json');
    $code      = trim($_POST['promo_code'] ?? '');
    $promoData = $promo->validateCode($code);
    if ($promoData) {
        $discountAmount = $promo->calcDiscount($promoData, $subtotal);
        echo json_encode([
            'valid'       => true,
            'promo_id'    => $promoData['id'],
            'code'        => $promoData['code'],
            'percent'     => $promoData['discount_percentage'],
            'max'         => $promoData['max_discount'],
            'discount'    => $discountAmount,
            'final_total' => $subtotal - $discountAmount,
        ]);
    } else {
        echo json_encode(['valid' => false, 'message' => 'Kode promo tidak valid atau sudah kedaluwarsa.']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $paymentType = $_POST['payment_type'] ?? '';
    $promoId     = (int) ($_POST['promo_id'] ?? 0) ?: null;
    $finalTotal  = (float) ($_POST['final_total'] ?? $subtotal);

    if (!$paymentType) {
        $error = 'Pilih metode pembayaran terlebih dahulu.';
    } else {
        $txId = $transaction->createFromCart($userId, $cartItems, $finalTotal, $promoId, $paymentType);
        if ($txId) {
            $transaction->clearCart($userId);
            header("Location: transaction.php?id=$txId");
            exit;
        } else {
            $error = 'Gagal memproses transaksi. Stok key mungkin habis, coba lagi.';
        }
    }
}

$pageTitle = 'Checkout';
require_once 'templates/header.php';
?>


<h2>Checkout</h2>

<?php if ($error): ?>
    <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="POST" id="checkoutForm">
    <input type="hidden" name="place_order" value="1">
    <input type="hidden" name="promo_id"    id="hiddenPromoId"    value="0">
    <input type="hidden" name="final_total" id="hiddenFinalTotal" value="<?php echo $subtotal; ?>">

    <div class="checkout-layout">

        <div>
            <div class="checkout-section">
                <h3>Item Pesanan (<?php echo count($cartItems); ?> game)</h3>
                <div class="checkout-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="checkout-item">
                            <span class="checkout-item-title"><?php echo htmlspecialchars($item['title']); ?></span>
                            <span class="checkout-item-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="checkout-section">
                <h3>Kode Promo</h3>
                <div class="promo-row">
                    <input type="text" id="promoInput" placeholder="Masukkan kode promo" style="text-transform:uppercase;">
                    <button type="button" class="btn btn-secondary" id="applyPromoBtn">Terapkan</button>
                </div>
                <div class="promo-feedback" id="promoFeedback"></div>
            </div>

            <div class="checkout-section">
                <h3>Metode Pembayaran</h3>
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment_type" value="bank_transfer" required>
                        <div>
                            <div class="payment-option-label">Transfer Bank</div>
                            <div class="payment-option-desc">BCA · Mandiri · BNI · BRI</div>
                        </div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_type" value="qris">
                        <div>
                            <div class="payment-option-label">QRIS</div>
                            <div class="payment-option-desc">Scan QR dari semua e-wallet & m-banking</div>
                        </div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_type" value="credit_card">
                        <div>
                            <div class="payment-option-label">Kartu Kredit / Debit</div>
                            <div class="payment-option-desc">Visa · Mastercard · JCB</div>
                        </div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_type" value="gopay">
                        <div>
                            <div class="payment-option-label">GoPay</div>
                            <div class="payment-option-desc">Bayar lewat aplikasi Gojek</div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div>
            <div class="checkout-section">
                <h3>Ringkasan Pesanan</h3>

                <div class="summary-row">
                    <span>Subtotal (<?php echo count($cartItems); ?> item)</span>
                    <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                </div>

                <div class="summary-row discount" id="discountRow" style="display:none;">
                    <span id="discountLabel">Diskon promo</span>
                    <span id="discountValue">-Rp 0</span>
                </div>

                <div class="summary-row total">
                    <span>Total</span>
                    <span id="totalValue">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                </div>

                <button type="submit" class="btn btn-checkout">Bayar Sekarang</button>
                <a href="cart.php" class="btn btn-secondary btn-checkout" style="text-align:center;display:block;margin-top:8px;">Kembali ke Keranjang</a>
            </div>
        </div>

    </div>
</form>

<script>
const subtotal = <?php echo $subtotal; ?>;

function rupiah(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

document.getElementById('applyPromoBtn').addEventListener('click', async () => {
    const code     = document.getElementById('promoInput').value.trim();
    const feedback = document.getElementById('promoFeedback');

    if (!code) {
        feedback.className = 'promo-feedback error';
        feedback.textContent = 'Masukkan kode promo terlebih dahulu.';
        return;
    }

    const btn = document.getElementById('applyPromoBtn');
    btn.textContent = '...';
    btn.disabled = true;

    const res  = await fetch('checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'check_promo=1&promo_code=' + encodeURIComponent(code),
    });
    const data = await res.json();

    btn.textContent = 'Terapkan';
    btn.disabled = false;

    if (data.valid) {
        feedback.className = 'promo-feedback success';
        feedback.textContent = `✓ Kode "${data.code}" berhasil — diskon ${data.percent}% (maks. ${rupiah(data.max)})`;

        document.getElementById('hiddenPromoId').value    = data.promo_id;
        document.getElementById('hiddenFinalTotal').value = data.final_total;

        document.getElementById('discountRow').style.display  = 'flex';
        document.getElementById('discountLabel').textContent  = `Diskon (${data.percent}%)`;
        document.getElementById('discountValue').textContent  = '- ' + rupiah(data.discount);
        document.getElementById('totalValue').textContent     = rupiah(data.final_total);
    } else {
        feedback.className = 'promo-feedback error';
        feedback.textContent = data.message;

        document.getElementById('hiddenPromoId').value    = 0;
        document.getElementById('hiddenFinalTotal').value = subtotal;
        document.getElementById('discountRow').style.display = 'none';
        document.getElementById('totalValue').textContent    = rupiah(subtotal);
    }
});
</script>

<?php require_once 'templates/footer.php'; ?>