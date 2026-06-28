<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/config.php';
require_once 'classes/Transaction.php';

$transaction = new Transaction();
$userId      = $_SESSION['user_id'];
$txId        = (int)($_GET['id'] ?? 0);

if(!$txId) {
    header('Location: index.php');
    exit;
}

$tx      = $transaction->getTransaction($txId, $userId);
$details = $transaction->getDetails($txId);

if(!$tx) {
    header('Location: index.php');
    exit;
}

if($tx['payment_status'] === 'pending') {
    header('Location: transaction.php?id=' . $txId);
    exit;
}

$paymentLabel = [
    'bank_transfer' => 'Transfer Bank',
    'qris'          => 'QRIS',
    'credit_card'   => 'Kartu Kredit / Debit',
    'gopay'         => 'GoPay',
];

$rawSubtotal = 0;
foreach($details as $d) $rawSubtotal += $d['price'];
$finalTotal  = (float)$tx['total_amount'];
$discountAmt = $rawSubtotal - $finalTotal;

$pageTitle = 'Transaksi Berhasil';
require_once 'templates/header.php';
?>

<div class="pay-wrap">

    <div class="txd-success-banner">
        <div class="txd-success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div class="txd-success-title">Pembayaran Berhasil!</div>
            <div class="txd-success-sub">Transaksi #<?php echo $tx['id']; ?> &middot; <?php echo date('d M Y, H:i', strtotime($tx['created_at'])); ?></div>
        </div>
    </div>

    <div class="co-block">
        <div class="txd-keys-title">
            <div class="txd-keys-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 8h2M9 7v2M15 8h2M16 7v2"/></svg>
            </div>
            Game Key Kamu
        </div>
        <?php foreach($details as $item): ?>
        <div class="txd-key-item">
            <div class="txd-key-header">
                <div class="txd-key-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 8h2M9 7v2M15 8h2M16 7v2"/></svg>
                </div>
                <div>
                    <div class="txd-key-game"><?php echo htmlspecialchars($item['title']); ?></div>
                    <div class="txd-key-price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></div>
                </div>
            </div>
            <div class="txd-key-row">
                <code class="txd-key-code"><?php echo htmlspecialchars($item['key_code']); ?></code>
                <button class="btn btn-secondary btn-sm" onclick="copyKey(this, '<?php echo htmlspecialchars($item['key_code']); ?>')">Salin</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="co-block">
        <div class="co-block-title">Payment Detail</div>
        <div class="txd-info-body">
            <div class="txd-info-row">
                <span>Metode</span>
                <span><?php echo htmlspecialchars($paymentLabel[$tx['payment_type']] ?? $tx['payment_type']); ?></span>
            </div>
            <div class="txd-info-row">
                <span>Tanggal</span>
                <span><?php echo date('d M Y, H:i', strtotime($tx['created_at'])); ?></span>
            </div>
            <div class="txd-info-row">
                <span>ID Transaksi</span>
                <span>#<?php echo $tx['id']; ?></span>
            </div>
            <?php if($tx['promo_code']): ?>
            <div class="txd-info-row">
                <span>Promo</span>
                <span><?php echo htmlspecialchars($tx['promo_code']); ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="co-block">
        <div class="co-block-title">Order Summary</div>
        <div class="txd-info-body">
            <?php foreach($details as $item): ?>
            <div class="txd-info-row">
                <span><span class="txd-qty">x1</span> <?php echo htmlspecialchars($item['title']); ?></span>
                <span>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></span>
            </div>
            <?php endforeach; ?>
            <div class="txd-info-row">
                <span>Subtotal</span>
                <span>Rp <?php echo number_format($rawSubtotal, 0, ',', '.'); ?></span>
            </div>
            <?php if($discountAmt > 0): ?>
            <div class="txd-info-row txd-discount-row">
                <span>Diskon <?php echo htmlspecialchars($tx['promo_code'] ?? ''); ?></span>
                <span>- Rp <?php echo number_format($discountAmt, 0, ',', '.'); ?></span>
            </div>
            <?php endif; ?>
            <div class="txd-info-row txd-total-row">
                <span>Total Dibayar</span>
                <span>Rp <?php echo number_format($finalTotal, 0, ',', '.'); ?></span>
            </div>
        </div>
    </div>

    <div class="txd-actions">
        <a href="library.php" class="btn btn-secondary">Library Key</a>
        <a href="index.php" class="btn">Beli Game Lagi</a>
    </div>

</div>

<script>
function copyKey(btn, key) {
    navigator.clipboard.writeText(key).then(() => {
        btn.textContent = 'Disalin!';
        setTimeout(() => btn.textContent = 'Salin', 2000);
    });
}
</script>

<?php require_once 'templates/footer.php'; ?>