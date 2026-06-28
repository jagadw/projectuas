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
$keys    = $library->getUserKeys($userId);

$pageTitle = 'Library Key';
require_once 'templates/header.php';
?>

<h2>Library Key</h2>

<?php if(empty($keys)): ?>
    <div class="empty-state">
        <p>Belum ada game key. Beli game terlebih dahulu!</p>
        <a href="index.php" class="btn btn-secondary">Jelajahi Game</a>
    </div>
<?php else: ?>
    <div class="library-grid">
        <?php foreach($keys as $key): ?>
        <div class="library-card">
            <div class="library-card-img">
                <?php if(!empty($key['image'])): ?>
                    <img src="public/assets/<?php echo htmlspecialchars($key['image']); ?>" alt="<?php echo htmlspecialchars($key['title']); ?>">
                <?php else: ?>
                    <div class="library-card-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M7 8h2M9 7v2M15 8h2M16 7v2"/></svg>
                    </div>
                <?php endif; ?>
            </div>
            <div class="library-card-body">
                <div class="library-card-title"><?php echo htmlspecialchars($key['title']); ?></div>
                <div class="library-card-meta">
                    <?php if(!empty($key['platform'])): ?>
                        <span class="badge badge-platform"><?php echo htmlspecialchars($key['platform']); ?></span>
                    <?php endif; ?>
                    <span class="library-card-date"><?php echo date('d M Y', strtotime($key['created_at'])); ?></span>
                </div>
                <div class="library-key-row">
                    <code class="library-key-code"><?php echo htmlspecialchars($key['key_code']); ?></code>
                    <button class="btn btn-secondary btn-sm" onclick="copyKey(this, '<?php echo htmlspecialchars($key['key_code']); ?>')">Salin</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
function copyKey(btn, key) {
    navigator.clipboard.writeText(key).then(() => {
        btn.textContent = 'Disalin!';
        setTimeout(() => btn.textContent = 'Salin', 2000);
    });
}
</script>

<?php require_once 'templates/footer.php'; ?>