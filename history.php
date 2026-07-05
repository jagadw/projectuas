<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/config.php';
require_once 'classes/Transaction.php';

$transaction  = new Transaction();
$userId       = $_SESSION['user_id'];
$transactions = $transaction->getUserTransactions($userId);

$pageTitle = 'Riwayat Transaksi';
require_once 'templates/header.php';
?>

<h2>Riwayat Transaksi</h2>

<?php if(empty($transactions)): ?>
    <div class="empty-state">
        <p>Belum ada transaksi.</p>
        <a href="index.php" class="btn btn-secondary">Jelajahi Game</a>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($transactions as $tx):
                    $imgSrc = !empty($tx['first_image'])
                        ? 'public/assets/' . htmlspecialchars($tx['first_image'])
                        : 'https://picsum.photos/seed/' . ($tx['first_game_id'] + 50) . '/80/80';
                    $statusClass = match($tx['payment_status']) {
                        'settlement' => 'status-settlement',
                        'pending'    => 'status-pending',
                        default      => 'status-danger',
                    };
                    $statusLabel = match($tx['payment_status']) {
                        'settlement' => 'Settlement',
                        'pending'    => 'Pending',
                        'expire'     => 'Expired',
                        'cancel'     => 'Cancelled',
                        'deny'       => 'Failed',
                        default      => ucfirst($tx['payment_status']),
                    };
                ?>
                <tr>
                    <td>
                        <div class="hist-order-cell">
                            <div class="hist-thumb">
                                <img src="<?php echo $imgSrc; ?>" alt="">
                            </div>
                            <div>
                                <div class="hist-order-id"><?php echo htmlspecialchars($tx['midtrans_transaction_id'] ?? '#' . $tx['id']); ?></div>
                                <div class="hist-game-name"><?php echo htmlspecialchars($tx['first_game'] ?? '-'); ?></div>
                                <div class="hist-platform"><?php echo htmlspecialchars($tx['first_platform'] ?? ''); ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div><?php echo date('d M Y', strtotime($tx['created_at'])); ?></div>
                        <div class="hist-time"><?php echo date('H:i', strtotime($tx['created_at'])); ?></div>
                    </td>
                    <td><?php echo (int)$tx['item_count']; ?> Item</td>
                    <td>
                        <div>Rp <?php echo number_format($tx['total_amount'], 0, ',', '.'); ?></div>
                        <?php if(!empty($tx['promo_code'])): ?>
                            <span class="badge" style="font-size:10px"><?php echo htmlspecialchars($tx['promo_code']); ?></span>
                        <?php else: ?>
                            <div class="hist-time">-</div>
                        <?php endif; ?>
                    </td>
                    <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    <td>
                        <div class="hist-payment">Midtrans</div>
                        <div class="hist-time"><?php echo htmlspecialchars($tx['payment_type'] ?? '-'); ?></div>
                    </td>
                    <td>
                        <a href="transaction_detail.php?id=<?php echo $tx['id']; ?>" class="btn btn-secondary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            View Details
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once 'templates/footer.php'; ?>