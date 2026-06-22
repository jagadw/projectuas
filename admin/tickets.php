<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/admin/Admin.php';
require_once __DIR__ . '/../classes/admin/AdminTicket.php';

$adminTicket = new AdminTicket();

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$filter = $_GET['status'] ?? 'open';
$tickets = $adminTicket->getTickets($filter);

$pageTitle = 'Layanan Pelanggan';
$pageInfo = count($tickets) . ' ticket';
require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<div class="admin-tabs">
    <a href="tickets.php?status=open" class="<?php echo $filter === 'open' ? 'active' : ''; ?>">Open</a>
    <a href="tickets.php?status=closed" class="<?php echo $filter === 'closed' ? 'active' : ''; ?>">Closed</a>
    <a href="tickets.php?status=all" class="<?php echo $filter === 'all' ? 'active' : ''; ?>">Semua</a>
</div>

<section class="admin-list">
    <?php foreach ($tickets as $ticket): ?>
        <article class="admin-panel ticket-item">
            <div class="admin-panel-head">
                <div>
                    <h3><?php echo e($ticket['subject']); ?></h3>
                    <p class="text-muted"><?php echo e($ticket['username'] ?? '-'); ?> - <?php echo e($ticket['email'] ?? '-'); ?> - <?php echo e($ticket['created_at']); ?></p>
                </div>
                <span class="status-badge status-<?php echo e($ticket['status']); ?>"><?php echo e($ticket['status']); ?></span>
            </div>
            <p><?php echo nl2br(e($ticket['message'])); ?></p>
            <form method="POST" class="ticket-actions">
                <input type="hidden" name="id" value="<?php echo e($ticket['id']); ?>">
                <input type="hidden" name="status" value="<?php echo $ticket['status'] === 'closed' ? 'open' : 'closed'; ?>">
                <button class="btn <?php echo $ticket['status'] === 'closed' ? 'btn-secondary' : ''; ?>" type="submit">
                    <?php echo $ticket['status'] === 'closed' ? 'Buka Lagi' : 'Tandai Closed'; ?>
                </button>
            </form>
        </article>
    <?php endforeach; ?>
    <?php if (!$tickets): ?>
        <div class="admin-panel empty-state">Tidak ada ticket.</div>
        <?php endif; ?>
</section>

<?php
require_once __DIR__ . '/../templates/admin/admin_footer.php';
?>