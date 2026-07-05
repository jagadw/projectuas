<?php
if(session_status() === PHP_SESSION_NONE) session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' Seedem' : 'Seedem'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body>

<header class="topbar">
    <div class="topbar-inner">
        <div class="topbar-left">
            <a href="index.php" class="logo">See<b>dem</b></a>
            <nav class="nav-links">
                <a href="index.php" <?php echo $currentPage == 'index.php' ? 'class="active"' : ''; ?>>Home</a>
                <a href="library.php" <?php echo $currentPage == 'library.php' ? 'class="active"' : ''; ?>>Library Key</a>
                <a href="history.php" <?php echo $currentPage == 'history.php' ? 'class="active"' : ''; ?>>History</a>
            </nav>
        </div>
        <div class="topbar-right">
            <a href="cart.php" class="nav-icon-btn" title="Keranjang">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            </a>
            <a href="favorite.php" class="nav-icon-btn" title="Favorit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </a>
            <a href="#" class="nav-icon-btn" title="Bantuan">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </a>
            <div class="nav-divider"></div>
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="nav-icon-btn hide-on-mobile" title="Keluar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </a>
            <button class="hamburger-btn" id="hamburgerBtn" title="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>
</header>

<nav class="mobile-nav" id="mobileNav">
    <div class="mobile-nav-header">
        <span class="user-name-mobile">Halo, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <button class="close-btn" id="closeMenuBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <div class="mobile-nav-links">
        <a href="index.php" <?php echo $currentPage == 'index.php' ? 'class="active"' : ''; ?>>Home</a>
        <a href="library.php" <?php echo $currentPage == 'library.php' ? 'class="active"' : ''; ?>>Library Key</a>
        <a href="history.php" <?php echo $currentPage == 'history.php' ? 'class="active"' : ''; ?>>History</a>
        <div class="nav-divider-horizontal"></div>
        <a href="cart.php">Keranjang</a>
        <a href="favorite.php">Favorit</a>
        <a href="#">Bantuan</a>
        <div class="nav-divider-horizontal"></div>
        <a href="logout.php" style="color: var(--red);">Keluar</a>
    </div>
</nav>
<div class="mobile-overlay" id="mobileOverlay"></div>

<div class="container">
