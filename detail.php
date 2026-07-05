<?php
require_once 'config/config.php';
require_once 'templates/header.php';
require_once 'classes/Game.php';
require_once 'classes/Cart.php';
require_once 'classes/Favorite.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$gameObj = new Game();
$games = $gameObj->getAllGames();
$game = null;

foreach($games as $g) {
    if($g['id'] == $_GET['id']) {
        $game = $g;
        break;
    }
}

if(!$game) {
    header("Location: index.php");
    exit;
}

$msg = '';

if(isset($_POST['add_cart'])) {
    $cart = new Cart();
    $cart->addToCart($_SESSION['user_id'], $_POST['game_id']);
    $msg = "Game berhasil ditambahkan ke keranjang!";
}

if(isset($_POST['add_fav'])) {
    $fav = new Favorite();
    $fav->addToFavorite($_SESSION['user_id'], $_POST['game_id']);
    $msg = "Game berhasil disimpan ke favorit!";
}

$imgSrc = !empty($game['image']) ? "public/uploads/" . htmlspecialchars($game['image']) : "https://picsum.photos/seed/" . ($game['id'] + 50) . "/600/400";
$categories = ['Action', 'RPG', 'Strategy', 'Sports', 'Racing'];
$gameCategory = $categories[($game['id'] - 1) % count($categories)];
?>

<a href="index.php" class="back-link">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><polyline points="12 19 5 12 12 5"/></svg>
    Kembali ke Daftar Game
</a>

<?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>

<div class="detail-wrapper">
    <div class="detail-image">
        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($game['title']); ?>" onerror="this.src='https://picsum.photos/seed/<?php echo ($game['id'] + 50); ?>/600/400'">
    </div>

    <div class="detail-info">
        <div class="detail-badges">
            <span class="badge"><?php echo $gameCategory; ?></span>
            <?php if(!empty($game['platform'])): ?>
                <span class="badge badge-platform"><?php echo htmlspecialchars($game['platform']); ?></span>
            <?php endif; ?>
        </div>

        <h1><?php echo htmlspecialchars($game['title']); ?></h1>

        <p class="detail-desc"><?php echo htmlspecialchars($game['description']); ?></p>

        <p class="detail-price">
            Rp <?php echo number_format($game['price'], 0, ',', '.'); ?>
        </p>

        <form method="POST" class="detail-actions">
            <input type="hidden" name="game_id" value="<?php echo $game['id']; ?>">
            <button type="submit" name="add_cart" class="btn">+ Tambah ke Keranjang</button>
            <button type="submit" name="add_fav" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                Simpan Favorit
            </button>
        </form>

        <div class="detail-meta">
            <?php if(!empty($game['platform'])): ?>
            <div class="detail-meta-row">
                <span>Platform</span>
                <span><?php echo htmlspecialchars($game['platform']); ?></span>
            </div>
            <?php endif; ?>
            <div class="detail-meta-row">
                <span>Kategori</span>
                <span><?php echo $gameCategory; ?></span>
            </div>
        </div>
    </div>
</div>

<?php 
require_once 'classes/Feedback.php';
$feedbackObj = new Feedback();
$feedbacks = $feedbackObj->getByGameId($game['id']);

$totalReviews = count($feedbacks);
$avgRating = 0;
$ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

if ($totalReviews > 0) {
    $sum = 0;
    foreach ($feedbacks as $fb) {
        $sum += $fb['rating'];
        $ratingCounts[$fb['rating']]++;
    }
    $avgRating = round($sum / $totalReviews, 1);
}
?>

<div class="feedback-section-v2">
    <h2 class="feedback-title">Ulasan Pengguna</h2>
    
    <?php if (empty($feedbacks)): ?>
        <div class="no-feedback-box">
            <p class="no-feedback">Belum ada ulasan untuk game ini.</p>
        </div>
    <?php else: ?>
        <div class="feedback-container">
            <div class="feedback-stats-box">
                <div class="stats-left">
                    <div class="avg-rating-big"><?php echo number_format($avgRating, 1, '.', ''); ?></div>
                    <div class="avg-stars">
                        <?php 
                        $fullStars = floor($avgRating);
                        for($i=1; $i<=5; $i++) {
                            if($i <= $fullStars) {
                                echo '<svg class="fb-star-filled" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
                            } else {
                                echo '<svg class="fb-star" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
                            }
                        }
                        ?>
                    </div>
                    <div class="review-count">Dari <?php echo $totalReviews; ?> ulasan</div>
                </div>
                <div class="stats-right">
                    <?php for($i=5; $i>=1; $i--): 
                        $pct = $totalReviews > 0 ? ($ratingCounts[$i] / $totalReviews) * 100 : 0;
                    ?>
                        <div class="rating-bar-row">
                            <span class="star-label"><?php echo $i; ?> <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: <?php echo $pct; ?>%"></div>
                            </div>
                            <span class="count-label"><?php echo $ratingCounts[$i]; ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="feedback-list-box">
                <div class="feedback-list-scroll">
                    <?php foreach ($feedbacks as $fb): ?>
                        <div class="feedback-item-v2">
                            <div class="fb-avatar" style="display:flex; align-items:center; justify-content:center; color: var(--text-tertiary);">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </div>
                            <div class="fb-content">
                                <div class="fb-header-v2">
                                    <span class="fb-username"><?php echo htmlspecialchars($fb['username']); ?></span>
                                    <span class="fb-date"><?php echo date('d M Y', strtotime($fb['created_at'])); ?></span>
                                </div>
                                <div class="fb-stars-v2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <svg class="fb-star <?php echo $i <= $fb['rating'] ? 'fb-star-filled' : ''; ?>"
                                             xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                             fill="<?php echo $i <= $fb['rating'] ? 'currentColor' : 'none'; ?>"
                                             stroke="currentColor" stroke-width="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <?php if (!empty($fb['comment'])): ?>
                                    <p class="fb-text"><?php echo htmlspecialchars($fb['comment']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="fb-view-all">
                    <a href="#">Lihat semua ulasan (<?php echo $totalReviews; ?>)</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
