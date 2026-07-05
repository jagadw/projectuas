<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/admin/Admin.php';
require_once __DIR__ . '/../classes/admin/AdminKey.php';

$adminKey = new AdminKey();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'], $_POST['key_codes'])) {
    $_SESSION['admin_notif'] = $adminKey->restockKeys((int)($_POST['game_id'] ?? 0), (string)($_POST['key_codes'] ?? ''))
        ? ['type' => 'success', 'message' => 'Stok key berhasil ditambahkan.']
        : ['type' => 'error', 'message' => 'Gagal menambah stok key.'];
}

$games = $adminKey->getGames();
$stocks = $adminKey->getStocks();
$keys = $adminKey->getLatestKeys();

$pageTitle = 'Restock Game Key';
$pageInfo = 'Monitor stok redeem key dan menambah jumlah stok dan daftar key.';
require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<div class="admin-grid-two">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h3>Tambah Stok Key</h3>
        </div>
        <form method="POST" class="admin-form">

            <div class="form-group">
                <label>Game</label>
                <select name="game_id" required>
                    <option value="">Pilih game</option>
                    <?php
                    foreach ($games as $game): ?><option value="<?php echo e($game['id']); ?>"><?php echo e($game['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Redeem key</label>
                <textarea name="key_codes" rows="8" placeholder="12 Digit Code" required></textarea>
            </div>
            <button class="btn" type="submit">Simpan Key Available</button>
        </form>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h3>Ringkasan Stok</h3>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Game</th>
                        <th>Available</th>
                        <th>Sold</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stocks as $stock): ?>
                        <tr>
                            <td><?php echo e($stock['title']); ?></td>
                            <td><?php echo e((int) $stock['available_stock']); ?></td>
                            <td><?php echo e((int) $stock['sold_stock']); ?></td>
                            <td><?php echo e((int) $stock['total_stock']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<section class="admin-panel">
    <div class="admin-panel-head">
        <h3>Key Terbaru</h3>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Game</th>
                    <th>Key</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($keys as $key): ?>
                    <tr>
                        <td><?php echo e($key['title']); ?></td>
                        <td><code><?php echo e($key['key_code']); ?></code></td>
                        <td><span class="status-badge status-<?php echo e($key['status']); ?>"><?php echo e($key['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$keys): ?><tr>
                        <td colspan="3" class="empty-state">Belum ada CD-Key.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../templates/admin/admin_footer.php'; ?>