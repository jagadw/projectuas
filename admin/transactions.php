<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/admin/Admin.php';
require_once __DIR__ . '/../classes/admin/AdminTransaction.php';

$filter = $_GET['status'] ?? 'all';

$adminTransaction = new AdminTransaction();
$transactions = $adminTransaction->getTransactions($filter);
$counts = $adminTransaction->getStatusCounts();
$failedCount = $adminTransaction->getFailedCount($counts);

$pageTitle = 'Pemantauan Transaksi';
$pageInfo = count($transactions) . ' transaksi tampil';
require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<div class="admin-tabs">
    <a href="transactions.php" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">Semua</a>
    <a href="transactions.php?status=pending" class="<?php echo $filter === 'pending' ? 'active' : ''; ?>">Pending (<?php echo e($counts['pending'] ?? 0); ?>)</a>
    <a href="transactions.php?status=settlement" class="<?php echo $filter === 'settlement' ? 'active' : ''; ?>">Settlement (<?php echo e($counts['settlement'] ?? 0); ?>)</a>
    <a href="transactions.php?status=failed" class="<?php echo $filter === 'failed' ? 'active' : ''; ?>">Failed (<?php echo e($failedCount); ?>)</a>
</div>

<section class="admin-panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Promo</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $trx): ?>
                    <tr>
                        <td>#<?php echo e($trx['id']); ?></td>
                        <td><?php echo e($trx['username'] ?? '-'); ?></td>
                        <td><?php echo e($trx['promo_code'] ?? '-'); ?></td>
                        <td><?php echo e($trx['payment_type'] ?: '-'); ?></td>
                        <td><?php echo rupiah($trx['total_amount']); ?></td>
                        <td><span class="status-badge status-<?php echo e($trx['payment_status']); ?>"><?php echo e($trx['payment_status']); ?></span></td>
                        <td><?php echo e($trx['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$transactions): ?><tr>
                        <td colspan="7" class="empty-state">Transaksi tidak ditemukan.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php
require_once __DIR__ . '/../templates/admin/admin_footer.php';
?>