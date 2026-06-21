<?php
session_start();
require_once 'config/config.php';
require_once 'classes/User.php';

if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$error = '';
if(isset($_POST['login'])) {
    $user = new User();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $data = $user->login($username, $password);

    if($data) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        if($data['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Username atau password salah";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - MarketGame</title>
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body class="bg-gray">
    <div class="auth-card">
        <h2>Login MarketGame</h2>
        <?php if($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn">Login</button>
        </form>
        <p class="mt-15">Belum punya akun? <a href="register.php">Daftar disini</a></p>
    </div>
</body>
</html>
