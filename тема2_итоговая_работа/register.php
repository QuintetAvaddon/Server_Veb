<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    
    if ($_POST['password'] !== $_POST['password_confirm']) {
        $error = 'Пароли не совпадают';
    } else {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO Users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['name'], $_POST['email'], $hash]);
            header('Location: login.php?registered=1');
            exit;
        } catch (PDOException $e) {
            $error = 'Email уже занят';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .register-box { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { color: #667eea; margin-bottom: 25px; text-align: center; }
        .form-group { margin-bottom: 20px; }
        input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
        input:focus { outline: none; border-color: #667eea; }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .hint { text-align: center; margin-top: 15px; color: #888; }
        .hint a { color: #667eea; }
    </style>
</head>
<body>
    <div class="register-box">
        <h1>Регистрация</h1>
        <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group"><input type="text" name="name" placeholder="Имя" required></div>
            <div class="form-group"><input type="email" name="email" placeholder="Email" required></div>
            <div class="form-group"><input type="password" name="password" placeholder="Пароль" required></div>
            <div class="form-group"><input type="password" name="password_confirm" placeholder="Повторите пароль" required></div>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p class="hint">Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</body>
</html>