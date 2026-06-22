<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/admin/Admin.php';
require_once __DIR__ . '/../classes/admin/AdminDashboard.php';

$pageTitle = 'Dashboard Admin';
$pageInfo = 'Manajemen toko';

$dashboard = new AdminDashboard();
$stats = $dashboard->getStats();
$revenue = $dashboard->getRevenue();
$latestTransactions = $dashboard->getLatestTransactions();
$lowStocks = $dashboard->getLowStocks();

require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<div class="admin-stats-grid">
    <div class="admin-stats"><span>Total Game</span><strong><?php echo e($stats['games']); ?></strong></div>
    <div class="admin-stats"><span>Key Available</span><strong><?php echo e($stats['available_keys']); ?></strong></div>
    <div class="admin-stats"><span>Pending</span><strong><?php echo e($stats['pending']); ?></strong></div>
    <div class="admin-stats"><span>Ticket Open</span><strong><?php echo e($stats['open_tickets']); ?></strong></div>
</div>

<div class="admin-grid-two">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h3>Transaksi Masuk</h3>
            <span><?php echo rupiah($revenue); ?></span>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestTransactions as $trx): ?>
                        <tr>
                            <td>#<?php echo e($trx['id']); ?></td>
                            <td><?php echo e($trx['username'] ?? '-'); ?></td>
                            <td><?php echo rupiah($trx['total_amount']); ?></td>
                            <td><span class="status-badge status-<?php echo e($trx['payment_status']); ?>"><?php echo e($trx['payment_status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$latestTransactions): ?>
                        <tr>
                            <td colspan="4" class="empty-state">Belum ada transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h3>Stok Terendah</h3>
            <a href="keys.php" class="btn btn-secondary btn-sm">Restock</a>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Key Available</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStocks as $game): ?>
                        <tr>
                            <td><?php echo e($game['title']); ?></td>
                            <td><?php echo e($game['available_stock']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php
    require_once __DIR__ . '/../templates/admin/admin_footer.php';
?>