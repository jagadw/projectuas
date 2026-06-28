<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/config.php';
require_once 'classes/Transaction.php';

$transaction = new Transaction();
$userId = $_SESSION['user_id'];
$txId   = (int)($_GET['id'] ?? 0);

if(!$txId) {
    header('Location: index.php');
    exit;
}

$tx = $transaction->getTransaction($txId, $userId);

if(!$tx || $tx['payment_status'] !== 'pending') {
    header('Location: transaction_detail.php?id=' . $txId);
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $transaction->settle($txId, $userId);
    header("Location: transaction_detail.php?id=$txId");
    exit;
}

$paymentLabel = [
    'bank_transfer' => 'Transfer Bank',
    'qris'          => 'QRIS',
    'credit_card'   => 'Kartu Kredit / Debit',
    'gopay'         => 'GoPay',
];

$pageTitle = 'Pembayaran #' . $txId;
require_once 'templates/header.php';
?>


<div class="pay-wrap">

    <div class="co-block">
        <div class="co-block-title">Menunggu Pembayaran</div>
        <div class="pay-method-body">

            <?php if($tx['payment_type'] === 'qris'): ?>
            <div class="pay-qr-wrap">
                <div class="pay-qr-box">
                    <svg width="180" height="180" viewBox="0 0 180 180" xmlns="http://www.w3.org/2000/svg">
                        <rect width="180" height="180" fill="#1c1c1f"/>
                        <rect x="10" y="10" width="60" height="60" fill="none" stroke="#ededef" stroke-width="6"/>
                        <rect x="22" y="22" width="36" height="36" fill="#ededef"/>
                        <rect x="110" y="10" width="60" height="60" fill="none" stroke="#ededef" stroke-width="6"/>
                        <rect x="122" y="22" width="36" height="36" fill="#ededef"/>
                        <rect x="10" y="110" width="60" height="60" fill="none" stroke="#ededef" stroke-width="6"/>
                        <rect x="22" y="122" width="36" height="36" fill="#ededef"/>
                        <rect x="80" y="10" width="10" height="10" fill="#ededef"/>
                        <rect x="80" y="30" width="10" height="10" fill="#ededef"/>
                        <rect x="80" y="50" width="10" height="10" fill="#ededef"/>
                        <rect x="10" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="30" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="50" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="80" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="100" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="120" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="140" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="160" y="80" width="10" height="10" fill="#ededef"/>
                        <rect x="80" y="100" width="10" height="10" fill="#ededef"/>
                        <rect x="100" y="100" width="10" height="10" fill="#ededef"/>
                        <rect x="80" y="120" width="10" height="10" fill="#ededef"/>
                        <rect x="100" y="120" width="10" height="10" fill="#ededef"/>
                        <rect x="120" y="100" width="10" height="10" fill="#ededef"/>
                        <rect x="140" y="100" width="10" height="10" fill="#ededef"/>
                        <rect x="160" y="120" width="10" height="10" fill="#ededef"/>
                        <rect x="140" y="140" width="10" height="10" fill="#ededef"/>
                        <rect x="160" y="140" width="10" height="10" fill="#ededef"/>
                        <rect x="120" y="160" width="10" height="10" fill="#ededef"/>
                        <rect x="140" y="160" width="10" height="10" fill="#ededef"/>
                        <rect x="160" y="160" width="10" height="10" fill="#ededef"/>
                    </svg>
                </div>
                <p class="pay-qr-hint">Scan QR code ini dengan aplikasi e-wallet atau m-banking kamu</p>
                <div class="pay-amount-badge">Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?></div>
            </div>

            <?php elseif($tx['payment_type'] === 'bank_transfer'): ?>
            <div class="pay-bank-wrap">
                <p class="pay-hint">Transfer ke rekening berikut:</p>
                <div class="pay-bank-list">
                    <div class="pay-bank-item">
                        <span class="pay-bank-name">BCA</span>
                        <div>
                            <div class="pay-bank-num">1234 5678 9012</div>
                            <div class="pay-bank-acc">a.n. Seedem Indonesia</div>
                        </div>
                    </div>
                    <div class="pay-bank-item">
                        <span class="pay-bank-name">Mandiri</span>
                        <div>
                            <div class="pay-bank-num">9876 5432 1098</div>
                            <div class="pay-bank-acc">a.n. Seedem Indonesia</div>
                        </div>
                    </div>
                </div>
                <div class="pay-amount-badge">Total: Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?></div>
            </div>

            <?php elseif($tx['payment_type'] === 'gopay'): ?>
            <div class="pay-bank-wrap">
                <p class="pay-hint">Bayar via GoPay ke nomor berikut:</p>
                <div class="pay-bank-list">
                    <div class="pay-bank-item">
                        <span class="pay-bank-name">GoPay</span>
                        <div>
                            <div class="pay-bank-num">0812-3456-7890</div>
                            <div class="pay-bank-acc">a.n. Seedem Indonesia</div>
                        </div>
                    </div>
                </div>
                <div class="pay-amount-badge">Total: Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?></div>
            </div>

            <?php else: ?>
            <div class="pay-bank-wrap">
                <p class="pay-hint">Masukkan detail kartu kredit/debit kamu:</p>
                <div class="pay-card-fields">
                    <div class="form-group">
                        <label>Nama di Kartu</label>
                        <input type="text" placeholder="John Doe" disabled>
                    </div>
                    <div class="form-group">
                        <label>Nomor Kartu</label>
                        <input type="text" placeholder="1234 5678 9012 3456" disabled>
                    </div>
                    <div class="pay-card-row">
                        <div class="form-group">
                            <label>Expired</label>
                            <input type="text" placeholder="MM/YY" disabled>
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" placeholder="123" disabled>
                        </div>
                    </div>
                </div>
                <div class="pay-amount-badge">Total: Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?></div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="co-block">
        <div class="co-block-title">Batas Waktu Pembayaran</div>
        <div class="pay-timer-body">
            <div class="pay-timer" id="countdown">15:00</div>
            <p class="pay-timer-hint">Selesaikan pembayaran sebelum waktu habis</p>
        </div>
    </div>

    <div class="co-block">
        <div class="co-block-title">Detail Pesanan</div>
        <div class="txd-info-body">
            <div class="txd-info-row">
                <span>ID Transaksi</span>
                <span>#<?php echo $tx['id']; ?></span>
            </div>
            <div class="txd-info-row">
                <span>Metode</span>
                <span><?php echo htmlspecialchars($paymentLabel[$tx['payment_type']] ?? $tx['payment_type']); ?></span>
            </div>
            <div class="txd-info-row">
                <span>Status</span>
                <span class="status-badge status-pending">Menunggu</span>
            </div>
            <div class="txd-info-row">
                <span>Total</span>
                <span style="font-weight:700; color:var(--text)">Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?></span>
            </div>
        </div>
    </div>

    <form method="POST">
        <input type="hidden" name="confirm_payment" value="1">
        <button type="submit" class="btn btn-full">✓ Konfirmasi Sudah Bayar</button>
    </form>

</div>

<script>
let seconds = 15 * 60;
const el = document.getElementById('countdown');
const timer = setInterval(() => {
    seconds--;
    if(seconds <= 0) {
        clearInterval(timer);
        el.textContent = '00:00';
        el.style.color = 'var(--red)';
        return;
    }
    const m = String(Math.floor(seconds / 60)).padStart(2, '0');
    const s = String(seconds % 60).padStart(2, '0');
    el.textContent = m + ':' + s;
    if(seconds <= 60) el.style.color = 'var(--red)';
}, 1000);
</script>

<?php require_once 'templates/footer.php'; ?>