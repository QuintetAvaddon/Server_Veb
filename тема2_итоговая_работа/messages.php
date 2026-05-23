<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();

$where = "s.user_id = ?";
$params = [$_SESSION['user_id']];

if (isset($_GET['hashtag'])) {
    $where .= " AND h.name = ?";
    $params[] = $_GET['hashtag'];
}

if (isset($_GET['field'])) {
    $where .= " AND f.id = ?";
    $params[] = $_GET['field'];
}

$stmt = $db->prepare("
    SELECT s.*, h.name as hashtag_name, c.name as channel_name, c.is_liked,
           GROUP_CONCAT(f.name SEPARATOR ', ') as fields
    FROM SMS s
    LEFT JOIN Hashtags h ON s.hashtag_id = h.id
    LEFT JOIN Channels c ON s.channel_id = c.id
    LEFT JOIN Hashtag_Fields hf ON h.id = hf.id_hashtag
    LEFT JOIN Fields f ON hf.id_field = f.id
    WHERE $where
    GROUP BY s.id
    ORDER BY s.created_at DESC
");
$stmt->execute($params);
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои сообщения</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .message-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .message-header { display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
        .tag { background: #667eea; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; }
        .field-tag { background: #27ae60; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; }
        .channel { background: #f39c12; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; }
        .saved { background: #e74c3c; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; }
        .liked { border: 2px solid #f39c12; }
        .message-text { color: #555; line-height: 1.6; }
        .empty { text-align: center; color: #888; padding: 40px; }
        .nav { margin-top: 20px; }
        .nav a { color: #667eea; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Мои сообщения</h1>
            <a href="index.php" style="color: white; text-decoration: none;">← Назад</a>
        </div>
        
        <?php if (empty($messages)): ?>
            <div class="empty">Нет сообщений</div>
        <?php else: ?>
            <?php foreach ($messages as $msg): ?>
                <div class="message-card <?php echo $msg['is_liked'] ? 'liked' : ''; ?>">
                    <div class="message-header">
                        <?php if ($msg['hashtag_name']): ?>
                            <span class="tag">#<?php echo htmlspecialchars($msg['hashtag_name']); ?></span>
                        <?php endif; ?>
                        <?php if ($msg['fields']): ?>
                            <span class="field-tag"><?php echo htmlspecialchars($msg['fields']); ?></span>
                        <?php endif; ?>
                        <?php if ($msg['channel_name']): ?>
                            <span class="channel"><?php echo htmlspecialchars($msg['channel_name']); ?></span>
                        <?php endif; ?>
                        <?php if ($msg['is_saved']): ?>
                            <span class="saved">Приватное</span>
                        <?php endif; ?>
                    </div>
                    <div class="message-text"><?php echo nl2br(htmlspecialchars($msg['description'])); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>