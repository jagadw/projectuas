<?php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
?>
<script>
    alert("Anda harus login sebagai admin untuk mengakses halaman ini.");
    window.location.href = "../login.php";
</script>
<?php
    exit;
}

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function rupiah($value)
{
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
}

$currentPage = basename($_SERVER['PHP_SELF']);
$notif = $_SESSION['admin_notif'] ?? null;
unset($_SESSION['admin_notif']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'Admin'); ?> Seedem</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../public/assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="topbar-inner">
        <div class="topbar-left">
            <a href="admin_dashboard.php" class="logo">See<b>dem</b> Admin</a>
            <nav class="nav-links admin-top-links">
                <a href="admin_dashboard.php" <?php echo $currentPage === 'admin_dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a>
                <a href="games.php" <?php echo $currentPage === 'games.php' ? 'class="active"' : ''; ?>>Game & Genre</a>
                <a href="keys.php" <?php echo $currentPage === 'keys.php' ? 'class="active"' : ''; ?>>Restock Key</a>
                <a href="promos.php" <?php echo $currentPage === 'promos.php' ? 'class="active"' : ''; ?>>Promo Code</a>
                <a href="transactions.php" <?php echo $currentPage === 'transactions.php' ? 'class="active"' : ''; ?>>Transaksi</a>
                <a href="tickets.php" <?php echo $currentPage === 'tickets.php' ? 'class="active"' : ''; ?>>Helpdesk</a>
                <a href="reviews.php" <?php echo $currentPage === 'reviews.php' ? 'class="active"' : ''; ?>>Ulasan</a>
            </nav>
        </div>
        <div class="topbar-right">
            <span class="user-name"><?php echo e($_SESSION['username'] ?? 'Admin'); ?></span>
            <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</header>

<div class="admin-shell">
    <aside class="admin-sidebar">
        <a href="admin_dashboard.php" <?php echo $currentPage === 'admin_dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a>
        <a href="games.php" <?php echo $currentPage === 'games.php' ? 'class="active"' : ''; ?>>Game & Genre</a>
        <a href="keys.php" <?php echo $currentPage === 'keys.php' ? 'class="active"' : ''; ?>>Restock Key</a>
        <a href="promos.php" <?php echo $currentPage === 'promos.php' ? 'class="active"' : ''; ?>>Promo Code</a>
        <a href="transactions.php" <?php echo $currentPage === 'transactions.php' ? 'class="active"' : ''; ?>>Transaksi</a>
        <a href="tickets.php" <?php echo $currentPage === 'tickets.php' ? 'class="active"' : ''; ?>>Helpdesk</a>
        <a href="reviews.php" <?php echo $currentPage === 'reviews.php' ? 'class="active"' : ''; ?>>Ulasan</a>
    </aside>
    <main class="admin-main">
        <div class="page-header">
            <h2><?php echo e($pageTitle ?? 'Admin'); ?></h2>
            <?php if (!empty($pageInfo)): ?>
                <span class="result-count"><?php echo e($pageInfo); ?></span>
            <?php endif; ?>
        </div>
        <?php if ($notif): ?>
            <div class="alert <?php echo $notif['type'] === 'success' ? 'success' : ''; ?>"><?php echo e($notif['message']); ?></div>
        <?php endif; ?>
