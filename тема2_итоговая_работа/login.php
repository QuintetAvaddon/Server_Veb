<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, password FROM Users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    }
    $error = 'Неверный email или пароль';
}
?>

<form method="post">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Пароль" required>
    <button type="submit">Войти</button>
</form>