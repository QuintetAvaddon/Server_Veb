<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_sms'])) {
    header('Location: index.php');
    exit;
}

$db = getDB();
$hashtags = $db->query("SELECT h.*, GROUP_CONCAT(f.name SEPARATOR ', ') as fields 
    FROM Hashtags h 
    LEFT JOIN Hashtag_Fields hf ON h.id = hf.id_hashtag 
    LEFT JOIN Fields f ON hf.id_field = f.id 
    GROUP BY h.id 
    ORDER BY h.name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['existing_hashtag'])) {
        $hashtagId = $_POST['existing_hashtag'];
    } elseif (isset($_POST['new_hashtag'])) {
        $name = strtolower(trim($_POST['new_hashtag']));
        if (empty($name)) {
            $error = 'Введите название хэштега';
        } else {
            $stmt = $db->prepare("SELECT id FROM Hashtags WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) {
                $error = 'Такой хэштег уже существует';
            } else {
                $stmt = $db->prepare("INSERT INTO Hashtags (name) VALUES (?)");
                $stmt->execute([$name]);
                $hashtagId = $db->lastInsertId();
            }
        }
    }
    
    if (!isset($error) && isset($hashtagId)) {
        $pending = $_SESSION['pending_sms'];
        $stmt = $db->prepare("INSERT INTO SMS (hashtag_id, user_id, channel_id, description, is_saved) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $hashtagId,
            $_SESSION['user_id'],
            $pending['channel_id'],
            $pending['description'],
            $pending['is_saved']
        ]);
        
        unset($_SESSION['pending_sms']);
        header('Location: index.php?success=1');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Выбор хэштега</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .container { max-width: 700px; margin: 40px auto; padding: 0 20px; }
        .header { background: #e74c3c; color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { margin-bottom: 15px; color: #667eea; font-size: 1.3rem; }
        .hashtag-list { display: flex; flex-direction: column; gap: 10px; }
        .hashtag-item { display: flex; align-items: center; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
        .hashtag-item:hover { border-color: #667eea; background: #f8f9ff; }
        .hashtag-item input { margin-right: 12px; width: 18px; height: 18px; accent-color: #667eea; }
        .hashtag-name { font-weight: 600; color: #667eea; font-size: 1.1rem; }
        .hashtag-fields { color: #888; font-size: 0.9rem; margin-left: auto; }
        .form-group { margin-bottom: 15px; }
        input[type="text"] { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
        input[type="text"]:focus { outline: none; border-color: #667eea; }
        .btn { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 12px 25px; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .btn-secondary { background: #95a5a6; }
        .or-divider { text-align: center; margin: 25px 0; color: #888; font-weight: 600; position: relative; }
        .or-divider::before, .or-divider::after { content: ''; position: absolute; top: 50%; width: 40%; height: 1px; background: #e0e0e0; }
        .or-divider::before { left: 0; }
        .or-divider::after { right: 0; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .pending-msg { background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Сообщение без хэштега</h2>
            <p>Выберите существующий хэштег или создайте новый</p>
        </div>
        
        <div class="pending-msg">
            <strong>Ваше сообщение:</strong><br>
            <?php echo htmlspecialchars($_SESSION['pending_sms']['description']); ?>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="card">
                <h2>Существующие хэштеги</h2>
                <div class="hashtag-list">
                    <?php foreach ($hashtags as $tag): ?>
                        <label class="hashtag-item">
                            <input type="radio" name="existing_hashtag" value="<?php echo $tag['id']; ?>">
                            <span class="hashtag-name">#<?php echo htmlspecialchars($tag['name']); ?></span>
                            <?php if ($tag['fields']): ?>
                                <span class="hashtag-fields"><?php echo htmlspecialchars($tag['fields']); ?></span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="or-divider">ИЛИ</div>
            
            <div class="card">
                <h2>Новый хэштег</h2>
                <div class="form-group">
                    <input type="text" name="new_hashtag" placeholder="Введите название нового хэштега (например: cake)">
                </div>
            </div>
            
            <button type="submit" class="btn">Сохранить сообщение</button>
            <a href="index.php" class="btn btn-secondary" style="text-decoration:none; margin-left:10px;">Отмена</a>
        </form>
    </div>
</body>
</html>