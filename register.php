<?php
session_start();
require_once 'config/config.php';
require_once 'classes/User.php';

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$msg = '';
$error = '';
if(isset($_POST['register'])) {
    $user = new User();
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($username) < 3 || strlen($username) > 20) {
        $error = "Username harus antara 3 hingga 20 karakter.";
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
        $error = "Username hanya boleh terdiri dari huruf dan angka (tanpa spasi).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal harus 8 karakter.";
    } else {
        if($user->register($username, $email, $password)) {
            $msg = "Daftar akun berhasil! Ayoo login.";
        } else {
            $error = "Username atau email sudah terdaftar!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Seedem</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body class="bg-gray">
    <div class="auth-card">
        <h2>Buat Akun</h2>
        <p class="auth-subtitle">Bergabung dengan Seedem sekarang</p>
        <?php if($msg): ?>
            <div class="alert success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="3–20 karakter, huruf dan angka" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="nama@email.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" required>
            </div>
            <button type="submit" name="register" class="btn btn-full">Buat Akun</button>
        </form>
        <hr class="divider">
        <p class="text-muted" style="text-align:center">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
</body>
</html>
