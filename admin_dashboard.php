<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="public/assets/style.css">
</head>
<body>
    <div class="container" style="margin-top: 50px;">
        <p>TODO.</p>
        <br>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>
