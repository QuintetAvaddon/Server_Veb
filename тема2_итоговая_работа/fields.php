<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$fields = $db->query("SELECT f.*, COUNT(hf.id_hashtag) as hashtag_count
    FROM Fields f
    LEFT JOIN Hashtag_Fields hf ON f.id = hf.id_field
    GROUP BY f.id
    ORDER BY f.name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Области знаний</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .field-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .field-name { font-size: 1.2rem; color: #667eea; font-weight: 600; }
        .field-desc { color: #666; margin-top: 5px; }
        .field-count { color: #27ae60; margin-top: 10px; font-weight: 500; }
        .nav a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Области знаний</h1>
            <a href="index.php" style="color: white; text-decoration: none;">← Назад</a>
        </div>
        
        <?php foreach ($fields as $field): ?>
            <div class="field-card">
                <div class="field-name"><?php echo htmlspecialchars($field['name']); ?></div>
                <?php if ($field['description']): ?>
                    <div class="field-desc"><?php echo htmlspecialchars($field['description']); ?></div>
                <?php endif; ?>
                <div class="field-count">Хэштегов: <?php echo $field['hashtag_count']; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>