<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/admin/Admin.php';
require_once __DIR__ . '/../classes/admin/AdminReview.php';

$adminReview = new AdminReview();

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$reviews = $adminReview->getReviews();

$pageTitle = 'Monitor Ulasan';
$pageInfo = count($reviews) . ' ulasan';
require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<section class="admin-panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Game</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?php echo e($review['game_title'] ?? '-'); ?></td>
                        <td><?php echo e($review['username'] ?? '-'); ?></td>
                        <td><?php echo e($review['rating']); ?>/5</td>
                        <td><?php echo e($review['comment']); ?></td>
                        <td><?php echo e($review['created_at']); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Hapus ulasan ini?')">

                                <input type="hidden" name="id" value="<?php echo e($review['id']); ?>">
                                <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$reviews): ?><tr>
                        <td colspan="6" class="empty-state">Belum ada ulasan.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php
require_once __DIR__ . '/../templates/admin/admin_footer.php';
?>