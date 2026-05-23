<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    $description = trim($_POST['description']);
    $channelId = !empty($_POST['channel_id']) ? $_POST['channel_id'] : null;
    $isSaved = isset($_POST['is_saved']) ? 1 : 0;
    
    preg_match_all('/#(\w+)/u', $description, $matches);
    $hashtags = $matches[1];
    
    if (empty($hashtags)) {
        $_SESSION['pending_sms'] = [
            'description' => $description,
            'channel_id' => $channelId,
            'is_saved' => $isSaved
        ];
        header('Location: hashtag_select.php');
        exit;
    }
    
    $hashtagName = strtolower($hashtags[0]);
    $stmt = $db->prepare("SELECT id FROM Hashtags WHERE name = ?");
    $stmt->execute([$hashtagName]);
    $hashtag = $stmt->fetch();
    
    if (!$hashtag) {
        $_SESSION['pending_hashtag'] = $hashtagName;
        $_SESSION['pending_sms'] = [
            'description' => $description,
            'channel_id' => $channelId,
            'is_saved' => $isSaved
        ];
        header('Location: hashtag_create.php?new=1');
        exit;
    }
    
    $hashtagId = $hashtag['id'];
    
    $stmt = $db->prepare("INSERT INTO SMS (hashtag_id, user_id, channel_id, description, is_saved) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$hashtagId, $_SESSION['user_id'], $channelId, $description, $isSaved]);
    
    $success = 'Сообщение добавлено!';
}

$channels = $db->query("SELECT * FROM Channels ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>#сортер — Добавление сообщения</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; }
        .header h1 { font-size: 2rem; }
        .form-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        textarea, select { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; transition: border-color 0.3s; }
        textarea { min-height: 120px; resize: vertical; }
        textarea:focus, select:focus { outline: none; border-color: #667eea; }
        .checkbox-wrapper { display: flex; align-items: center; gap: 10px; }
        input[type="checkbox"] { width: 20px; height: 20px; accent-color: #667eea; }
        .btn { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 14px 30px; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: transform 0.2s; }
        .btn:hover { transform: translateY(-2px); }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .hint { color: #888; font-size: 0.9rem; margin-top: 5px; }
        .nav { margin-top: 20px; display: flex; gap: 15px; }
        .nav a { color: #667eea; text-decoration: none; font-weight: 500; }
        .nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>#сортер</h1>
            <p>Сортировка сообщений по областям знаний</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="form-card">
            <form method="post">
                <div class="form-group">
                    <label for="description">Сообщение</label>
                    <textarea name="description" id="description" placeholder="Введите сообщение с хэштегом, например: Рецепт торта #cake"></textarea>
                    <p class="hint">Если не указать #, вы перейдёте к выбору или созданию хэштега</p>
                </div>
                
                <div class="form-group">
                    <label for="channel_id">Канал</label>
                    <select name="channel_id" id="channel_id">
                        <option value="">— Без канала —</option>
                        <?php foreach ($channels as $ch): ?>
                            <option value="<?php echo $ch['id']; ?>"><?php echo htmlspecialchars($ch['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="is_saved" id="is_saved" value="1">
                        <label for="is_saved" style="margin: 0;">Приватное сообщение (не видно другим)</label>
                    </div>
                </div>
                
                <button type="submit" class="btn">Добавить сообщение</button>
            </form>
        </div>
        
        <div class="nav">
            <a href="hashtags.php">Все хэштеги</a>
            <a href="fields.php">Области знаний</a>
            <a href="messages.php">Мои сообщения</a>
            <a href="logout.php">Выйти</a>
        </div>
    </div>
</body>
</html>