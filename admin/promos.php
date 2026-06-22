<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/admin/Admin.php';
require_once __DIR__ . '/../classes/admin/AdminPromo.php';

$adminPromo = new AdminPromo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_promo') {
        $adminPromo->savePromo($_POST);
        header('Location: promos.php');
        exit;
    }

    if ($action === 'delete_promo') {
        $adminPromo->deletePromo((int) ($_POST['id'] ?? 0));
        header('Location: promos.php');
        exit;
    }
}

$editId = (int) ($_GET['edit'] ?? 0);
$editPromo = $editId ? $adminPromo->getPromo($editId) : null;
$promos = $adminPromo->getPromos();

$pageTitle = 'Manajemen Promo Code';
$pageInfo = count($promos) . ' promo';
require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<section class="admin-panel">
    <div class="admin-panel-head">
        <h3><?php echo $editPromo ? 'Edit Promo' : 'Promo Baru'; ?></h3>
    </div>
    <form method="POST" class="admin-form admin-form-wide">
        <input type="hidden" name="action" value="save_promo">
        <input type="hidden" name="id" value="<?php echo e($editPromo['id'] ?? 0); ?>">
        <div class="admin-form-row four">
            <div class="form-group"><label>Kode Kupon</label><input type="text" name="code" value="<?php echo e($editPromo['code'] ?? ''); ?>" required></div>
            <div class="form-group"><label>Diskon (%)</label><input type="number" name="discount_percentage" min="0" max="100" step="0.01" value="<?php echo e($editPromo['discount_percentage'] ?? 0); ?>" required></div>
            <div class="form-group"><label>Maks. Potongan</label><input type="number" name="max_discount" min="0" step="0.01" value="<?php echo e($editPromo['max_discount'] ?? 0); ?>" required></div>
            <div class="form-group"><label>Kedaluwarsa</label><input type="datetime-local" name="valid_until" value="<?php echo $editPromo && $editPromo['valid_until'] ? e(date('Y-m-d\TH:i', strtotime($editPromo['valid_until']))) : ''; ?>" required></div>
        </div>
        <button class="btn" type="submit">Simpan Promo</button>
        <?php if ($editPromo): ?><a href="promos.php" class="btn btn-secondary">Batal</a><?php endif; ?>
    </form>
</section>

<section class="admin-panel">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Diskon</th>
                    <th>Maks. Potongan</th>
                    <th>Kedaluwarsa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($promos as $promo): ?>
                    <tr>
                        <td><code><?php echo e($promo['code']); ?></code></td>
                        <td><?php echo e($promo['discount_percentage']); ?>%</td>
                        <td><?php echo rupiah($promo['max_discount']); ?></td>
                        <td><?php echo e($promo['valid_until']); ?></td>
                        <td class="actions-cell">
                            <a href="promos.php?edit=<?php echo e($promo['id']); ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" onsubmit="return confirm('Hapus promo ini?')">
                                <input type="hidden" name="action" value="delete_promo">
                                <input type="hidden" name="id" value="<?php echo e($promo['id']); ?>">
                                <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$promos): ?><tr>
                        <td colspan="5" class="empty-state">Belum ada promo code.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php
require_once __DIR__ . '/../templates/admin/admin_footer.php';
?>