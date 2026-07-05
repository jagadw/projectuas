<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/config.php';
require_once 'classes/Transaction.php';
require_once 'classes/Feedback.php';

$transaction = new Transaction();
$feedback    = new Feedback();
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

if($tx['payment_status'] === 'pending' && !empty($tx['midtrans_transaction_id'])) {
    try {
        $status         = \Midtrans\Transaction::status($tx['midtrans_transaction_id']);
        $midtransStatus = $status->transaction_status ?? '';
        $fraudStatus    = $status->fraud_status ?? 'accept';
        if($midtransStatus === 'settlement' || ($midtransStatus === 'capture' && $fraudStatus === 'accept')) {
            $transaction->updateStatus($txId, 'settlement');
            $tx = $transaction->getTransaction($txId, $userId);
        } elseif(in_array($midtransStatus, ['cancel', 'deny', 'expire'])) {
            $transaction->updateStatus($txId, $midtransStatus);
            $tx = $transaction->getTransaction($txId, $userId);
        }
    } catch(\Exception $e) {}
}

if($tx['payment_status'] === 'pending') {
    header('Location: transaction.php?id=' . $txId);
    exit;
}

$feedbackError = '';
$feedbackMsg   = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $gameId  = (int)($_POST['game_id'] ?? 0);
    $rating  = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if($rating < 1 || $rating > 5) {
        $feedbackError = 'Pilih rating antara 1–5.';
    } elseif(empty($comment)) {
        $feedbackError = 'Komentar tidak boleh kosong.';
    } elseif(!$gameId) {
        $feedbackError = 'Game tidak valid.';
    } else {
        if($feedback->submit($userId, $gameId, $rating, $comment)) {
            $feedbackMsg = 'Feedback berhasil dikirim!';
        } else {
            $feedbackError = 'Kamu sudah pernah mengirim feedback untuk game ini.';
        }
    }
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

    <?php foreach($details as $item):
        $gameId     = $item['game_id'] ?? null;
        if(!$gameId) continue;
        $existingFb = $feedback->getByTransactionAndGame($userId, $txId, $gameId);
    ?>
    <div class="co-block">
        <div class="co-block-title">Feedback — <?php echo htmlspecialchars($item['title']); ?></div>
        <div class="txd-feedback-body">
            <?php if($existingFb): ?>
                <div class="fb-stars-display">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <svg class="fb-star <?php echo $i <= $existingFb['rating'] ? 'fb-star-filled' : ''; ?>"
                             xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                             fill="<?php echo $i <= $existingFb['rating'] ? 'currentColor' : 'none'; ?>"
                             stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    <?php endfor; ?>
                </div>
                <?php if($existingFb['comment']): ?>
                    <p class="fb-comment"><?php echo htmlspecialchars($existingFb['comment']); ?></p>
                <?php endif; ?>
                <p class="fb-submitted-date">Submitted on: <?php echo date('d M Y H:i', strtotime($existingFb['created_at'])); ?></p>
                <div class="fb-already-notice">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Kamu sudah mengirim feedback untuk game ini.
                </div>
            <?php else: ?>
                <?php if($feedbackMsg && isset($_POST['game_id']) && (int)$_POST['game_id'] === $gameId): ?>
                    <div class="alert success"><?php echo htmlspecialchars($feedbackMsg); ?></div>
                <?php endif; ?>
                <?php if($feedbackError && isset($_POST['game_id']) && (int)$_POST['game_id'] === $gameId): ?>
                    <div class="alert"><?php echo htmlspecialchars($feedbackError); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <input type="hidden" name="game_id" value="<?php echo $gameId; ?>">
                    <div class="fb-form-group">
                        <label class="fb-label">Rating</label>
                        <div class="fb-star-input" data-game="<?php echo $gameId; ?>">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <svg class="fb-star-pick" data-val="<?php echo $i; ?>"
                                 xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" class="rating-val" value="0">
                    </div>
                    <div class="fb-form-group">
                        <label class="fb-label">Comment</label>
                        <textarea name="comment" class="fb-textarea" placeholder="Bagikan pengalamanmu tentang game ini..." rows="4"></textarea>
                    </div>
                    <button type="submit" name="submit_feedback" class="btn">Submit Feedback</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

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

document.querySelectorAll('.fb-star-input').forEach(container => {
    const stars      = container.querySelectorAll('.fb-star-pick');
    const ratingInput = container.closest('form').querySelector('.rating-val');
    stars.forEach(star => {
        star.addEventListener('mouseover', () => highlight(stars, +star.dataset.val));
        star.addEventListener('mouseout',  () => highlight(stars, +ratingInput.value));
        star.addEventListener('click', () => {
            ratingInput.value = star.dataset.val;
            highlight(stars, +star.dataset.val);
        });
    });
});

function highlight(stars, val) {
    stars.forEach(s => {
        const on = +s.dataset.val <= val;
        s.setAttribute('fill', on ? 'currentColor' : 'none');
        s.classList.toggle('fb-star-filled', on);
    });
}
</script>

<?php require_once 'templates/footer.php'; ?>