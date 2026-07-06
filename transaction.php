<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/config.php';
require_once 'classes/Transaction.php';

$transaction = new Transaction();
$userId      = $_SESSION['user_id'];
$txId        = (int) ($_GET['id'] ?? 0);

if (!$txId) {
    header('Location: index.php');
    exit;
}

$tx = $transaction->getTransaction($txId, $userId);

if (!$tx) {
    header('Location: index.php');
    exit;
}

if ($tx['payment_status'] !== 'pending') {
    header('Location: transaction_detail.php?id=' . $txId);
    exit;
}

$snapToken = $transaction->getSnapToken($txId);

$midtransClientKey = $_ENV['MIDTRANS_CLIENT_KEY'];
$midtransScriptUrl = filter_var($_ENV['MIDTRANS_IS_PRODUCTION'] ?? 'false', FILTER_VALIDATE_BOOLEAN)
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js';

$pageTitle = 'Menunggu Pembayaran #' . $txId;
require_once 'templates/header.php';
?>

<script src="<?php echo $midtransScriptUrl; ?>"
        data-client-key="<?php echo htmlspecialchars($midtransClientKey); ?>"></script>

<div class="pay-wrap">

    <div class="co-block">
        <div class="co-block-title">Menunggu Pembayaran</div>
        <div class="pay-method-body" style="text-align:center;padding:32px 16px;">
            <div style="font-size:48px;margin-bottom:16px;">⏳</div>
            <p style="margin-bottom:8px;color:var(--muted)">
                Transaksi <strong style="color:var(--text)">#<?php echo $txId; ?></strong>
                menunggu pembayaran sebesar
            </p>
            <div class="pay-amount-badge" style="display:inline-block;margin-bottom:24px;">
                Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?>
            </div>

            <?php if ($snapToken): ?>
            <!-- Buka kembali popup Midtrans Snap -->
            <button class="btn btn-full" id="openSnapBtn" style="max-width:320px;">
                Lanjutkan Pembayaran
            </button>
            <?php else: ?>
            <p class="error-box">Snap token tidak ditemukan. Hubungi admin.</p>
            <?php endif; ?>

            <a href="index.php" class="btn btn-secondary btn-full" style="max-width:320px;margin-top:10px;text-align:center;display:inline-block;">
                Kembali ke Beranda
            </a>
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
                <span>Status</span>
                <span class="status-badge status-pending">Menunggu Pembayaran</span>
            </div>
            <div class="txd-info-row">
                <span>Total</span>
                <span style="font-weight:700;color:var(--text)">
                    Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?>
                </span>
            </div>
            <div class="txd-info-row">
                <span>Tanggal</span>
                <span><?php echo date('d M Y, H:i', strtotime($tx['created_at'])); ?></span>
            </div>
        </div>
    </div>

</div>

<?php if ($snapToken): ?>
<script>
const snapToken = <?php echo json_encode($snapToken); ?>;
const txId      = <?php echo $txId; ?>;

function openSnap() {
    snap.pay(snapToken, {
        onSuccess: function(result) {
            window.location.href = 'transaction_detail.php?id=' + txId;
        },
        onPending: function(result) {
            window.location.reload();
        },
        onError: function(result) {
            alert('Pembayaran gagal. Silakan coba lagi.');
            window.location.reload();
        },
        onClose: function() {
            alert('Anda menutup popup pembayaran. Silakan lanjutkan pembayaran nanti.');
        }
    });
}

document.getElementById('openSnapBtn').addEventListener('click', openSnap);

<?php if (!empty($_GET['autoopen'])): ?>
openSnap();
<?php endif; ?>
</script>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>
