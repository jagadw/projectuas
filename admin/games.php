<?php
    require_once __DIR__ . '/../config/config.php';
    require_once __DIR__ . '/../classes/admin/Admin.php';
    require_once __DIR__ . '/../classes/admin/AdminGame.php';

    $adminGame = new AdminGame();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action == 'save_game') {
            $_SESSION['admin_notif'] = $adminGame->saveGame($_POST, $_FILES)
                ? ['type' => 'success', 'message' => 'Data game berhasil disimpan.']
                : ['type' => 'error', 'message' => 'Judul game wajib diisi.'];
            header("Location: games.php");
            exit;
        }

        if ($action == 'delete_game') {
            $adminGame->deleteGame((int) $_POST['id']);
            $_SESSION['admin_notif'] = ['type' => 'success', 'message' => 'Game berhasil dihapus.'];
            header("Location: games.php");
            exit;
        }

        if ($action == 'add_genre') {
            $adminGame->addGenre(trim($_POST['name'] ?? ''));
            $_SESSION['admin_notif'] = ['type' => 'success', 'message' => 'Genre berhasil ditambahkan.'];
            header("Location: games.php");
            exit;
        }
    }

    $editId = (int) ($_GET['edit'] ?? 0);
    $platforms = $adminGame->getPlatforms();
    $editGame = $editId ? $adminGame->getGame($editId) : null;
    $selectedGenres = $editId ? $adminGame->getSelectedGenres($editId) : [];
    $genres = $adminGame->getGenres();
    $games = $adminGame->getGames();

    $pageTitle = 'Manajemen Game';
    $pageInfo = count($games) . ' game';
    require_once __DIR__ . '/../templates/admin/admin_header.php';
?>

<div class="admin-grid-two">
    <section class="admin-panel">
        <div class="admin-panel-head">
            <h3><?php echo $editGame ? 'Edit Game' : 'Tambah Game'; ?></h3>
        </div>
        <form method="POST" class="admin-form" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_game">
            <input type="hidden" name="id" value="<?php echo e($editGame['id'] ?? 0); ?>">
            <input type="hidden" name="old_image" value="<?php echo e($editGame['image'] ?? ''); ?>">
            <div class="form-group"><label>Judul</label><input type="text" name="title" value="<?php echo e($editGame['title'] ?? ''); ?>" required></div>
            <div class="form-group"><label>Deskripsi</label><textarea name="description" rows="4"><?php echo e($editGame['description'] ?? ''); ?></textarea></div>
            <div class="admin-form-row">
                <div class="form-group"><label>Harga</label><input type="number" name="price" min="0" step="0.01" value="<?php echo e($editGame['price'] ?? 0); ?>" required></div>
                <div class="form-group"><label>Platform</label><select name="platform"><?php foreach ($platforms as $platform): ?><option value="<?php echo $platform; ?>" <?php echo ($editGame['platform'] ?? '') === $platform ? 'selected' : ''; ?>><?php echo $platform; ?></option><?php endforeach; ?></select></div>
            </div>
            <div class="form-group">
                <label>Gambar</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/avif">
                <?php if (!empty($editGame['image'])): ?><p class="text-muted">Gambar saat ini: <?php echo e($editGame['image']); ?></p><?php endif; ?>
            </div>
            <div class="form-group">
                <label>Genre</label>
                <div class="check-grid">
                    <?php foreach ($genres as $genre):
                        $checked = in_array($genre['id'], $selectedGenres, true);
                    ?>
                        <label class="genre-check">
                            <input type="checkbox" name="genres[]" value="<?php echo e($genre['id']); ?>" <?php echo $checked ? 'checked' : ''; ?> />
                            <span><?php echo e($genre['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                    <?php if (!$genres): ?><span class="text-muted">Belum ada genre.</span><?php endif; ?>
                </div>
            </div>
            <button class="btn" type="submit">Simpan Game</button>
            <?php if ($editGame): ?><a href="games.php" class="btn btn-secondary">Batal</a><?php endif; ?>
        </form>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h3>Genre Baru</h3>
        </div>
        <form method="POST" class="inline-form">
            <input type="hidden" name="action" value="add_genre">
            <input type="text" name="name" placeholder="Contoh: RPG" required>
            <button class="btn" type="submit">Tambah</button>
        </form>
        <div class="genre-list">
            <?php foreach ($genres as $genre): ?><span class="badge"><?php echo e($genre['name']); ?></span><?php endforeach; ?>
        </div>
    </section>
</div>

<section class="admin-panel">
    <div class="admin-panel-head">
        <h3>Daftar Game</h3>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Harga</th>
                    <th>Platform</th>
                    <th>Genre</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td><?php echo e($game['title']); ?></td>
                        <td><?php echo rupiah($game['price']); ?></td>
                        <td><?php echo e($game['platform']); ?></td>
                        <td><?php echo e($game['genres'] ?: '-'); ?></td>
                        <td class="actions-cell">
                            <a href="games.php?edit=<?php echo e($game['id']); ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" onsubmit="return confirm('Hapus game ini?')">
                                <input type="hidden" name="action" value="delete_game">
                                <input type="hidden" name="id" value="<?php echo e($game['id']); ?>">
                                <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php
                endforeach;
                ?>
            </tbody>
        </table>
    </div>
</section>

<?php
    require_once __DIR__ . '/../templates/admin/admin_footer.php';
?>