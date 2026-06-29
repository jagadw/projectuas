<?php
session_start();
require_once 'config/config.php';
require_once 'classes/User.php';

if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') {
        header("Location: admin/admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
    $remaining_time = $_SESSION['lockout_time'] - time();
    $error = "Terlalu banyak percobaan gagal. Silakan coba lagi dalam " . $remaining_time . " detik.";
}

if(isset($_POST['login']) && empty($error)) {
    $user = new User();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $data = $user->login($username, $password);

    if($data) {
        $_SESSION['login_attempts'] = 0; 
        unset($_SESSION['lockout_time']);

        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if($data['role'] == 'admin') {
            header("Location: admin/admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] >= 5) {
            $_SESSION['lockout_time'] = time() + 30;
            $error = "Terlalu banyak percobaan gagal. Silakan coba lagi dalam 30 detik.";
        } else {
            $error = "Username atau password salah. Percobaan tersisa: " . (5 - $_SESSION['login_attempts']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body class="bg-gray">
    <div class="auth-card">
        <h2>Masuk</h2>
        <p class="auth-subtitle">Selamat datang kembali di Seedem</p>
        <?php if($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn btn-full">Masuk</button>
        </form>
        <hr class="divider">
        <p class="text-muted" style="text-align:center">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
    </div>
</body>
</html>
