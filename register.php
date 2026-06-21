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
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if($user->register($username, $email, $password)) {
        $msg = "Daftar akun berhasil! Ayoo login.";
    } else {
        $error = "Username sudah digunakan, cari nama lain!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - MarketGame</title>
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body class="bg-gray">
    <div class="auth-card">
        <h2>Daftar Akun Baru</h2>
        <?php if($msg): ?>
            <div class="alert success"><?php echo $msg; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="register" class="btn">Daftar</button>
        </form>
        <p class="mt-15">Sudah punya akun? <a href="login.php">Login disini</a></p>
    </div>
</body>
</html>
