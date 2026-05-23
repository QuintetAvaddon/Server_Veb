<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$hashtags = $db->query("SELECT h.*, GROUP_CONCAT(f.name SEPARATOR ', ') as fields, COUNT(s.id) as msg_count
    FROM Hashtags h
    LEFT JOIN Hashtag_Fields hf ON h.id = hf.id_hashtag
    LEFT JOIN Fields f ON hf.id_field = f.id
    LEFT JOIN SMS s ON h.id = s.hashtag_id
    GROUP BY h.id
    ORDER BY h.name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Все хэштеги</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .hashtag-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .hashtag-name { font-size: 1.3rem; color: #667eea; font-weight: 600; }
        .hashtag-fields { color: #27ae60; font-size: 0.9rem; margin-top: 5px; }
        .hashtag-count { background: #667eea; color: white; padding: 5px 15px; border-radius: 20px; }
        .nav a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Все хэштеги</h1>
            <a href="index.php" style="color: white; text-decoration: none;">← Назад</a>
        </div>
        
        <?php foreach ($hashtags as $tag): ?>
            <div class="hashtag-card">
                <div>
                    <div class="hashtag-name">#<?php echo htmlspecialchars($tag['name']); ?></div>
                    <?php if ($tag['fields']): ?>
                        <div class="hashtag-fields"><?php echo htmlspecialchars($tag['fields']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="hashtag-count"><?php echo $tag['msg_count']; ?> сообщений</div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>