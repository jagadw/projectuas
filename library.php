<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/config.php';
require_once 'classes/Transaction.php';

$userId = $_SESSION['user_id'];

class Library extends Database {
    public function getUserKeys($userId) {
        $stmt = $this->conn->prepare(
            "SELECT gk.key_code, g.title, g.image, g.platform, td.price,
                    t.created_at, t.id as transaction_id
             FROM transactions t
             JOIN transaction_details td ON t.id = td.transaction_id
             JOIN game_keys gk ON td.game_key_id = gk.id
             JOIN games g ON gk.game_id = g.id
             WHERE t.user_id = ? AND t.payment_status = 'settlement'
             ORDER BY t.created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

$library = new Library();
$keys    = $library->getUserKeys($_SESSION['user_id']);

$pageTitle = 'Library Key';
require_once 'templates/header.php';
?>

<h2>Library Key</h2>
<p class="lib-subtitle">Game key untuk semua game yang kamu beli.</p>

<?php if(empty($keys)): ?>
    <div class="empty-state">
        <p>Belum ada game key. Beli game terlebih dahulu!</p>
        <a href="index.php" class="btn btn-secondary">Jelajahi Game</a>
    </div>
<?php else: ?>
    <div class="lib-list">
        <?php foreach($keys as $key): ?>
        <div class="lib-item">
            <div class="lib-item-img">
                <div class="lib-item-img-inner">
                    <?php
                    $imgSrc = !empty($key['image'])
                        ? 'public/assets/' . htmlspecialchars($key['image'])
                        : 'https://picsum.photos/seed/' . ($key['transaction_id'] + 50) . '/400/250';
                    ?>
                    <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($key['title']); ?>">
                </div>
            </div>
            <div class="lib-item-body">
                <div class="lib-item-title"><?php echo htmlspecialchars($key['title']); ?></div>
                <div class="lib-item-meta">
                    <?php if(!empty($key['platform'])): ?>
                        <span class="badge badge-platform"><?php echo htmlspecialchars($key['platform']); ?></span>
                    <?php endif; ?>
                    <span class="lib-item-date">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?php echo date('d M Y', strtotime($key['created_at'])); ?>
                    </span>
                </div>
                <div class="lib-key-label">Library Key</div>
                <div class="lib-key-row">
                    <code class="lib-key-code" id="key-<?php echo md5($key['key_code']); ?>"><?php echo htmlspecialchars($key['key_code']); ?></code>
                    <button class="lib-copy-btn" onclick="copyKey(this, '<?php echo htmlspecialchars($key['key_code']); ?>')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        Copy
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function copyKey(btn, key) {
    navigator.clipboard.writeText(key).then(() => {
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
        setTimeout(() => {
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy';
        }, 2000);
    });
}
</script>

<?php require_once 'templates/footer.php'; ?>